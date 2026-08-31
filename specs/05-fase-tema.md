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

### SDD-TEM-02 — estilos de la portada (cards + hero) ✅ (2026-08-31)
- **Ejecutado**: `_extra.scss` ya traía `.hero`, `.modulos-grid`, `.modulo-card`,
  `.embed-video` (16:9) desde el esqueleto original; lo que faltaba (y se agregó ahora)
  fue el **fondo real del hero**, con las imágenes que pasó el desarrollador:
  ```scss
  .hero {
      background:
          linear-gradient(90deg, rgba(16,42,67,.90) 0%, rgba(21,101,192,.55) 45%, rgba(21,101,192,.15) 100%),
          url([[pix:theme|hero-bg]]) center right / cover no-repeat;
      color: $light;
      ...
  }
  ```
  El degradado queda como overlay para que el título/CTA (a la izquierda) sigan legibles
  sobre la ilustración (a la derecha, en `pix/hero-bg.png`).
- **Verificación**
  - `purge_caches.php` sin errores.
  - CSS compilado confirmado con la regla `.hero` incluyendo la imagen real, y
    `theme/image.php/.../hero-bg` responde `200`.
- **DoD**
  - [x] Estilos SCSS compilables y aplicados (una vez corregido el bug de SDD-TEM-01, sin
        el cual esta tarjeta nunca se habría reflejado en el sitio).

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

### SDD-TEM-04 — Logo, favicon y footer 🟡 logo/favicon hechos, footer pendiente (2026-08-31)
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
  3. Footer con enlaces a Aviso de Privacidade/Termos de Uso: **no ejecutado todavía**.
- **Verificación**
  - `curl` a las URLs reales de favicon/logo/hero-bg (`theme/image.php/plataforma/...`)
    → `200` los tres.
  - Verificación visual con Playwright/captura: **pendiente** — la extensión de Chrome no
    estaba conectada en el momento de esta tarjeta; queda para confirmar en una sesión
    donde sí lo esté.
- **DoD**
  - [x] Logo y favicon reales, visibles (verificado por `curl`, falta confirmación visual).
  - [ ] Footer con enlaces legales — pendiente.