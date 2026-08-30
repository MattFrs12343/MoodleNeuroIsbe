# CHANGELOG — Plataforma Educativa

> Registrar avances por fase (Spec-Driven Development). Actualizar con cada tarjeta SDD cerrada.

## Fase 0 — Variables fijas
- [ ] `specs/00-variables-fijas.md` completado (dominio, credenciales).

## Fase 1 — Infraestructura (SDD-INF-*)
- [ ] SDD-INF-01 verificable en hosting.

## Fase 2 — Configuración (SDD-CFG-*)
- [ ] (pendiente)

## Fase 3 — Estructura (SDD-EST-*)
- [ ] (pendiente)

## Fase 4 — Carga (SDD-CAR-*)
- [ ] (pendiente)

## Fase 5 — Tema/Portada (SDD-TEM-*)
- [ ] (pendiente)

## Fase 6 — QA/Producción (SDD-QA-*)
- [ ] (pendiente)

## Historial
| Fecha | Detalle |
|---|---|
| 2026-08-28 | Proyecto contratado: **Bs 6,000** (50% inicio + 50% entrega). Docs y SDD creados. |
| 2026-08-30 | Auditoría completa de `docs/`, `specs/`, `tasks/` y `src/`. Gaps completados: (1) creado `specs/plantillas/lista_ejemplo.csv` (faltaba, referenciado desde 3 archivos); (2) corregido bug en `src/local/importcontenido/cli/importar.php` (faltaba `require_once course/modlib.php`, `create_module()` habría fallado con fatal error); (3) corregido typo en `specs/04-fase-carga.md`; (4) creadas las 5 skills de `docs/14-mcps-skills.md` en `.claude/skills/` (T0.3); (5) creado `.mcp.json` (Playwright + MySQL, sin credenciales embebidas). Pendiente de decisión del desarrollador (no se improvisó): wiring de `local_importcontenido_render_modulos_grid()` (hoy no está conectada a ninguna página/filtro; v1 de `docs/06-portada-tema.md` opta por bloques nativos), assets de marca (`pix/logo.svg`/`favicon.ico`, bloqueados por `docs/13-info-necesaria.md` §5), e inicialización del repo Git (`CLAUDE.md` §3.5 exige permiso explícito antes de cualquier commit). |