# SDD-05 — Fase 5: Tema Hijo + Portada con Tarjetas

> Prereq: Fases 1–4. ESD ref: `docs/06-portada-tema.md`.
> Regla clave: **no tocar el core** (`theme/boost/*` intacto).

---

### SDD-TEM-01 — Crear tema hijo `theme_plataforma`
- **Estructura a crear**
  ```
  theme/plataforma/
  ├── config.php                  (padre = boost)
  ├── version.php
  ├── lang/pt_br/theme_plataforma.php
  ├── scss/presets.scss           (variables: paleta, fuentes)
  ├── scss/_extra.scss            (cards, hero, iframe 16:9)
  ├── templates/frontpage.mustache   (plantilla de portada si se usa render)
  └── pix/logo.svg · favicon.ico
  ```
- **`config.php` mínimno**
  ```php
  $THEME->name = 'plataforma';
  $THEME->parents = ['boost'];
  $THEME->sheets = [];
  $THEME->scss = ['presets', '_extra'];
  $THEME->enable_dock = false;
  $THEME->rendererfactory = 'theme_overridden_renderer_factory';
  ```
- **Instalar y activar**
  ```bash
  php admin/cli/upgrade.php --non-interactive --allow-unstable
  php admin/cli/cfg.php --name=theme --value=plataforma
  ```
- **Verificación**
  - `SELECT value FROM mdl_config WHERE name='theme';` → `plataforma`.
  - `theme/boost/*` intacto: `git status theme/boost` (si hay repo) vacío.
- **DoD**
  - [ ] Tema activo, padre boost (`$THEME->parents=['boost']`), core no tocado.
  - [ ] `lang/pt_br/theme_plataforma.php` con strings propias (UI en pt-BR).

---

### SDD-TEM-02 — estilios de la portada (cards + hero)
- **Pasos**
  1. En `_extra.scss`: definir
     - `.hero` (bloque de bienvenida con título + CTA "Entrar").
     - `.modulos-grid` (grid responsive: 1 col ≤576px, 2 cols 576–992, 3+ ≥992).
     - `.modulo-card` (imagen, título, resumen, botón "Acessar").
     - `.embed-responsive` 16:9 para iframes de video.
  2. Ajustar la paleta de `presets.scss` (colores del Dr./clínica).
- **Verificación**
  - CSS compilado (`php admin/cli/purge_caches.php`) sin errores SCSS.
- **DoD**
  - [ ] Estilos SCSS compilables y aplicados.

---

### SDD-TEM-03 — Portada (frontpage) con tarjetas
- **Pasos**
  1. UI: *Site admin → Front page → Front page settings*: modo "A custom HTML page" (página HTML personalizada).
  2. HTML de la portada: hero + `<div class="modulos-grid">` con tarjetas por curso de la categoría `Módulos`.
  3. Generar tarjetas de forma dinámica con el plugin `local_importcontenido` (extensión: `lib.php` expone un shortcode/tag para renderizar la grilla) o con bloques nativos (raw HTML) mientras tanto.
  4. Enlaces de tarjetas → `/course/view.php?id=<courseid>`.
- **Verificación**
  - Portada lista cada curso visible (comparar IDs en `mdl_course`).
  - Desde la portada, acceso al módulo en **≤ 2 clics**.
- **DoD**
  - [ ] Portada con tarjetas funcional y responsive.
  - [ ] Menú mínimo (logo, Módulos, Minha conta, Sair) y footer.

---

### SDD-TEM-04 — Logo, favicon y footer
- **Pasos**
  1. Subir `pix/logo.svg` y `pix/favicon.ico`.
  2. UI: *Site admin → Appearance → Theme selector → plataforma* → Logo/Favicon.
  3. Footer con: nombre, año, enlaces a Aviso de Privacidad y Términos (`docs/08`).
- **Verificación**
  - Logo/favicon visibles; footer con enlaces legales.
- **DoD**
  - [ ] Identidad visual completa en pt-BR.