---
name: boost-theme-custom-global
description: Reglas para cualquier tarea de frontend (tema hijo theme_plataforma, tarjetas, hero, iframe de video, SCSS, responsive). Usar en toda la Fase 5 y en cualquier cambio visual posterior.
---

# Personalización del tema (child de Boost)

> Referencia: `docs/06-portada-tema.md`, `specs/05-fase-tema.md`, `CLAUDE.md` §3.1.

## Regla de oro
**NUNCA** editar `theme/boost/*` en el Moodle desplegado. Todo cambio visual va en el tema
hijo `theme/plataforma` (fuente versionada en `src/theme/plataforma/`, se copia/sincroniza
al Moodle real en el servidor).

## Dónde vive cada cosa
- `src/theme/plataforma/config.php` — `$THEME->parents = ['boost']`, `$THEME->scss` debe
  ser un **callback**, no un array de nombres (ver "Errores silenciosos" abajo).
- `src/theme/plataforma/lib.php` — `theme_plataforma_get_main_scss_content()`: arma
  presets + `theme/boost/scss/preset/default.scss` + `_extra.scss`.
- `src/theme/plataforma/classes/output/core_renderer.php` — override de
  `get_logo_url()`/`get_compact_logo_url()` (necesario, ver "Logo/favicon rotos" abajo).
- `src/theme/plataforma/scss/presets.scss` — paleta NeuroIsbe (`$ink`, `$teal-deep`,
  `$teal-spark`, `$bone`, `$slate`) y tipografía (`$font-family-display` = Space Grotesk,
  `$font-family-sans-serif` = IBM Plex Sans, `$font-family-monospace` = IBM Plex Mono).
- `src/theme/plataforma/scss/_extra.scss` — componentes propios (`.hero`, `.modulos-grid`,
  `.modulo-card`, `.embed-video`) **y overrides directos** de clases Bootstrap
  (`.btn-primary`, `body`, `a`, `.navbar`) — ver por qué son directos, no vía variables,
  en "Errores silenciosos" abajo.
- `src/theme/plataforma/pix/` — `logo.png`, `favicon.ico`, `hero-bg.png` (recibidos del Dr.
  2026-08-31, ver `docs/13-info-necesaria.md` §5 — ya resuelto).
- Google Fonts: cargadas vía `$CFG->additionalhtmlhead` (Site admin → Appearance →
  Additional HTML), no vía `@import` en SCSS.
- Footer legal: vía `$CFG->additionalhtmlfooter`.

## ⚠️ Errores silenciosos de SCSS — la trampa más costosa de este tema
En producción (`debug=0`) **cualquier error de compilación SCSS se traga en silencio** y
Moodle sirve el CSS precompilado de boost de fábrica (`theme/boost/style/moodle.css`) sin
avisar nada — la página carga con `200 OK`, el sitio "funciona", pero **ninguno de los
estilos propios se aplica** y parece que el tema nunca cambió. Esto costó una sesión
entera de investigación (2026-08-31) antes de encontrar que la causa era una sola línea:
`font-size: clamp(1.9rem, 3vw + 1rem, 2.75rem);` — Sass intenta sumar `vw` y `rem` en
tiempo de compilación (unidades incompatibles) y lanza `CompilerException`, silenciada por
`theme_config::get_css_content_from_scss()`. **Antes de asumir que "las variables no se
propagan" o inventar teorías sobre el pipeline de Moodle, verificar primero si hay un
error de compilación real** con este script (más rápido que cualquier otra hipótesis):
```php
// CLI, con require(__DIR__.'/config.php') ya cargado:
$theme = theme_config::load('plataforma');
[$paths, $scss] = $theme->get_scss_property();
$compiler = new core_scss();
$compiler->prepend_raw_scss($theme->get_pre_scss_code());
$compiler->append_raw_scss($scss($theme));
$compiler->setImportPaths($paths);
$compiler->append_raw_scss($theme->get_extra_scss_code());
try { $compiler->to_css(); echo "OK\n"; }
catch (\Throwable $e) { echo $e->getMessage() . "\n"; } // el error REAL, sin silenciar
```
Si esto da error, **arreglar el SCSS** (evitar mezclar unidades en `clamp()`/`calc()` sin
envolver en `calc()`; scssphp no siempre trata `clamp()` igual que `calc()`). Si compila
bien pero las variables Bootstrap ($primary, $font-family-sans-serif, etc.) igual no se
reflejan, **no perder tiempo peleando la cascada de `!default`**: escribir los overrides
directo sobre la clase ya renderizada en `_extra.scss` (`.btn-primary { background-color:
$teal-deep !important; }`, etc.) — funciona siempre, es el patrón ya usado en este tema.

## ⚠️ Boost puede "ganar por default" en propiedades que nunca tocaste
No alcanza con verificar que las clases PROPIAS aparecen en el CSS servido — hay que
revisar si una regla de **Boost** sobre el mismo selector puede estar aplicando una
propiedad que nuestro código nunca declaró. Caso real (login con fondo de
"constelaciones", 2026-08-31): se puso un fondo navy en `body.pagelayout-login` y un
`<canvas>` detrás (`z-index:0`), con `#page` encima (`z-index:1`) sólo para posicionar
(`position/display/align-items/…`) — **sin declarar `background`**. Boost trae su propia
regla `.pagelayout-login #page{background:#f8f9fa; background-image:linear-gradient(...)}`
(gris claro), y como nadie competía por esa propiedad, ganaba sin pelea y tapaba TODO
(el navy del body y el canvas, ambos por detrás). El síntoma reportado fue vago ("se ve
mal"), no un error de compilación — antes de rediseñar de nuevo, comparar el **CSS
servido real** (no el propio) contra cada contenedor de la cadena DOM (`#page` →
`#page-content` → `#region-main-box` → `#region-main` → contenido), buscando
`background`/`background-color`/`background-image` que Boost declare y uno no haya
neutralizado explícitamente (`background: transparent !important` si hace falta).

## ⚠️ Cuando razonar sobre CSS estático no alcanza: Chrome headless + CDP crudo
Si la extensión de Chrome no conecta (pasa siempre en este entorno) y ya van 2-3 rondas
de "arreglé esto pero se sigue viendo mal" sin confirmación visual, **dejar de razonar
sobre el CSS servido a ciegas** y conseguir evidencia real de navegador:
```powershell
# Captura real (no necesita la extensión de Chrome ni Puppeteer):
& "C:\Program Files\Google\Chrome\Application\chrome.exe" --headless=new --disable-gpu `
  --no-sandbox --window-size=1440,1000 --screenshot="$env:TEMP\shot.png" `
  --virtual-time-budget=4000 "https://sitio.real/pagina"
```
Para medir DOM/CSS con precisión (ancho real, qué regla está ganando, etc.), usar el
Chrome DevTools Protocol directo desde Node 22+ (tiene `fetch`/`WebSocket` nativos, no
hace falta instalar Puppeteer):
1. `chrome.exe --headless=new --remote-debugging-port=9333 --user-data-dir=<tmp>`
2. `PUT http://localhost:9333/json/new?<url>` → da `webSocketDebuggerUrl`.
3. Conectar por `WebSocket`, `Page.enable` + esperar `Page.loadEventFired`, esperar
   ~2s más (para que corran los módulos AMD tipo `core/togglesensitive`).
4. `Runtime.evaluate` con `getBoundingClientRect()`/`getComputedStyle()` para medir de
   verdad (no asumir), o `DOM.querySelector` + `CSS.getMatchedStylesForNode` para listar
   **todas** las reglas CSS que compiten por una propiedad — esto encontró en minutos una
   regla vieja de compatibilidad YUI (`input[type="text"]{width:12.25em}`, más específica
   que `.form-control{width:100%}` de Bootstrap) que 3 rondas de inspección manual del
   CSS servido no habían detectado. Bug real 2026-08-31: dos inputs del login con el
   mismo `.form-control-lg` medían 196px vs 328px porque esa regla vieja le ganaba a
   Bootstrap en uno de los dos (el otro escapaba por tener un wrapper con más
   especificidad, por casualidad, no por diseño). Corregido con `width:100%!important`
   explícito. Moraleja: medir con `getBoundingClientRect` en vez de comparar HTML/CSS a
   ojo ahorra rondas enteras de intentos fallidos.

## 🔧 Cuando la extensión de Chrome no conecta: Chrome headless local + CDP crudo
Si tras 2-3 rondas de fixes "a ciegas" (razonando sobre el CSS servido, sin ver el
renderizado real) el desarrollador sigue reportando el mismo problema visual, **dejar de
adivinar y medir de verdad**. En Windows con Chrome instalado, no hace falta la extensión:
```powershell
# Captura de pantalla real de la página (sirve para ver de un vistazo si algo cambió).
& "C:\Program Files\Google\Chrome\Application\chrome.exe" --headless=new --disable-gpu `
  --no-sandbox --window-size=1440,1000 --screenshot="$env:TEMP\shot.png" `
  --virtual-time-budget=4000 "https://el-sitio/pagina.php"
```
Para medir DOM/CSS con precisión (`getBoundingClientRect`, `getComputedStyle`,
`CSS.getMatchedStylesForNode` para ver **todas** las reglas que compiten por una
propiedad, en orden y con su especificidad) — Node 22+ trae `fetch`/`WebSocket` nativos,
no hace falta instalar Puppeteer:
```powershell
& "C:\Program Files\Google\Chrome\Application\chrome.exe" --headless=new --disable-gpu `
  --remote-debugging-port=9333 --window-size=1440,1000 `
  --user-data-dir="$env:TEMP\chrome-profile" -WindowStyle Hidden
```
```js
// medir.mjs — abrir tab, esperar load + tiempo para JS async (AMD/togglesensitive/etc.),
// y correr cualquier expresión JS vía Runtime.evaluate.
const targ = await (await fetch("http://localhost:9333/json/new?URL", {method:"PUT"})).json();
const ws = new WebSocket(targ.webSocketDebuggerUrl);
// ... Page.enable, esperar Page.loadEventFired, setTimeout ~2500ms, Runtime.evaluate
```
Caso real donde esto fue decisivo (2026-08-31, "el input de usuario sigue sin el mismo
tamaño que el de senha", 3 intentos fallidos razonando el CSS a ciegas): medir con
`getBoundingClientRect()` reveló que un input medía 196px y el otro 328px con el MISMO
contenedor padre — y `CSS.getMatchedStylesForNode` mostró la causa real en segundos: una
regla vieja de Moodle (`input[type="text"]{width:12.25em}`, rastro de compatibilidad YUI)
con más especificidad que `.form-control{width:100%}` de Bootstrap, ganando en un campo
sí y en el otro no según qué otro selector competía. Sin esta técnica, se podría haber
seguido iterando CSS a ciegas indefinidamente.

## ⚠️ Logo/favicon vía Site admin están rotos en esta versión (Moodle 4.5.13)
`Site admin → Appearance → Logos` genera URLs que **siempre dan 404**:
`moodle_url::make_pluginfile_url()` concatena la revisión del tema con el nombre de
archivo sin separador (`.../favicon/64x64/{rev}{filename}`), y
`core_admin_pluginfile()` (`admin/lib.php`) no puede volver a separarlos al hacer
`array_shift()` sobre los argumentos. No usar ese mecanismo. En su lugar:
- **Favicon**: dejar `core_admin/favicon` vacío (`unset_config('favicon','core_admin')`)
  para que `core_renderer::favicon()` caiga en su fallback nativo — sirve
  `pix/favicon.ico` del tema por una ruta distinta que sí funciona. Debe ser
  literalmente `pix/favicon.ico` (Moodle busca ese nombre exacto, no busca extensiones
  alternativas para favicon como sí hace con otras imágenes de tema).
- **Logo**: no tiene fallback nativo — se resuelve con el renderer override en
  `classes/output/core_renderer.php` (ya implementado), que sirve `pix/logo.png` directo
  vía `$this->image_url('logo', 'theme')`.

## Flujo de edición segura
1. Editar SCSS/config en `src/theme/plataforma/`.
2. `scp` al `theme/plataforma/` del Moodle real (no hay staging separado en este hosting;
   extremar cuidado, ver `docs/15-prevencion-regresiones.md`).
3. `php admin/cli/purge_caches.php` para recompilar SCSS.
4. **Verificar que realmente compiló** (ver script de arriba) antes de asumir que un
   cambio visual "no se ve" por otra razón — es la causa más probable.
5. Verificar visualmente con Playwright en **380px / 768px / 1280px** (móvil/tablet/desktop),
   comparando captura ANTES/DESPUÉS (`docs/15-prevencion-regresiones.md` §7). Si la
   extensión de Chrome no está conectada, verificar al menos por `curl` que el CSS
   compilado contiene las clases/colores/fuentes esperadas.
6. Confirmar que `theme/boost` sigue intacto (no se edita nunca directamente).

## Checklist de aceptación (TC-F de `docs/06-portada-tema.md`)
- Grilla de módulos visible y con tarjetas (imagen, título, resumen, botón "Acessar").
- Responsive sin scroll horizontal en los 3 breakpoints.
- Cero strings en inglés en portada/menú.
- Clic en tarjeta → `/course/view.php?id=<ID>`.
