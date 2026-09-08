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
// AREAS — live occupancy cards
// ============================================================
async function area() {
  const grid = $('#area-grid');
  let areas = [];

  try {
    areas = (await api('/api/areas')).data || [];
  } catch (e) {
    toast(e.message, 'err');
  }

  const spots = areas.reduce((s, a) => s + num(a.kapasitas), 0);
  const occupied = areas.reduce((s, a) => s + num(a.terisi), 0);
  const free = Math.max(0, spots - occupied);
  countUp($('#area-sum-spots'), spots);
  countUp($('#area-sum-occupied'), occupied);
  countUp($('#area-sum-free'), free);

  if (!grid) return;
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
      </div>`;
  }).join('');

  $$('#area-grid [data-bar]').forEach((b) => setBar(b, num(b.dataset.bar)));
}

// ============================================================
// VEHICLES — registry with search
// ============================================================
async function kendaraan() {
  const grid = $('#vehicle-grid');
  const input = $('#vehicle-search');
  let all = [];
  let parkedNow = {};

  try {
    all = (await api('/api/kendaraans')).data || [];
    const tx = (await api('/api/transaksis')).data || [];
    tx.filter((t) => t.status === 'masuk').forEach((t) => {
      parkedNow[t.id_kendaraan] = t;
    });
  } catch (e) {
    toast(e.message, 'err');
  }

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
        </div>`;
    }).join('');
  };

  render();
  if (input) input.addEventListener('input', render);
}

// ------------------------------------------------------------
// boot
// ------------------------------------------------------------
const PAGE = document.body ? document.body.dataset.page : '';
const PAGES = { beranda, masuk, keluar, transaksi, area, kendaraan };

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
