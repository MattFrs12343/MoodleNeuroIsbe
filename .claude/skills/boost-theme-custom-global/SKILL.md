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
