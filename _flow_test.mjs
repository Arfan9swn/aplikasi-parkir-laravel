// Temporary end-to-end test of the ticketing flow through the running API.
const BASE = 'http://127.0.0.1:8000';

async function call(method, path, body) {
  const res = await fetch(BASE + path, {
    method,
    headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
    body: body === undefined ? undefined : JSON.stringify(body),
  });
  const text = await res.text();
  let json = null;
  try { json = JSON.parse(text); } catch { json = text; }
  console.log(`\n--- ${method} ${path} -> ${res.status}`);
  console.log(JSON.stringify(json, null, 2));
  return { ok: res.ok, status: res.status, json };
}

const main = async () => {
  // 1) Check-in: issue an ACTIVE ticket (status masuk, no exit time)
  const issue = await call('POST', '/api/transaksis', {
    id_kendaraan: 3,
    id_tarif: 2,
    id_user: 1,
    id_area: 2,
    waktu_masuk: '2026-09-08 04:45:00',
    status: 'masuk',
  });

  if (!issue.ok) {
    console.log('ISSUE FAILED — aborting.');
    return;
  }

  const ticketId = issue.json.data.id_parkir;
  console.log('\nISSUED TICKET id =', ticketId);

  // 2) Area 2 (VIP) occupancy should now be 1
  await call('GET', '/api/areas');

  // 3) Check-out: complete the ticket (1 hour later)
  await call('PATCH', `/api/transaksis/${ticketId}`, {
    status: 'keluar',
    waktu_keluar: '2026-09-08 05:45:00',
  });

  // 4) Cleanup test ticket (keep the seeded demo data pristine)
  await call('DELETE', `/api/transaksis/${ticketId}`);

  // 5) Final state
  await call('GET', '/api/transaksis');
  await call('GET', '/api/areas');
};

main().catch((e) => { console.error('FATAL', e); process.exit(1); });