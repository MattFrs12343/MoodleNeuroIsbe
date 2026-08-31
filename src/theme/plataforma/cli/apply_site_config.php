<?php
// Script de una sola ejecución (no forma parte del runtime del tema). Aplica:
//   1) el nombre visible del sitio => "NeuroIsbe" (antes "NeuroIsbe — Portal de Estudos").
//   2) $CFG->additionalhtmlfooter => footer legal (marca actualizada) + el <script> que
//      dibuja las "constelaciones" animadas en un <canvas>, SÓLO en la página de login
//      (guardado por la clase body.pagelayout-login) + el splash de carga con el logo
//      (cerebro), SÓLO en páginas sin sesión iniciada (guardado por body.notloggedin —
//      login, portada como invitado, etc.; nunca navegando ya logueado).
// Usa API pública de Moodle / config estándar, no toca core ni la BD a mano salvo el
// campo fullname del curso sitio (lo mismo que hace la pantalla "Front page settings").
//
// Uso: php theme/plataforma/cli/apply_site_config.php
define('CLI_SCRIPT', true);
require(__DIR__ . '/../../../config.php');
require_once($CFG->dirroot . '/course/lib.php');
require_once($CFG->libdir . '/clilib.php');
global $DB, $OUTPUT;

// 1) Nombre del sitio.
$DB->set_field('course', 'fullname', 'NeuroIsbe', ['id' => SITEID]);
rebuild_course_cache(SITEID, true);
cli_writeln('OK: fullname del sitio => "NeuroIsbe".');

// URL real del logo (theme/plataforma/pix/logo.png) resuelta vía la API de Moodle
// (con la revisión de tema correcta) — NO hardcodear la ruta, cambia con cada purge.
$logourl = $OUTPUT->image_url('logo', 'theme')->out(false);

// 2) Footer + constelaciones del login + splash de carga. Heredoc (interpola $logourl;
// el resto del bloque no usa "$" en ningún lado, es JS puro).
$footer = <<<HTML
<div class="plataforma-footer">
  <div>NeuroIsbe &middot; &copy; 2026</div>
  <div class="plataforma-footer-links">
    <a href="/admin/tool/policy/view.php?versionid=1">Aviso de Privacidade</a>
    <span>&middot;</span>
    <a href="/admin/tool/policy/view.php?versionid=2">Termos de Uso</a>
  </div>
</div>
<script>
(function(){
  var b = document.body;
  if (!b || b.className.indexOf('pagelayout-login') === -1) { return; }
  var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var canvas = document.createElement('canvas');
  canvas.className = 'login-constellation';
  canvas.setAttribute('aria-hidden', 'true');
  b.insertBefore(canvas, b.firstChild);
  var ctx = canvas.getContext('2d');
  var w, h, dpr, pts, raf;
  function build(){
    dpr = Math.min(window.devicePixelRatio || 1, 2);
    w = canvas.width = window.innerWidth * dpr;
    h = canvas.height = window.innerHeight * dpr;
    canvas.style.width = window.innerWidth + 'px';
    canvas.style.height = window.innerHeight + 'px';
    var n = Math.max(28, Math.min(90, Math.round(window.innerWidth * window.innerHeight / 17000)));
    pts = [];
    for (var i = 0; i < n; i++) {
      pts.push({
        x: Math.random() * w, y: Math.random() * h,
        vx: (Math.random() - 0.5) * 0.16 * dpr, vy: (Math.random() - 0.5) * 0.16 * dpr,
        r: (Math.random() * 1.6 + 0.6) * dpr
      });
    }
  }
  function draw(){
    ctx.clearRect(0, 0, w, h);
    var max = 150 * dpr;
    for (var i = 0; i < pts.length; i++) {
      var p = pts[i];
      for (var j = i + 1; j < pts.length; j++) {
        var q = pts[j], dx = p.x - q.x, dy = p.y - q.y, d = Math.sqrt(dx * dx + dy * dy);
        if (d < max) {
          ctx.globalAlpha = (1 - d / max) * 0.45;
          ctx.strokeStyle = '#8ECAE6';
          ctx.lineWidth = dpr;
          ctx.beginPath(); ctx.moveTo(p.x, p.y); ctx.lineTo(q.x, q.y); ctx.stroke();
        }
      }
    }
    for (var k = 0; k < pts.length; k++) {
      var s = pts[k];
      ctx.globalAlpha = 0.9;
      ctx.fillStyle = '#CFE8F7';
      ctx.beginPath(); ctx.arc(s.x, s.y, s.r, 0, 6.283); ctx.fill();
    }
    ctx.globalAlpha = 1;
  }
  function tick(){
    for (var i = 0; i < pts.length; i++) {
      var p = pts[i];
      p.x += p.vx; p.y += p.vy;
      if (p.x < 0 || p.x > w) { p.vx *= -1; }
      if (p.y < 0 || p.y > h) { p.vy *= -1; }
    }
    draw();
    raf = window.requestAnimationFrame(tick);
  }
  build();
  if (reduce) { draw(); } else { tick(); }
  var t;
  window.addEventListener('resize', function(){
    window.clearTimeout(t);
    t = window.setTimeout(function(){
      if (raf) { window.cancelAnimationFrame(raf); }
      build();
      if (reduce) { draw(); } else { tick(); }
    }, 200);
  });
})();
</script>
<script>
(function(){
  var b = document.body;
  if (!b || b.className.indexOf('notloggedin') === -1) { return; }
  var splash = document.createElement('div');
  splash.className = 'platform-splash';
  splash.setAttribute('aria-hidden', 'true');
  splash.innerHTML = '<div class="platform-splash-ring"><img src="{$logourl}" alt=""></div>';
  b.insertBefore(splash, b.firstChild);
  var minVisibleMs = 400;
  var shownAt = Date.now();
  function hide(){
    var wait = Math.max(0, minVisibleMs - (Date.now() - shownAt));
    window.setTimeout(function(){
      splash.classList.add('is-hidden');
      window.setTimeout(function(){ splash.remove(); }, 400);
    }, wait);
  }
  if (document.readyState === 'complete') { hide(); }
  else { window.addEventListener('load', hide); }
})();
</script>
<script>
(function(){
  var data = document.getElementById('platform-greeting-data');
  var block = document.querySelector('.block-myoverview');
  if (!data || !block || !block.parentNode) { return; }
  var name = data.textContent.trim();
  var greeting = document.createElement('div');
  greeting.className = 'platform-greeting';
  // El ícono (estático, sin datos del usuario) va por innerHTML; el nombre se
  // inserta aparte vía textContent para no mezclar HTML con un valor de la BD.
  greeting.innerHTML =
    '<span class="platform-greeting-icon">' +
      '<svg viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">' +
        '<defs><linearGradient id="platform-stetho-grad" x1="0" y1="0" x2="40" y2="40" gradientUnits="userSpaceOnUse">' +
          '<stop offset="0" stop-color="#134074"/><stop offset="1" stop-color="#3E92CC"/>' +
        '</linearGradient></defs>' +
        '<path d="M12 6V16C12 20 15 23 19 23C23 23 26 20 26 16V6" stroke="url(#platform-stetho-grad)" stroke-width="1.8" stroke-linecap="round"/>' +
        '<path d="M9 6C9 4.3 10.3 3 12 3C13.7 3 15 4.3 15 6" stroke="url(#platform-stetho-grad)" stroke-width="1.8" stroke-linecap="round"/>' +
        '<path d="M23 6C23 4.3 24.3 3 26 3C27.7 3 29 4.3 29 6" stroke="url(#platform-stetho-grad)" stroke-width="1.8" stroke-linecap="round"/>' +
        '<path d="M19 23V29" stroke="url(#platform-stetho-grad)" stroke-width="1.8" stroke-linecap="round"/>' +
        '<circle cx="19" cy="33" r="4.2" stroke="url(#platform-stetho-grad)" stroke-width="1.8"/>' +
        '<circle cx="30" cy="16" r="3" stroke="url(#platform-stetho-grad)" stroke-width="1.8"/>' +
      '</svg>' +
    '</span>' +
    '<span>' +
      '<span class="platform-greeting-title" style="display:block;"></span>' +
      '<span class="platform-greeting-subtitle" style="display:block;">Bem-vindo(a) de volta aos seus cursos.</span>' +
    '</span>';
  greeting.querySelector('.platform-greeting-title').textContent = 'Olá, ' + name + '!';
  block.parentNode.insertBefore(greeting, block);
})();
</script>
HTML;

set_config('additionalhtmlfooter', $footer);
cli_writeln('OK: additionalhtmlfooter actualizado (footer legal, constelaciones del login, splash y saludo en Meus cursos).');
cli_writeln('Recordá purgar cachés: php admin/cli/purge_caches.php');
