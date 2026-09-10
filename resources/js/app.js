// ============================================================
// ParkEase — simple parking ticketing front-end (vanilla JS)
// ============================================================
'use strict';

const $ = (sel, root = document) => root.querySelector(sel);
const $$ = (sel, root = document) => [...root.querySelectorAll(sel)];

const pad = (n) => String(n).padStart(2, '0');
const normPlate = (s) => `${s ?? ''}`.trim().toUpperCase().replace(/\s+/g, ' ');

const num = (v) => {
  if (v === null || v === undefined || v === '') return 0;
  if (typeof v === 'number') return Number.isFinite(v) ? v : 0;
  const n = parseFloat(`${v}`.replace(',', '.'));
  return Number.isFinite(n) ? n : 0;
};

const fmtInt = (n) => Math.round(num(n)).toLocaleString('id-ID');
const fmtMoney = (v) => `Rp ${fmtInt(v)}`;

// The API serializes datetimes as ISO (UTC) — parse them as-is.
const parseDb = (s) => {
  if (!s) return null;
  const t = `${s}`.trim();
  const d = t.includes('T') ? new Date(t) : new Date(t.replace(' ', 'T') + 'Z');
  return Number.isNaN(d.getTime()) ? null : d;
};

const fmtDate = (d) => d ? d.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' }) : '—';
const fmtTime = (d) => d ? d.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) : '—';
const fmtDateTime = (d) => d ? `${fmtDate(d)} · ${fmtTime(d)}` : '—';

// Format a Date as "YYYY-MM-DD HH:MM:SS" in UTC (server stores UTC-naive).
const toDb = (d) =>
  `${d.getUTCFullYear()}-${pad(d.getUTCMonth() + 1)}-${pad(d.getUTCDate())} ` +
  `${pad(d.getUTCHours())}:${pad(d.getUTCMinutes())}:${pad(d.getUTCSeconds())}`;

const esc = (s) =>
  `${s ?? ''}`
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#39;');

const TYPE_LABEL = { motor: 'Motor', mobil: 'Mobil', lainnya: 'Lainnya' };
const typeLabel = (k) => TYPE_LABEL[k] || k || '—';
const ticketNo = (id) => `P-${String(id).padStart(6, '0')}`;

const durLabel = (hours) => {
  const h = num(hours);
  if (h <= 0) return '—';
  return `${fmtInt(h)} jam`;
};

const hmss = (ms) => {
  const total = Math.max(0, Math.floor(ms / 1000));
  return `${pad(Math.floor(total / 3600))}:${pad(Math.floor((total % 3600) / 60))}:${pad(total % 60)}`;
};

// ------------------------------------------------------------
// fetch helper
// ------------------------------------------------------------
class ApiError extends Error {
  constructor(message, status, errors) {
    super(message);
    this.status = status;
    this.errors = errors;
  }
}

async function api(path, opts = {}) {
  const init = { headers: { Accept: 'application/json' }, ...opts };

  // Serialize plain-object payloads as JSON. Without this, fetch would send
  // the literal string "[object Object]", the API would see an empty request
  // and every "required" validation rule would fail.
  if (init.body && typeof init.body === 'object' && !(init.body instanceof FormData)) {
    init.body = JSON.stringify(init.body);
    init.headers['Content-Type'] = 'application/json';
  }

  let res;
  try {
    res = await fetch(path, init);
  } catch (e) {
    throw new ApiError('Tidak dapat terhubung ke server. Pastikan aplikasi sudah berjalan.', 0, null);
  }

  let json = null;
  try {
    json = await res.json();
  } catch (e) { /* empty body */ }

  if (!res.ok) {
    let msg = (json && json.message) || 'Terjadi kesalahan.';
    if (json && json.errors) {
      msg = Object.values(json.errors).flat().filter(Boolean).join(' · ');
    }
    throw new ApiError(msg, res.status, json && json.errors);
  }

  return json;
}

// ------------------------------------------------------------
// toast notifications
// ------------------------------------------------------------
function toast(message, kind = 'ok') {
  const wrap = $('#toast-wrap');
  if (!wrap) return;
  const box = document.createElement('div');
  const tone = kind === 'err'
    ? 'bg-red-50 border-red-200 text-red-700'
    : 'bg-emerald-50 border-emerald-200 text-emerald-800';
  box.className = `toast-box max-w-xs rounded-xl border ${tone} px-4 py-3 text-sm shadow-lg rise`;
  box.textContent = message;
  wrap.appendChild(box);
  setTimeout(() => {
    box.style.transition = 'opacity 300ms ease';
    box.style.opacity = '0';
    setTimeout(() => box.remove(), 350);
  }, 3400);
}

// ------------------------------------------------------------
// small widgets
// ------------------------------------------------------------
function startClock() {
  const el = $('#nav-clock');
  if (!el) return;
  const tick = () => {
    const d = new Date();
    el.textContent = d.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
  };
  tick();
  setInterval(tick, 1000);
}

function countUp(el, target, formatter = fmtInt) {
  if (!el) return;
  const t0 = performance.now();
  const dur = 850;
  const step = (t) => {
    const p = Math.min(1, (t - t0) / dur);
    const eased = 1 - Math.pow(1 - p, 3);
    el.textContent = formatter(target * eased);
    if (p < 1) requestAnimationFrame(step);
  };
  requestAnimationFrame(step);
}

function setBar(el, pct) {
  if (!el) return;
  el.style.transition = 'none';
  el.style.width = '0%';
  void el.offsetWidth; // reflow
  el.style.transition = 'width 800ms cubic-bezier(0.16, 1, 0.3, 1)';
  el.style.width = `${Math.max(0, Math.min(100, pct))}%`;
}

const statusBadge = (st) =>
  st === 'masuk'
    ? '<span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-800 px-2.5 py-0.5 text-[11px] font-semibold whitespace-nowrap"><span class="h-1.5 w-1.5 rounded-full bg-emerald-500 live-dot"></span>AKTIF</span>'
    : '<span class="inline-flex items-center rounded-full bg-primary-100 text-primary-700 px-2.5 py-0.5 text-[11px] font-semibold whitespace-nowrap">SELESAI</span>';

// ------------------------------------------------------------
// page helpers
// ------------------------------------------------------------
const setBusy = (btn, busy, label = 'Menyimpan…') => {
  if (!btn) return;
  if (busy) {
    btn.dataset.original = btn.textContent;
    btn.disabled = true;
    btn.textContent = label;
  } else {
    btn.disabled = false;
    btn.textContent = btn.dataset.original || '';
  }
};

const setPanel = (id, show) => {
  const el = $(id);
  if (!el) return null;
  if (show) {
    el.classList.remove('hidden');
    el.classList.remove('pop');
    void el.offsetWidth;
    el.classList.add('pop');
  } else {
    el.classList.add('hidden');
  }
  return el;
};

// ============================================================
// HOME — live stats + quick actions
// ============================================================
async function beranda() {
  const heroClock = $('#hero-clock');
  if (heroClock) {
    const tick = () => {
      const d = new Date();
      heroClock.textContent =
        d.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long' }) +
        ' · ' + d.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
    };
    tick();
    setInterval(tick, 1000);
  }

  let areas = [];
  let tx = [];
  try {
    areas = (await api('/api/areas')).data || [];
    tx = (await api('/api/transaksis')).data || [];
  } catch (e) {
    toast(e.message, 'err');
  }

  const spots = areas.reduce((s, a) => s + num(a.kapasitas), 0);
  const occupied = areas.reduce((s, a) => s + num(a.terisi), 0);
  const active = tx.filter((t) => t.status === 'masuk').length;
  const revenue = tx
    .filter((t) => t.status === 'keluar' && parseDb(t.waktu_keluar))
    .filter((t) => {
      const d = parseDb(t.waktu_keluar);
      const today = new Date();
      return d.getFullYear() === today.getFullYear() &&
             d.getMonth() === today.getMonth() &&
             d.getDate() === today.getDate();
    })
    .reduce((s, t) => s + num(t.biaya_total), 0);

  $$('.stat-skeleton').forEach((el) => el.classList.add('hidden'));
  countUp($('#stat-spots'), spots);
  countUp($('#stat-occupied'), occupied);
  countUp($('#stat-active'), active);
  countUp($('#stat-revenue'), revenue, fmtMoney);
// compact area preview
  const wrap = $('#area-mini');
  if (wrap && areas.length) {
    wrap.innerHTML = areas.map((a) => {
      const cap = num(a.kapasitas);
      const use = num(a.terisi);
      const pct = cap ? (use / cap) * 100 : 0;
      const tone = use >= cap ? 'bg-red-400' : 'bg-primary-500';
      return `
        <div class="rise" style="animation-delay:0.05s">
          <div class="flex items-center justify-between text-sm">
            <span class="font-medium text-slate-700">${esc(a.nama_area)}</span>
            <span class="text-xs text-slate-500">${fmtInt(use)} / ${fmtInt(cap)}</span>
          </div>
          <div class="mt-1.5 h-2 rounded-full bg-primary-100 overflow-hidden">
            <div class="${tone} h-full rounded-full" data-bar="${pct}"></div>
          </div>
        </div>`;
    }).join('');
    $$('#area-mini [data-bar]').forEach((b) => setBar(b, num(b.dataset.bar)));
  }
}

// ============================================================
// CHECK IN — issue a parking ticket
// ============================================================
async function masuk() {
  const form = $('#issue-form');
  const areaSel = $('#issue-area');
  const typeSel = $('#issue-type');
  const plateIn = $('#issue-plate');
  const colorIn = $('#issue-color');
  const ownerIn = $('#issue-owner');
  const userId = $('#issue-user-id');
  const operator = $('#issue-operator');
  const submit = $('#issue-submit');

  let tarifByType = {};

  try {
    const areas = (await api('/api/areas')).data || [];
    areas.forEach((a) => {
      const free = num(a.kapasitas) - num(a.terisi);
      areaSel.append(new Option(`${a.nama_area} — ${fmtInt(free)} slot kosong`, a.id_area));
    });

    const tarifs = (await api('/api/tarifs')).data || [];
    tarifs.forEach((t) => {
      tarifByType[t.jenis_kendaraan] = t.id_tarif;
      typeSel.append(new Option(typeLabel(t.jenis_kendaraan), t.jenis_kendaraan));
    });

    const users = (await api('/api/users')).data || [];
    const operatorUser =
      users.find((u) => u.role === 'petugas' && num(u.status_aktif) === 1) || users[0];
    if (operatorUser) {
      userId.value = operatorUser.id_user;
      operator.textContent = operatorUser.nama_lengkap || operatorUser.username;
    }
  } catch (e) {
    toast(e.message, 'err');
  }
form.addEventListener('submit', async (ev) => {
    ev.preventDefault();

    const plate = normPlate(plateIn.value);
    const jenis = typeSel.value;
    const idArea = areaSel.value;
    const idUser = userId.value;
    const idTarif = tarifByType[jenis];

    if (plate.length < 3) return toast('Nomor polisi tidak valid.', 'err');
    if (!idArea) return toast('Silakan pilih area parkir.', 'err');
    if (!idUser) return toast('Tidak ada akun petugas yang tersedia.', 'err');
    if (!idTarif) return toast(`Belum ada tarif untuk jenis "${typeLabel(jenis)}".`, 'err');

    setBusy(submit, true, 'Menerbitkan…');
    const btn2 = $('#issue-submit2');
    if (btn2) setBusy(btn2, true, 'Menerbitkan…');

    try {
      let all = (await api('/api/kendaraans')).data || [];
      let vehicle = all.find((k) => normPlate(k.plat_nomor) === plate);

      if (!vehicle) {
        try {
          const created = await api('/api/kendaraans', {
            method: 'POST',
            body: {
              id_user: idUser,
              plat_nomor: plate,
              jenis_kendaraan: jenis,
              warna: colorIn.value.trim() || '—',
              pemilik: ownerIn.value.trim() || 'Tanpa nama',
            },
          });
          vehicle = created.data;
        } catch (e) {
          // likely a duplicate-plate race — look it up again
          all = (await api('/api/kendaraans')).data || [];
          vehicle = all.find((k) => normPlate(k.plat_nomor) === plate);
          if (!vehicle) throw e;
        }
      }

      const issued = await api('/api/transaksis', {
        method: 'POST',
        body: {
          id_kendaraan: vehicle.id_kendaraan,
          id_tarif: idTarif,
          id_user: idUser,
          id_area: idArea,
          waktu_masuk: toDb(new Date()),
          status: 'masuk',
        },
      });

      const area = (await api('/api/areas')).data.find((a) => `${a.id_area}` === `${idArea}`);
      const t = issued.data;

      $('#ticket-no').textContent = ticketNo(t.id_parkir);
      $('#ticket-plate').textContent = plate;
      $('#ticket-type').textContent = typeLabel(jenis);
      $('#ticket-color').textContent = colorIn.value.trim() || '—';
      $('#ticket-owner').textContent = ownerIn.value.trim() || 'Tanpa nama';
      $('#ticket-area').textContent = area ? area.nama_area : '—';
      $('#ticket-time').textContent = fmtDateTime(new Date());

      setPanel('#ticket-panel', true);
      toast(`Tiket ${ticketNo(t.id_parkir)} diterbitkan untuk ${plate}.`);
      form.reset();
    } catch (e) {
      toast(e.message, 'err');
    } finally {
      setBusy(submit, false);
      if (btn2) setBusy(btn2, false);
    }
  });

  const again = $('#issue-again');
  if (again) again.addEventListener('click', () => setPanel('#ticket-panel', false));
}
// ============================================================
// CHECK OUT — find active ticket, pay & release the slot
// ============================================================
async function keluar() {
  const plateIn = $('#exit-plate');
  const findBtn = $('#find-ticket');
  const activePanel = $('#exit-active');
  const receiptPanel = $('#exit-receipt');
  const emptyPanel = $('#exit-empty');
  const payBtn = $('#pay-btn');
  const liveElapsed = $('#live-elapsed');
  const liveFee = $('#live-fee');

  let current = null;
  let timer = null;
  let entryDt = null;
  let rate = 0;

  const stopTimer = () => {
    if (timer) { clearInterval(timer); timer = null; }
  };

  const updateLive = () => {
    if (!entryDt) return;
    const now = new Date();
    const ms = Math.max(0, now.getTime() - entryDt.getTime());
    const charged = Math.max(1, Math.ceil(ms / 3600000));
    liveElapsed.textContent = hmss(ms);
    liveFee.textContent = fmtMoney(charged * rate);
  };

  const renderActive = (t) => {
    current = t;
    entryDt = parseDb(t.waktu_masuk);
    rate = num(t.tarif && t.tarif.tarif_per_jam);

    $('#exit-no').textContent = ticketNo(t.id_parkir);
    $('#exit-plate-show').textContent = normPlate(t.kendaraan && t.kendaraan.plat_nomor);
    $('#exit-type').textContent = typeLabel(t.kendaraan && t.kendaraan.jenis_kendaraan);
    $('#exit-area').textContent = t.area ? t.area.nama_area : '—';
    $('#exit-owner').textContent = (t.kendaraan && t.kendaraan.pemilik) || '—';
    $('#exit-time-in').textContent = fmtDateTime(entryDt);
    $('#exit-rate').textContent = `${fmtMoney(rate)} / jam`;

    stopTimer();
    updateLive();
    timer = setInterval(updateLive, 1000);
  };

  const findActive = async () => {
    const plate = normPlate(plateIn.value);
    if (plate.length < 3) return toast('Masukkan nomor polisi terlebih dahulu.', 'err');
    setBusy(findBtn, true, 'Mencari…');
    setPanel(activePanel, false);
    setPanel(receiptPanel, false);
    setPanel(emptyPanel, false);
    try {
      const all = (await api('/api/transaksis')).data || [];
      const match = all.find(
        (t) => t.status === 'masuk' &&
               normPlate(t.kendaraan && t.kendaraan.plat_nomor) === plate
      );
      if (!match) {
        $('#empty-plate').textContent = plate;
        setPanel(emptyPanel, true);
        return;
      }
      renderActive(match);
      setPanel(activePanel, true);
    } catch (e) {
      toast(e.message, 'err');
    } finally {
      setBusy(findBtn, false);
    }
  };

  findBtn.addEventListener('click', findActive);
  plateIn.addEventListener('keydown', (ev) => {
    if (ev.key === 'Enter') { ev.preventDefault(); findActive(); }
  });

  payBtn.addEventListener('click', async () => {
    if (!current) return;
    setBusy(payBtn, true, 'Memproses…');
    try {
      const res = await api(`/api/transaksis/${current.id_parkir}`, {
        method: 'PATCH',
        body: { status: 'keluar', waktu_keluar: toDb(new Date()) },
      });
      const d = res.data;
      stopTimer();
      setPanel(activePanel, false);

      $('#rcpt-no').textContent  = ticketNo(d.id_parkir);
      $('#rcpt-plate').textContent = normPlate(d.kendaraan && d.kendaraan.plat_nomor);
      $('#rcpt-type').textContent = typeLabel(d.kendaraan && d.kendaraan.jenis_kendaraan);
      $('#rcpt-area').textContent = d.area ? d.area.nama_area : '—';
      $('#rcpt-in').textContent   = fmtDateTime(parseDb(d.waktu_masuk));
      $('#rcpt-out').textContent  = fmtDateTime(parseDb(d.waktu_keluar));
      $('#rcpt-dur').textContent  = durLabel(d.durasi_jam);
      $('#rcpt-amount').textContent = fmtMoney(d.biaya_total);

      setPanel(receiptPanel, true);
      toast(`Tiket ${ticketNo(d.id_parkir)} selesai — ${fmtMoney(d.biaya_total)} diterima.`);
      current = null;
    } catch (e) {
      toast(e.message, 'err');
    } finally {
      setBusy(payBtn, false);
    }
  });

  const done = $('#rcpt-done');
  if (done) done.addEventListener('click', () => {
    setPanel(receiptPanel, false);
    plateIn.value = '';
  });
}
// ============================================================
// TICKETS — history with search & status filters
// ============================================================
async function transaksi() {
  const wrap = $('#tx-body');
  const countEl = $('#tx-count');
  let all = [];
  let q = '';
  let filter = 'all';

  try {
    all = (await api('/api/transaksis')).data || [];
  } catch (e) {
    toast(e.message, 'err');
  }

  all.sort((a, b) => {
    const da = parseDb(a.waktu_masuk);
    const db = parseDb(b.waktu_masuk);
    return (db ? db.getTime() : 0) - (da ? da.getTime() : 0);
  });

  const matches = () =>
    all.filter((t) => {
      if (filter !== 'all' && t.status !== filter) return false;
      if (!q) return true;
      const plate = normPlate(t.kendaraan && t.kendaraan.plat_nomor);
      return `${plate} ${ticketNo(t.id_parkir)}`.toLowerCase().includes(q.toLowerCase());
    });

  const render = () => {
    const rows = matches();
    countEl.textContent = `${rows.length} dari ${all.length} tiket`;

    if (!rows.length) {
      wrap.innerHTML =
        '<tr><td colspan="8" class="px-4 py-12 text-center text-sm text-slate-400">' +
        'Tidak ada tiket yang cocok dengan pencarian.</td></tr>';
      return;
    }

    wrap.innerHTML = rows.map((t, i) => {
      const k = t.kendaraan || {};
      const inD = parseDb(t.waktu_masuk);
      const outD = parseDb(t.waktu_keluar);
      const done = t.status === 'keluar';
      return `<tr class="rise border-b border-primary-100 hover:bg-primary-50" style="animation-delay:${Math.min(i * 35, 400)}ms">
        <td class="px-4 py-3 font-mono font-semibold text-primary-700 whitespace-nowrap">${ticketNo(t.id_parkir)}</td>
        <td class="px-4 py-3">
          <div class="font-medium text-slate-800">${esc(k.plat_nomor || '—')}</div>
          <div class="text-[11px] uppercase tracking-wide text-slate-400">${esc(typeLabel(k.jenis_kendaraan))}</div>
        </td>
        <td class="px-4 py-3 text-slate-600">${esc(t.area ? t.area.nama_area : '—')}</td>
        <td class="px-4 py-3 text-slate-600 whitespace-nowrap">${fmtDateTime(inD)}</td>
        <td class="px-4 py-3 text-slate-600 whitespace-nowrap">${done ? fmtDateTime(outD) : '<span class="text-slate-300">—</span>'}</td>
        <td class="px-4 py-3 text-slate-600">${done ? durLabel(t.durasi_jam) : '<span class="text-slate-300">—</span>'}</td>
        <td class="px-4 py-3 font-semibold ${done ? 'text-primary-700' : 'text-slate-300'} whitespace-nowrap">${done ? fmtMoney(t.biaya_total) : '—'}</td>
        <td class="px-4 py-3">${statusBadge(t.status)}</td>
      </tr>`;
    }).join('');
  };

  render();

  const search = $('#tx-search');
  if (search) search.addEventListener('input', (e) => { q = e.target.value; render(); });

  $$('#tx-filters button').forEach((b) => {
    b.addEventListener('click', () => {
      filter = b.dataset.filter;
      $$('#tx-filters button').forEach((x) => x.classList.toggle('is-active', x === b));
      render();
    });
  });
}
// ============================================================
// AREAS — live occupancy cards + admin/petugas management
// ============================================================
async function area() {
  const grid = $('#area-grid');
  const modal = $('#area-modal');
  const form = $('#area-form');
  const title = $('#area-modal-title');
  const submit = $('#area-submit');
  const managed = canManageAreas();
  let areas = [];

  const load = async () => {
    try {
      areas = (await api('/api/areas')).data || [];
    } catch (e) {
      toast(e.message, 'err');
      return;
    }
    render();
  };

  const render = () => {
    const spots = areas.reduce((s, a) => s + num(a.kapasitas), 0);
    const occupied = areas.reduce((s, a) => s + num(a.terisi), 0);
    const free = Math.max(0, spots - occupied);
    countUp($('#area-sum-spots'), spots);
    countUp($('#area-sum-occupied'), occupied);
    countUp($('#area-sum-free'), free);

    if (!grid) return;
    if (!areas.length) {
      grid.innerHTML =
        '<div class="rounded-2xl border border-primary-100 bg-white p-10 text-center text-sm text-slate-400 rise">Belum ada area parkir.</div>';
      return;
    }

    grid.innerHTML = areas.map((a, i) => {
    const cap = num(a.kapasitas);
    const use = num(a.terisi);
    const pct = cap ? (use / cap) * 100 : 0;

    let chip = '<span class="inline-flex items-center rounded-full bg-emerald-100 text-emerald-800 px-2.5 py-0.5 text-[11px] font-semibold">TERSEDIA</span>';
    let barTone = 'bg-primary-500';
    if (use >= cap) {
      chip = '<span class="inline-flex items-center rounded-full bg-red-100 text-red-700 px-2.5 py-0.5 text-[11px] font-semibold">PENUH</span>';
      barTone = 'bg-red-400';
    } else if (pct >= 80) {
      chip = '<span class="inline-flex items-center rounded-full bg-amber-100 text-amber-700 px-2.5 py-0.5 text-[11px] font-semibold">HAMPIR PENUH</span>';
      barTone = 'bg-amber-400';
    }

    const actions = managed ? `
      <div class="mt-auto flex gap-2 border-t border-primary-50 pt-3">
        <button type="button" data-edit="${a.id_area}" class="flex-1 rounded-lg border border-primary-200 bg-white px-3 py-1.5 text-xs font-semibold text-primary-700 transition hover:bg-primary-50">Ubah</button>
        <button type="button" data-del="${a.id_area}" class="flex-1 rounded-lg border border-red-200 bg-white px-3 py-1.5 text-xs font-semibold text-red-600 transition hover:bg-red-50">Hapus</button>
      </div>` : '';

    return `
      <div class="rise flex flex-col gap-3 rounded-2xl border border-primary-100 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md" style="animation-delay:${i * 60}ms">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-2">
            <div class="grid h-8 w-8 place-items-center rounded-lg bg-primary-500 text-sm font-bold text-white">${esc(a.nama_area.slice(0, 1))}</div>
            <div>
              <h3 class="font-semibold text-slate-800">${esc(a.nama_area)}</h3>
              <p class="text-xs text-slate-400">Slot P-${esc(a.nama_area.slice(-2))}</p>
            </div>
          </div>
          ${chip}
        </div>
        <div class="h-2.5 rounded-full bg-primary-100 overflow-hidden">
          <div class="${barTone} h-full rounded-full" data-bar="${pct}"></div>
        </div>
        <div class="flex items-center justify-between text-sm">
          <span class="font-semibold text-primary-700">${fmtInt(use)} <span class="text-xs text-slate-400">dari ${fmtInt(cap)} terisi</span></span>
          <span class="text-slate-500">${Math.max(0, cap - use)} slot kosong</span>
        </div>
        ${actions}
      </div>`;
  }).join('');

    $$('#area-grid [data-bar]').forEach((b) => setBar(b, num(b.dataset.bar)));
  };

  wireAreaManagement({ modal, form, title, submit, managed, getAreas: () => areas, refresh: load });

  await load();
}

// ------------------------------------------------------------
// area management modal — add / edit / delete (admin & petugas)
// ------------------------------------------------------------
function wireAreaManagement({ modal, form, title, submit, managed, getAreas, refresh }) {
  if (!managed || !modal || !form) return;

  const openModal = (a = null) => {
    form.dataset.editing = a ? a.id_area : '';
    $('#area-form-nama').value = a ? a.nama_area : '';
    $('#area-form-kapasitas').value = a ? a.kapasitas : '';
    $('#area-form-terisi').value = a ? a.terisi : 0;
    if (title) title.textContent = a ? `Ubah Area — ${a.nama_area}` : 'Tambah Area Parkir';
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    $('#area-form-nama').focus();
  };

  const closeModal = () => {
    modal.classList.add('hidden');
    modal.classList.remove('flex');
  };

  $('#area-add')?.addEventListener('click', () => openModal());
  $('#area-cancel')?.addEventListener('click', closeModal);
  $('#area-close')?.addEventListener('click', closeModal);
  modal.addEventListener('click', (e) => { if (e.target === modal) closeModal(); });

  form.addEventListener('submit', async (ev) => {
    ev.preventDefault();

    const editingId = form.dataset.editing || '';
    const payload = {
      nama_area: $('#area-form-nama').value.trim(),
      kapasitas: Math.round(num($('#area-form-kapasitas').value)),
      terisi: Math.round(num($('#area-form-terisi').value)),
    };

    if (!payload.nama_area) return toast('Nama area wajib diisi.', 'err');
    if (payload.kapasitas < 1) return toast('Kapasitas minimal 1 slot.', 'err');
    if (payload.terisi < 0) return toast('Jumlah terisi tidak boleh negatif.', 'err');
    if (payload.terisi > payload.kapasitas) return toast('Jumlah terisi tidak boleh melebihi kapasitas.', 'err');

    setBusy(submit, true, 'Menyimpan…');
    try {
      const res = editingId
        ? await api(`/api/areas/${editingId}`, { method: 'PUT', body: payload })
        : await api('/api/areas', { method: 'POST', body: payload });
      toast(res.message || 'Data area tersimpan.');
      closeModal();
      await refresh();
    } catch (e) {
      toast(e.message, 'err');
    } finally {
      setBusy(submit, false);
    }
  });

  const grid = $('#area-grid');
  if (!grid) return;
  grid.addEventListener('click', async (e) => {
    const editBtn = e.target.closest('[data-edit]');
    const delBtn = e.target.closest('[data-del]');

    if (editBtn) {
      const a = getAreas().find((x) => `${x.id_area}` === `${editBtn.dataset.edit}`);
      if (a) openModal(a);
    } else if (delBtn) {
      const a = getAreas().find((x) => `${x.id_area}` === `${delBtn.dataset.del}`);
      if (!a) return;
      if (!confirm(`Hapus area "${a.nama_area}"?`)) return;
      setBusy(delBtn, true, '…');
      try {
        const res = await api(`/api/areas/${a.id_area}`, { method: 'DELETE' });
        toast(res.message || 'Area parkir dihapus.');
        await refresh();
      } catch (err) {
        toast(err.message, 'err');
        setBusy(delBtn, false);
      }
    }
  });
}

// ============================================================
// VEHICLES — registry with search + add / edit / delete
// ============================================================
async function kendaraan() {
  const grid = $('#vehicle-grid');
  const input = $('#vehicle-search');
  const modal = $('#vehicle-modal');
  const form = $('#vehicle-form');
  let all = [];
  let parkedNow = {};
  let users = [];

  const load = async () => {
    try {
      all = (await api('/api/kendaraans')).data || [];
      const tx = (await api('/api/transaksis')).data || [];
      parkedNow = {};
      tx.filter((t) => t.status === 'masuk').forEach((t) => {
        parkedNow[t.id_kendaraan] = t;
      });
    } catch (e) {
      toast(e.message, 'err');
      return;
    }
    render();
  };

  const render = () => {
    const q = normPlate(input ? input.value : '');
    const rows = all.filter((v) => !q || normPlate(v.plat_nomor).includes(q));

    if (!grid) return;
    if (!rows.length) {
      grid.innerHTML =
        '<div class="rounded-2xl border border-primary-100 bg-white p-10 text-center text-slate-400 text-sm rise">' +
        (q ? 'Tidak ada kendaraan dengan plat tersebut.' : 'Belum ada kendaraan yang terdaftar.') + '</div>';
      return;
    }

    grid.innerHTML = rows.map((v, i) => {
      const active = parkedNow[v.id_kendaraan];
      const actions = `
        <div class="mt-auto flex gap-2 border-t border-primary-50 pt-3">
          <button type="button" data-edit="${v.id_kendaraan}" class="flex-1 rounded-lg border border-primary-200 bg-white px-3 py-1.5 text-xs font-semibold text-primary-700 transition hover:bg-primary-50">Ubah</button>
          <button type="button" data-del="${v.id_kendaraan}" class="flex-1 rounded-lg border border-red-200 bg-white px-3 py-1.5 text-xs font-semibold text-red-600 transition hover:bg-red-50">Hapus</button>
        </div>`;
      return `
        <div class="rise flex flex-col gap-3 rounded-2xl border border-primary-100 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md" style="animation-delay:${i * 50}ms">
          <div class="flex items-center justify-between">
            <div class="grid h-11 w-11 place-items-center rounded-xl bg-primary-500 text-white">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6">
                <path d="M4 14 L14 14 M4 8 L14 8 M4 8 L4 14 M14 8 L14 14 M2 8 L2 14 M16 8 L16 14 M9 5 L9 8 M9 8 L9 5" stroke-linecap="round"/>
              </svg>
            </div>
            <span class="rounded-full bg-primary-100 px-2.5 py-0.5 text-[11px] font-semibold text-primary-700">${esc(typeLabel(v.jenis_kendaraan))}</span>
          </div>
          <p class="font-mono text-lg font-bold tracking-wide text-slate-800">${esc(v.plat_nomor)}</p>
          <div class="text-xs text-slate-400">${esc(v.warna || '—')} · ${esc(v.pemilik || 'Tanpa nama')}</div>
          ${active
            ? `<div class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 text-emerald-800 px-2.5 py-0.5 text-[11px] font-semibold"><span class="h-1.5 w-1.5 rounded-full bg-emerald-500 live-dot"></span>SEDANG PARKIR · ${esc(ticketNo(active.id_parkir))}</div>`
            : '<span class="text-[11px] text-slate-300">Belum ada tiket aktif</span>'}
          ${actions}
        </div>`;
    }).join('');
  };

  try {
    users = (await api('/api/users')).data || [];
  } catch (e) { /* dropdown stays empty */ }

  wireVehicleManagement({ modal, form, getUsers: () => users, getVehicles: () => all, refresh: load });

  await load();

  if (input) input.addEventListener('input', render);
}

// ------------------------------------------------------------
// vehicle management modal — add / edit / delete
// ------------------------------------------------------------
function wireVehicleManagement({ modal, form, getUsers, getVehicles, refresh }) {
  if (!modal || !form) return;

  const title = $('#vehicle-modal-title');
  const submit = $('#vehicle-submit');

  const fillUserOptions = (selectedId) => {
    const sel = $('#vehicle-form-user');
    if (!sel) return;
    sel.innerHTML = getUsers()
      .map((u) => `<option value="${u.id_user}">${esc(u.nama_lengkap || u.username)} · ${esc(u.role)}</option>`)
      .join('');
    if (selectedId) {
      sel.value = selectedId;
    } else {
      const def = getUsers().find((u) => u.role === 'petugas' && num(u.status_aktif) === 1) || getUsers()[0];
      if (def) sel.value = def.id_user;
    }
  };

  const openModal = (v = null) => {
    form.dataset.editing = v ? v.id_kendaraan : '';
    $('#vehicle-form-plate').value = v ? v.plat_nomor : '';
    $('#vehicle-form-type').value = v ? v.jenis_kendaraan : 'mobil';
    $('#vehicle-form-color').value = v && v.warna && v.warna !== '—' ? v.warna : '';
    $('#vehicle-form-owner').value = v && v.pemilik && v.pemilik !== 'Tanpa nama' ? v.pemilik : '';
    fillUserOptions(v ? v.id_user : null);
    if (title) title.textContent = v ? `Ubah Kendaraan — ${v.plat_nomor}` : 'Tambah Kendaraan';
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    $('#vehicle-form-plate').focus();
  };

  const closeModal = () => {
    modal.classList.add('hidden');
    modal.classList.remove('flex');
  };

  $('#vehicle-add')?.addEventListener('click', () => openModal());
  $('#vehicle-cancel')?.addEventListener('click', closeModal);
  $('#vehicle-close')?.addEventListener('click', closeModal);
  modal.addEventListener('click', (e) => { if (e.target === modal) closeModal(); });

  form.addEventListener('submit', async (ev) => {
    ev.preventDefault();

    const editingId = form.dataset.editing || '';
    const plate = normPlate($('#vehicle-form-plate').value);
    const payload = {
      id_user: $('#vehicle-form-user').value,
      plat_nomor: plate,
      jenis_kendaraan: $('#vehicle-form-type').value,
      warna: $('#vehicle-form-color').value.trim() || '—',
      pemilik: $('#vehicle-form-owner').value.trim() || 'Tanpa nama',
    };

    if (plate.length < 3) return toast('Nomor polisi minimal 3 karakter.', 'err');
    if (!payload.jenis_kendaraan) return toast('Pilih jenis kendaraan.', 'err');
    if (!payload.id_user) return toast('Pilih petugas / pemilik data.', 'err');
    const dup = getVehicles().find((v) => normPlate(v.plat_nomor) === plate && `${v.id_kendaraan}` !== `${editingId}`);
    if (dup) return toast(`Plat ${plate} sudah terdaftar.`, 'err');

    setBusy(submit, true, 'Menyimpan…');
    try {
      const res = editingId
        ? await api(`/api/kendaraans/${editingId}`, { method: 'PUT', body: payload })
        : await api('/api/kendaraans', { method: 'POST', body: payload });
      toast(res.message || 'Data kendaraan tersimpan.');
      closeModal();
      await refresh();
    } catch (e) {
      toast(e.message, 'err');
    } finally {
      setBusy(submit, false);
    }
  });

  const grid = $('#vehicle-grid');
  if (!grid) return;
  grid.addEventListener('click', async (e) => {
    const editBtn = e.target.closest('[data-edit]');
    const delBtn = e.target.closest('[data-del]');

    if (editBtn) {
      const v = getVehicles().find((x) => `${x.id_kendaraan}` === `${editBtn.dataset.edit}`);
      if (v) openModal(v);
    } else if (delBtn) {
      const v = getVehicles().find((x) => `${x.id_kendaraan}` === `${delBtn.dataset.del}`);
      if (!v) return;
      if (!confirm(`Hapus kendaraan "${v.plat_nomor}"?`)) return;
      setBusy(delBtn, true, '…');
      try {
        const res = await api(`/api/kendaraans/${v.id_kendaraan}`, { method: 'DELETE' });
        toast(res.message || 'Kendaraan dihapus.');
        await refresh();
      } catch (err) {
        toast(err.message, 'err');
        setBusy(delBtn, false);
      }
    }
  });
}

// ============================================================
// LOG — live monitoring of activity & system log files
// ============================================================
const LOG_LEVEL_RE = /\b(local|production)\.(ALERT|CRITICAL|ERROR|WARNING|NOTICE|INFO|DEBUG)\b/;
const LOG_LEVEL_TONE = {
  ALERT: 'text-red-300 bg-red-500/40 px-1 rounded',
  CRITICAL: 'text-red-300 bg-red-500/40 px-1 rounded',
  ERROR: 'text-red-400',
  WARNING: 'text-amber-300',
  NOTICE: 'text-sky-300',
  INFO: 'text-cyan-300',
  DEBUG: 'text-slate-400',
};

async function log() {
  const liveBadge = $('#log-live-badge');
  const liveLabel = $('#log-live-label');
  const intervalSel = $('#log-interval');
  const refreshBtn = $('#log-refresh');
  const searchIn = $('#log-search');
  const body = $('#log-activity-body');
  const countEl = $('#log-count');
  const chip = $('#log-new-chip');
  const fileMeta = $('#log-file-meta');
  const fileSize = $('#log-file-size');
  const fileBody = $('#log-file-body');

  let all = [];
  let sys = [];
  let sysLoaded = false;
  let q = '';
  let live = true;
  let timer = null;
  let chipTimer = null;
  let prevTopId = null;

  const secs = () => num(intervalSel ? intervalSel.value : 5) || 0;
  const isToday = (d) => d && d.toDateString() === new Date().toDateString();

  const renderStats = () => {
    const total = all.length;
    const today = all.filter((l) => isToday(parseDb(l.waktu_aktivitas))).length;
    const users = new Set(all.map((l) => l.user && l.user.id_user).filter(Boolean)).size;
    const errors = sysLoaded && sys ? sys.filter((ln) => /\.(ALERT|CRITICAL|ERROR)\b/.test(ln)).length : 0;
    const setTxt = (sel, txt) => { const el = $(sel); if (el) el.textContent = txt; };
    setTxt('#log-stat-total', fmtInt(total));
    setTxt('#log-stat-today', fmtInt(today));
    setTxt('#log-stat-users', fmtInt(users));
    setTxt('#log-stat-errors', sysLoaded ? (sys === null ? '—' : fmtInt(errors)) : '…');
  };

  const matches = (l) => {
    if (!q) return true;
    const u = l.user || {};
    const hay = `${l.aktivitas} ${u.nama_lengkap} ${u.username} ${u.role}`.toLowerCase();
    return hay.includes(q.toLowerCase());
  };

  const renderTable = () => {
    const rows = all.filter(matches);
    countEl.textContent = `${rows.length} dari ${all.length} log`;
    if (!rows.length) {
      body.innerHTML =
        '<tr><td colspan="3" class="px-4 py-12 text-center text-sm text-slate-400">' +
        'Tidak ada log yang cocok dengan pencarian.</td></tr>';
      return;
    }
    body.innerHTML = rows.map((l, i) => {
      const u = l.user || {};
      const d = parseDb(l.waktu_aktivitas);
      const ini = esc(`${u.nama_lengkap || '?'}`.trim().charAt(0).toUpperCase());
      const tone = isToday(d)
        ? 'bg-primary-100 text-primary-700 ring-primary-200'
        : 'bg-slate-100 text-slate-500 ring-slate-200';
      return `<tr class="rise border-b border-primary-100 hover:bg-primary-50" style="animation-delay:${Math.min(i * 30, 300)}ms">
        <td class="px-4 py-3 whitespace-nowrap text-slate-600">${fmtDateTime(d)}</td>
        <td class="px-4 py-3">
          <div class="flex items-center gap-2.5">
            <span class="grid h-7 w-7 shrink-0 place-items-center rounded-full text-[11px] font-bold ring-1 ${tone}">${ini}</span>
            <div>
              <div class="text-xs font-semibold text-slate-800">${esc(u.nama_lengkap || '—')}</div>
              <div class="text-[10px] text-slate-400">@${esc(u.username || '?')} · <span class="uppercase">${esc(u.role || '—')}</span></div>
            </div>
          </div>
        </td>
        <td class="px-4 py-3 text-slate-700">${esc(l.aktivitas)}</td>
      </tr>`;
    }).join('');
  };

  const renderFile = () => {
    if (!sysLoaded) {
      fileMeta.textContent = 'Memuat isi file log…';
      fileSize.textContent = '';
      fileBody.innerHTML = '<span class="text-slate-500">Menunggu data…</span>';
      return;
    }
    if (sys === null) {
      fileMeta.textContent = 'Akses dibatasi untuk admin / petugas / owner.';
      fileSize.textContent = '';
      fileBody.innerHTML =
        '<div class="text-slate-400">Login dengan akun staff untuk membaca isi file log server — ' +
        '<a href="/login" class="text-primary-300 underline hover:text-primary-200">masuk di sini</a>.</div>';
      return;
    }
    if (!sys.length) {
      fileMeta.textContent = 'Belum ada file log ditemukan.';
      fileSize.textContent = '';
      fileBody.innerHTML = '<span class="text-slate-500">Kosong.</span>';
      return;
    }
    fileBody.innerHTML = sys.map((ln) => {
      const m = ln.match(LOG_LEVEL_RE);
      const tone = m ? (LOG_LEVEL_TONE[m[2]] || 'text-slate-300') : 'text-slate-500';
      return `<div class="whitespace-pre-wrap break-all ${tone}">${esc(ln)}</div>`;
    }).join('');
  };
const refresh = async () => {
    await Promise.all([loadAktivitas(), loadSystem()]);
  };

  const loadAktivitas = async () => {
    try {
      const res = await api('/api/logs');
      const list = res.data || [];
      let fresh = 0;
      if (prevTopId !== null && list.length) {
        const idx = list.findIndex((l) => String(l.id_log) === String(prevTopId));
        fresh = idx === -1 ? list.length : idx;
      }
      prevTopId = list.length ? String(list[0].id_log) : null;
      all = list;
      renderStats();
      renderTable();
      if (fresh > 0) {
        chip.textContent = `+${fresh} log baru`;
        chip.classList.remove('hidden');
        clearTimeout(chipTimer);
        chipTimer = setTimeout(() => chip.classList.add('hidden'), 5000);
      }
    } catch (e) {
      body.innerHTML =
        `<tr><td colspan="3" class="px-4 py-12 text-center text-sm text-red-400">${esc(e.message)}</td></tr>`;
      toast(e.message, 'err');
    }
  };

  const loadSystem = async () => {
    try {
      const res = await api('/api/system-logs');
      const d = res.data || {};
      sysLoaded = true;
      sys = d.tail || [];
      fileMeta.textContent = d.file
        ? `${d.file} · diperbarui ${fmtDateTime(new Date(d.mtime * 1000))}`
        : 'Tidak ditemukan file log';
      fileSize.textContent = d.size ? `${fmtInt(d.size / 1024)} KB` : '';
      renderFile();
      renderStats();
    } catch (e) {
      sysLoaded = true;
      sys = null;
      renderFile();
      renderStats();
    }
  };

  const stopTimer = () => { if (timer) { clearInterval(timer); timer = null; } };
  const startTimer = () => {
    stopTimer();
    const s = secs();
    if (s > 0) timer = setInterval(() => { if (live) refresh(); }, s * 1000);
  };

  const updateBadge = () => {
    const s = secs();
    liveLabel.textContent = live ? (s > 0 ? `LIVE · ${s} dtk` : 'LIVE · Manual') : 'PAUSED';
    liveBadge.className = live
      ? 'inline-flex items-center gap-1.5 rounded-full bg-emerald-100 px-3 py-1.5 text-xs font-bold text-emerald-800 transition hover:bg-emerald-200'
      : 'inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-3 py-1.5 text-xs font-bold text-slate-500 transition hover:bg-slate-200';
  };

  const manualRefresh = async () => {
    setBusy(refreshBtn, true, 'Memuat…');
    await refresh();
    setBusy(refreshBtn, false);
  };

  if (intervalSel) intervalSel.addEventListener('change', () => { updateBadge(); startTimer(); });
  if (liveBadge) liveBadge.addEventListener('click', () => {
    live = !live;
    updateBadge();
    if (live) { startTimer(); refresh(); } else stopTimer();
  });
  if (refreshBtn) refreshBtn.addEventListener('click', manualRefresh);
  if (searchIn) searchIn.addEventListener('input', (e) => { q = e.target.value; renderTable(); });

  $$('#log-tabs button').forEach((b) => {
    b.addEventListener('click', () => {
      $$('#log-tabs button').forEach((x) => x.classList.toggle('is-active', x === b));
      const active = b.dataset.tab;
      ['aktivitas', 'sistem'].forEach((tab) => {
        const panel = $('#panel-' + tab);
        if (panel) panel.classList.toggle('hidden', tab !== active);
      });
    });
  });

  updateBadge();
  startTimer();
  refresh();
}

// ------------------------------------------------------------
// auth — session user injected by the layout (<body data-auth-*>)
// ------------------------------------------------------------
const AUTH_ROLE = document.body ? document.body.dataset.authRole || '' : '';
const canManageAreas = () => AUTH_ROLE === 'admin' || AUTH_ROLE === 'petugas';

// ============================================================
// LOGIN
// ============================================================
function login() {
  const form = $('#login-form');
  if (!form) return;

  const username = $('#login-username');
  const password = $('#login-password');
  const submit = $('#login-submit');

  form.addEventListener('submit', async (ev) => {
    ev.preventDefault();

    const u = username.value.trim();
    const p = password.value;
    if (!u || !p) return toast('Isi username dan password terlebih dahulu.', 'err');

    setBusy(submit, true, 'Memeriksa…');
    try {
      const res = await api('/api/login', {
        method: 'POST',
        body: { username: u, password: p },
      });
      toast(res.message || 'Login berhasil.');
      setTimeout(() => { window.location.href = '/'; }, 500);
    } catch (e) {
      toast(e.message, 'err');
      setBusy(submit, false);
    }
  });
}

// ============================================================
// REGISTER — create a petugas account, then auto-login
// ============================================================
function register() {
  const form = $('#register-form');
  if (!form) return;

  const nama = $('#register-nama');
  const username = $('#register-username');
  const password = $('#register-password');
  const password2 = $('#register-password2');
  const submit = $('#register-submit');

  form.addEventListener('submit', async (ev) => {
    ev.preventDefault();

    const n = nama.value.trim();
    const u = username.value.trim();
    const p = password.value;
    const p2 = password2.value;

    if (!n || !u || !p || !p2) return toast('Semua kolom wajib diisi.', 'err');
    if (u.length < 3) return toast('Username minimal 3 karakter.', 'err');
    if (p.length < 8) return toast('Password minimal 8 karakter.', 'err');
    if (p !== p2) return toast('Konfirmasi password tidak cocok.', 'err');

    setBusy(submit, true, 'Mendaftarkan…');
    try {
      const res = await api('/api/register', {
        method: 'POST',
        body: {
          nama_lengkap: n,
          username: u,
          password: p,
          password_confirmation: p2,
        },
      });
      toast(res.message || 'Registrasi berhasil.');
      setTimeout(() => { window.location.href = '/'; }, 500);
    } catch (e) {
      toast(e.message, 'err');
      setBusy(submit, false);
    }
  });
}

// ------------------------------------------------------------
// boot
// ------------------------------------------------------------
const PAGE = document.body ? document.body.dataset.page : '';
const PAGES = { beranda, masuk, keluar, transaksi, area, kendaraan, log, login, register };

startClock();

const tgl = $('#nav-toggle');
if (tgl) {
  tgl.addEventListener('click', () => {
    const menu = $('#nav-menu');
    if (menu) menu.classList.toggle('hidden');
  });
}

if (PAGE && PAGES[PAGE]) {
  PAGES[PAGE]().catch((e) => toast(e.message || 'Terjadi kesalahan yang tidak terduga.', 'err'));
}
