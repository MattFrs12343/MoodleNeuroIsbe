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
- `src/theme/plataforma/config.php` — `$THEME->parents = ['boost']`, declara `$THEME->scss`.
- `src/theme/plataforma/scss/presets.scss` — paleta y tipografía (variables SCSS).
- `src/theme/plataforma/scss/_extra.scss` — componentes propios: `.hero`, `.modulos-grid`,
  `.modulo-card`, `.embed-video` (iframe 16:9).
- `src/theme/plataforma/lang/pt_br/theme_plataforma.php` — strings propias, siempre pt-BR.
- `src/theme/plataforma/pix/` — logo/favicon (pendiente del Dr., ver `docs/13-info-necesaria.md` §5).

## Flujo de edición segura
1. Editar SCSS/config en `src/theme/plataforma/`.
2. Copiar al `theme/plataforma/` del Moodle real (staging primero, nunca producción directa).
3. `php admin/cli/purge_caches.php` para recompilar SCSS.
4. Verificar visualmente con Playwright en **380px / 768px / 1280px** (móvil/tablet/desktop),
   comparando captura ANTES/DESPUÉS (`docs/15-prevencion-regresiones.md` §7).
5. Confirmar que `theme/boost` sigue intacto (`git status theme/boost` si hay repo).

## Checklist de aceptación (TC-F de `docs/06-portada-tema.md`)
- Grilla de módulos visible y con tarjetas (imagen, título, resumen, botón "Acessar").
- Responsive sin scroll horizontal en los 3 breakpoints.
- Cero strings en inglés en portada/menú.
- Clic en tarjeta → `/course/view.php?id=<ID>`.
