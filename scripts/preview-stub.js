#!/usr/bin/env node
// Lovable harness shim for a PHP project. Accepts and ignores --port etc.
const http = require('http');
const port = (() => {
  const i = process.argv.indexOf('--port');
  return i >= 0 ? parseInt(process.argv[i + 1], 10) : 8080;
})();
const html = `<!doctype html><meta charset="utf-8"><title>Cartly (PHP)</title>
<style>body{font-family:system-ui;max-width:640px;margin:40px auto;padding:0 16px;color:#1f2937}
code,pre{background:#f5f7f6;padding:2px 6px;border-radius:4px}
pre{padding:12px;overflow:auto}</style>
<h1>🛒 Cartly &mdash; PHP MVC project</h1>
<p>Lovable's in-browser preview can't run PHP. This project is built for
<strong>XAMPP / LAMP</strong>. To run it locally:</p>
<pre>1. Copy the project into <code>htdocs/cartly</code>
2. Import <code>src/database/schema.sql</code> then <code>seed.sql</code> in phpMyAdmin
3. Visit <code>http://localhost/cartly/src/public/</code></pre>
<p>Default logins: <code>admin</code> / <code>merchant</code> / <code>customer</code> &mdash; password <code>password123</code>.</p>
<p>See <code>src/database/README.md</code> for full setup.</p>`;
http.createServer((req, res) => {
  res.writeHead(200, { 'content-type': 'text/html; charset=utf-8' });
  res.end(html);
}).listen(port, '0.0.0.0', () => console.log('Cartly placeholder preview on :' + port));
