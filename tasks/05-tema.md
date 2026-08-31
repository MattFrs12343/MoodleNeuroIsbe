# Fase 5 — Tema y Portada

> Spec: `specs/05-fase-tema.md` · ESD: `docs/06-portada-tema.md`
> Código base: `src/theme/plataforma` (skeleton) · Bloqueante: Fase 4 (para probar tarjetas con contenido).

**Progreso:** ⬜ 0% (0/4) · **Estado:** PENDIENTE

## ▶ Próxima tarea
T5.1 (copiar skeleton y activar) — puede adelantarse tras Fase 1 para pruebas.

---

## T5.1 · Copiar y activar tema hijo (SDD-TEM-01)
Estado: ⬜
- [ ] Copiar `src/theme/plataforma` → `theme/plataforma`
- [ ] `upgrade.php` + `cfg.php theme=plataforma`
- [ ] `theme/boost` intacto (verificado)
- Verificación: SELECT mdl_config theme=plataforma

## T5.2 · Estilos SCSS (SDD-TEM-02)
Estado: ⬜
- [ ] presets (paleta) + _extra (hero, tarjetas, iframe 16:9)
- [ ] `purge_caches` sin errores SCSS
- Verificación: portada con estilos propios

## T5.3 · Portada con tarjetas (SDD-TEM-03)
Estado: ⬜
- [ ] Frontpage "página HTML" con grilla dinámica (shortcode del plugin o bloques)
- [ ] Enlaces → /course/view.php?id=.. · acceso ≤ 2 clics
- Verificación: TC-F-01..07 (Playwright adecuado)

## T5.4 · Logo, favicon, footer (SDD-TEM-04)
Estado: ⬜
- [ ] Logo real del Dr., favicon, footer con aviso/términos
- [ ] **Banner de cookies** (diferido de T2.5, `specs/02-fase-config.md`): Moodle 4.5 no
      trae uno nativo; implementar con CSS/JS ligero en el tema (`_extra.scss` +
      pequeño script), "Aceitar / Recusar", enlazando al Aviso de Privacidade
      (`admin/tool/policy/view.php?versionid=1`)
- Verificación: captura visual