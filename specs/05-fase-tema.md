# SDD-05 — Fase 5: Tema Hijo + Portada con Tarjetas

> Prereq: Fases 1–4. ESD ref: `docs/06-portada-tema.md`.
> Regla clave: **no tocar el core** (`theme/boost/*` intacto).

---

### SDD-TEM-01 — Crear tema hijo `theme_plataforma` ✅ (2026-08-31)
- **Estructura real desplegada** (vía `scp`, no UI):
  ```
  theme/plataforma/
  ├── config.php
  ├── lib.php                      (callback de SCSS, ver hallazgo abajo)
  ├── version.php
  ├── classes/output/core_renderer.php   (override de logo, ver SDD-TEM-04)
  ├── lang/pt_br/theme_plataforma.php
  ├── scss/presets.scss
  ├── scss/_extra.scss
  └── pix/logo.png · favicon.ico · hero-bg.png
  ```
- **Instalado y activado**:
  ```bash
  php -d max_input_vars=5000 admin/cli/upgrade.php --non-interactive --allow-unstable
  php admin/cli/cfg.php --name=theme --set=plataforma   # --value= NO existe (ver Fase 2)
  ```
- ⚠️ **Bug real encontrado y corregido**: `$THEME->scss = ['presets', '_extra'];` (el
  original de la plantilla) **NO es válido en Moodle 4.5** — `$THEME->scss` debe ser un
  **callback** (`function($theme) { return ...; }`), no un array de nombres de archivo.
  Con el array, Moodle simplemente no compilaba nada custom y servía el CSS de boost sin
  ningún cambio — el tema "parecía" funcionar (cargaba sin error) pero **ninguna de
  nuestras clases (`.hero`, `.modulo-card`, etc.) llegaba nunca al CSS final**, desde el
  día en que se creó el esqueleto. Se detectó recién ahora, al tener imágenes reales para
  probar el hero y notar que no cambiaba nada visualmente. Corregido con un callback en
  `lib.php` (`theme_plataforma_get_main_scss_content()`) que arma: `presets.scss` (variables)
  + `theme/boost/scss/preset/default.scss` (contenido principal de boost) + `_extra.scss`
  (componentes propios) — mismo patrón que usa `theme_classic` (child oficial de boost).
- **Verificación**
  - `mdl_config.theme = 'plataforma'`.
  - CSS compilado descargado y confirmado: contiene `.hero{...}`, `.modulo-card`,
    `.embed-video`, `.modulos-grid` (antes: ninguna de estas clases existía en el CSS servido).
  - No se tocó ningún archivo de `theme/boost/*`.
- **DoD**
  - [x] Tema activo, padre boost, core no tocado.
  - [x] `lang/pt_br/theme_plataforma.php` con strings propias.

---

### SDD-TEM-02 — estilos de la portada (cards + hero) ✅ (2026-08-31, con rediseño completo)
- **Primera pasada** (fondo real del hero con `pix/hero-bg.png` + degradado overlay para
  legibilidad del texto): hecha y verificada.
- **Segunda pasada, el mismo día**: el desarrollador probó el sitio ("se ve mal") y pidió
  una identidad visual propia, no solo colores por defecto de Bootstrap. Se aplicó un
  sistema de diseño completo (se usó la skill `frontend-design` como guía):
  - **Paleta** (extraída del propio logo, no del azul de Bootstrap): `$ink #0B1F33`
    (navy — navbar/footer/texto), `$teal-deep #0F6C61` (acento primario), `$teal-spark
    #2BC7B4` (hover/foco — "sinapse"), `$bone #F6F4EF` (fondo cálido), `$slate #55697C`
    (texto secundario).
  - **Tipografía**: Space Grotesk (títulos), IBM Plex Sans (cuerpo), IBM Plex Mono
    (etiquetas — ej. "MOD 01" en las tarjetas). Cargadas vía Google Fonts en
    `$CFG->additionalhtmlhead` (no vía `@import` en SCSS).
  - **Firma visual**: un hilo teal degradado (`.synapse-rule`) bajo títulos de sección,
    eco discreto del mapa de nodos del logo — único elemento "llamativo", el resto del
    sistema deliberadamente contenido.
  - Navbar reskin oscuro, tarjetas de módulo con etiqueta monoespaciada y borde superior
    de acento al hover, footer oscuro con enlaces legales.
  - Nombre del sitio actualizado a **"NeuroIsbe — Portal de Estudos"** (antes
    "Plataforma de Estudos", placeholder) — resuelto `docs/13-info-necesaria.md` §1.
- ⚠️ **Hallazgo crítico de esta sesión, documentado en la skill `boost-theme-custom-global`**:
  Moodle **traga en silencio** cualquier error de compilación SCSS en producción
  (`debug=0`) y sirve el CSS precompilado de boost de fábrica sin avisar — costó una
  investigación larga (se llegó a sospechar un bug del pipeline de variables de Moodle)
  antes de encontrar la causa real: una sola línea con unidades incompatibles en Sass
  (`clamp(1.9rem, 3vw + 1rem, 2.75rem)` — Sass no puede sumar `vw` y `rem`). Corregido
  envolviendo en `calc()`. Dado que este error es completamente silencioso, **de ahí en
  adelante los overrides de clases Bootstrap (`.btn-primary`, `body`, `a`, navbar) se
  escriben directo sobre las clases ya renderizadas** en vez de depender de que las
  variables SCSS (`$primary`, `$font-family-sans-serif`, etc.) se propaguen correctamente
  a través de `theme/boost/scss/preset/default.scss` — más robusto y más fácil de
  verificar con `curl` sin depender de la cascada de `!default`.
- **Verificación**
  - Script de compilación aislado (sin silenciar excepciones) confirma `to_css()` sin error.
  - CSS servido por HTTP real contiene `#0F6C61` en `.btn-primary`, "Plex" y "Grotesk"
    en las declaraciones de `font-family`.
  - `<title>` de la portada: `NeuroIsbe` (antes `plataforma`).
  - **Falta confirmación visual** (Playwright/captura): la extensión de Chrome no se
    conectó en ningún intento durante esta sesión. Recomendado al desarrollador: hacer
    **hard refresh** (`Ctrl+Shift+R`) al revisar, porque el CSS se sirve con
    `Cache-Control: immutable, max-age=90 días` y el navegador pudo haber cacheado la
    versión fallida (CSS de boost sin estilos propios) de antes del fix.
- **DoD**
  - [x] Estilos SCSS compilables y aplicados, verificado por `curl` (colores/fuentes reales
        presentes en el CSS servido).
  - [ ] Confirmación visual — pendiente, extensión de Chrome no disponible en esta sesión.

---

### SDD-TEM-03 — Portada (frontpage) con tarjetas ⏸️ BLOQUEADA
> Bloqueada por SDD-EST-03 (Fase 3): no hay todavía cursos reales de módulos que listar
> en la grilla — solo existe la plantilla `mod00` (`visible=0`, no debe aparecer). Armar
> la portada ahora mostraría una grilla vacía o con datos inventados; se retoma en cuanto
> haya al menos un módulo real.
- **Pasos** (sin cambios respecto al plan original, pendientes de ejecutar):
  1. UI: *Site admin → Front page → Front page settings*: modo "A custom HTML page".
  2. HTML de la portada: hero + `<div class="modulos-grid">` con tarjetas por curso de la
     categoría `Módulos`.
  3. Generar tarjetas con `local_importcontenido_render_modulos_grid()` (ya existe en el
     plugin) o bloques nativos — **decisión pendiente**, ver nota en la skill
     `importar-contenido`.
  4. Enlaces de tarjetas → `/course/view.php?id=<courseid>`.
- **DoD**
  - [ ] Portada con tarjetas funcional y responsive — **no iniciado**.
  - [ ] Menú mínimo (logo, Módulos, Minha conta, Sair) y footer — el logo del navbar ya
        funciona (ver SDD-TEM-04), falta el resto del menú/footer.

---

### SDD-TEM-04 — Logo, favicon y footer ✅ (2026-08-31)
- **Imágenes recibidas del desarrollador** (carpeta `assets/`, 6 archivos): se identificaron
  por inspección visual (nombres genéricos `img1..6.png`, sin metadata). 2 de las 6
  (`img1`, `img2`) tenían el fondo transparente "quemado" como cuadrícula de ajedrez en
  vez de transparencia real (`RGB` sin canal alfa, confirmado con `file`/PowerShell
  `System.Drawing`) — no usables, descartadas con acuerdo del desarrollador. Usadas:
  `img3.png` (1254×1254, RGBA real) como logo/favicon; `img6.png` (2241×702) como fondo
  del hero (SDD-TEM-02).
- **Pasos ejecutados**:
  1. `pix/logo.png` (de `img3.png`) y `pix/favicon.ico` (generado con
     `convert img3.png -define icon:auto-resize=16,32,48,64 favicon.ico`, ImageMagick
     disponible en el servidor).
  2. ⚠️ **Segundo bug real de Moodle 4.5.13 encontrado**: configurar el logo vía
     `Site admin → Appearance → Logos` (o programáticamente vía `core_admin/logo`,
     `core_admin/logocompact`, `core_admin/favicon`) genera una URL rota —
     `moodle_url::make_pluginfile_url()` concatena la revisión del tema con el nombre de
     archivo **sin separador** (`.../favicon/64x64/1788188541favicon.png`), y
     `core_admin_pluginfile()` (`admin/lib.php`) no puede volver a separarlos al hacer
     `array_shift()` sobre los argumentos — el `filename` queda vacío y siempre da `404`.
     Confirmado que NO es un problema de este hosting: se reprodujo leyendo el código
     fuente completo (`make_pluginfile_url()`, `core_admin_pluginfile()`) y probando URLs
     con segmentos separados manualmente (esas sí funcionan). No se puede parchear el
     core. **Solución sin tocar core**: dejar `core_admin/favicon` vacío para que
     `core_renderer::favicon()` caiga en su propio fallback nativo
     (`image_url('favicon','theme')`, que sirve `pix/favicon.ico` por una ruta distinta
     que sí funciona); para el logo (que NO tiene ese fallback nativo) se creó un
     **renderer override** del tema (`classes/output/core_renderer.php`, mecanismo
     oficial de Moodle vía `$THEME->rendererfactory`, ya declarado en `config.php`) que
     sobreescribe `get_logo_url()`/`get_compact_logo_url()` para servir directo desde
     `pix/logo.png` del tema.
  3. Footer con enlaces a Aviso de Privacidade/Termos de Uso: hecho vía
     `$CFG->additionalhtmlfooter` (clase `.plataforma-footer`, ya estilada en `_extra.scss`),
     enlaza a `admin/tool/policy/view.php?versionid=1` (Aviso) y `versionid=2` (Termos).
- **Verificación**
  - `curl` a las URLs reales de favicon/logo/hero-bg (`theme/image.php/plataforma/...`)
    → `200` los tres.
  - HTML de la portada contiene `plataforma-footer` y los dos enlaces legales.
  - Verificación visual con Playwright/captura: **pendiente** — la extensión de Chrome no
    se conectó en ningún intento durante esta sesión; queda para confirmar apenas esté
    disponible.
- **DoD**
  - [x] Logo y favicon reales, visibles (verificado por `curl`, falta confirmación visual).
  - [x] Footer con enlaces legales.