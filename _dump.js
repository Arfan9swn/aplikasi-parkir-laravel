import fs from 'fs';
const file = process.argv[2] || 'resources/js/app.js';
const L = fs.readFileSync(file, 'utf8').split('\n');
if (process.argv[3] === 'grep') {
  const re = new RegExp(process.argv[4], 'i');
  L.forEach((l, i) => { if (re.test(l)) console.log((i + 1) + ': ' + l.trim()); });
  process.exit(0);
}
console.log('TOTAL LINES:', L.length);
const a = parseInt(process.argv[3] || '96', 10);
const b = parseInt(process.argv[4] || String(L.length), 10);
for (let i = a; i <= Math.min(b, L.length); i++) console.log(i + ': ' + L[i - 1]);
