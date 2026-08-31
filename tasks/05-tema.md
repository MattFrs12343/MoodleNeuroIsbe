# Fase 5 — Tema y Portada

> Spec: `specs/05-fase-tema.md` · ESD: `docs/06-portada-tema.md`
> Código base: `src/theme/plataforma` · Ejecutada parcialmente 2026-08-31 (2/4), con
> imágenes reales entregadas por el desarrollador.

**Progreso:** 🟡 50% (2/4) · **Estado:** T5.3 bloqueada por Fase 3/4 (sin cursos reales)

## ▶ Próxima tarea
T5.4 (footer + banner de cookies) puede avanzar ya. T5.3 (portada con tarjetas) espera a
que exista al menos un módulo real (SDD-EST-03).

---

## T5.1 · Copiar y activar tema hijo (SDD-TEM-01)
Estado: ✅
- [x] Copiado `src/theme/plataforma` → `theme/plataforma` (vía `scp`)
- [x] `upgrade.php` + `cfg.php --name=theme --set=plataforma` (no `--value=`)
- [x] `theme/boost` intacto (no se tocó ningún archivo de core)
- **Bug real corregido**: `$THEME->scss` como array de nombres no es válido en Moodle
  4.5 — debe ser un callback. Sin este fix, el SCSS propio nunca se compilaba (el tema
  llevaba desde su creación sirviendo CSS de boost sin ningún cambio). Se agregó
  `lib.php` con `theme_plataforma_get_main_scss_content()`.
- Verificación: `mdl_config.theme=plataforma`; CSS compilado contiene `.hero`,
  `.modulo-card`, etc. (antes no contenía ninguna)

## T5.2 · Estilos SCSS (SDD-TEM-02)
Estado: ✅
- [x] `.hero` con imagen de fondo real (`pix/hero-bg.png`, de `img6.png`) + overlay de
      degradado para legibilidad del texto
- [x] `purge_caches` sin errores SCSS
- Verificación: `theme/image.php/.../hero-bg` → `200`; CSS compilado con la regla correcta

## T5.3 · Portada con tarjetas (SDD-TEM-03)
Estado: ⏸️ BLOQUEADA
- [ ] Frontpage "página HTML" con grilla dinámica — **falta al menos un módulo real**
      (SDD-EST-03, Fase 3, bloqueada por datos del Dr.)
- [ ] Decisión pendiente: shortcode del plugin vs. bloques nativos (ver skill
      `importar-contenido`)

## T5.4 · Logo, favicon, footer (SDD-TEM-04)
Estado: 🟡 logo/favicon hechos, falta footer y banner de cookies
- [x] Logo real (`img3.png`) y favicon (`.ico` generado con ImageMagick) — verificados
      por `curl` (`200`), pendiente confirmación visual (extensión de Chrome no conectada
      en el momento de esta tarjeta)
- **Segundo bug real de Moodle 4.5.13 encontrado y corregido**: el mecanismo estándar de
  logo/favicon vía `Site admin → Appearance → Logos` genera URLs rotas
  (`core_admin_pluginfile()` no puede parsear el nombre de archivo concatenado con la
  revisión del tema — siempre `404`, confirmado leyendo el código fuente, no es cosa del
  hosting). Favicon resuelto dejando `core_admin/favicon` vacío para que caiga en el
  fallback nativo de Moodle (`pix/favicon.ico` del tema). Logo resuelto con un
  **renderer override** propio (`classes/output/core_renderer.php`, mecanismo oficial de
  temas hijos) que sirve `pix/logo.png` directo, sin pasar por el mecanismo roto.
- [ ] Footer con enlaces a Aviso de Privacidade/Termos de Uso — pendiente
- [ ] **Banner de cookies** (diferido de T2.5, `specs/02-fase-config.md`): Moodle 4.5 no
      trae uno nativo; implementar con CSS/JS ligero en el tema (`_extra.scss` +
      pequeño script), "Aceitar / Recusar", enlazando al Aviso de Privacidade
      (`admin/tool/policy/view.php?versionid=1`)
- Verificación: `curl` a las 3 imágenes → `200`; captura visual pendiente
