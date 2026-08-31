# Tablero Maestro — Estado del Proyecto

> Actualizar siempre que se avance/termine una tarea.
> Fecha de inicio: 2026-08-28 · Contrato: **Bs 6,000** (50% arranque + 50% entrega)

---

## ▶ PUNTO DE CONTINUIDAD (LEER PRIMERO)

> **Fases 1, 2 HECHAS. Fase 5 al 75% (3/4), con identidad visual completa "NeuroIsbe".**
> El desarrollador dijo que el sitio "se veía mal" y pidió personalización estética real
> (skill `frontend-design`): paleta propia (navy+teal del logo), tipografía Space
> Grotesk/IBM Plex, hero con imagen real, navbar/footer oscuros, nombre del sitio
> actualizado a "NeuroIsbe — Portal de Estudos". Fase 3 sigue bloqueada en T3.3 (falta
> número/orden real de módulos del Dr.) → Fase 4 y SDD-TEM-03 (portada con tarjetas)
> dependen de eso. **Tres bugs reales de Moodle 4.5.13 encontrados y corregidos sin
> tocar core** (ver `specs/05-fase-tema.md` y skill `boost-theme-custom-global`):
> (1) `$THEME->scss` como array no compilaba nada custom desde el origen del tema;
> (2) logo/favicon vía Site admin generan URLs rotas; (3) **Moodle traga en silencio
> cualquier error de compilación SCSS en producción** (una unidad incompatible en un
> `clamp()` causó horas de investigación antes de encontrar la causa real). Falta
> confirmación **visual** (Playwright/captura) — la extensión de Chrome no se conectó en
> ningún intento. **Recomendar al desarrollador un hard refresh (Ctrl+Shift+R)** al
> revisar, por el `Cache-Control: immutable` del CSS. Sigue pendiente del Dr.: acceso
> YouTube/Dropbox, conteos de contenido — ver `docs/13-info-necesaria.md` §2, §3.

---

## Progreso por fase

| Fase | Archivo | Progreso | Estado |
|---|---|---|---|
| 0 · Datos y setup | `docs/13-info-necesaria.md` | 🟡 Parcial | Falta: contenido, diseño, YouTube/Dropbox del Dr. |
| 1 · Infraestructura | [01-infra.md](01-infra.md) | ✅ 100% (7/7) | HECHO (2026-08-30, sobre hosting real) |
| 2 · Configuración base | [02-config.md](02-config.md) | ✅ 100% (6/6) | HECHO (banner de cookies diferido a Fase 5, es trabajo de tema) |
| 3 · Estructura | [03-estructura.md](03-estructura.md) | 🟡 63% (2.5/4) | **BLOQUEADA** en T3.3 — falta número/orden real de módulos del Dr. (§3) |
| 4 · Carga de contenido | [04-carga.md](04-carga.md) | ⬜ 0% | PENDIENTE (bloqueada por T3.3 + contenido/YouTube/Dropbox del Dr.) |
| 5 · Tema y portada | [05-tema.md](05-tema.md) | 🟡 75% (3/4) | Identidad visual completa (NeuroIsbe); solo falta la portada con tarjetas (Fase 3/4) |
| 6 · QA / producción | [06-qa.md](06-qa.md) | 🟡 33% (3/9) | T6.1 (parcial)/T6.3/T6.5 adelantadas 2026-08-31; resto pendiente |

## Tareas de setup (Fase 0)

| # | Tarea | Subtareas | Estado |
|---|---|---|---|
| T0.1 | Completar `docs/13-info-necesaria.md` | [x] Licencia (§4) · [x] Dominio (§1, nombre "NeuroIsbe" incluido) · [x] Hosting técnico (§6) · [x] Acceso/BD (§2, falta YouTube/Dropbox) · [x] Diseño (§5, logo/imágenes recibidas y aplicadas) · [ ] Contenido (§3, número de módulos) | 🟡 PARCIAL (solo falta §3 y YouTube/Dropbox de §2) |
| T0.2 | Volcar respuestas a `specs/00-variables-fijas.md` | [x] generar · [x] llenar (dominio, BD, PHP, dataroot — todo lo verificado en Fase 1) | ✅ HECHO (para lo disponible) |
| T0.3 | Instalar MCP + skills (`docs/14-mcps-skills.md`) | [x] `.mcp.json` creado · [x] Playwright verificado · [x] MySQL verificado (`@benborla29/mcp-server-mysql`) · [x] 5 skills en `.claude/skills/` · [ ] conexión SQL real desde el MCP local — **limitación real**: MySQL del hosting solo escucha en `localhost` del servidor remoto, no expuesto a internet; el MCP corre en tu máquina, así que necesitaría un túnel SSH (`ssh -L 3306:localhost:3306 matiasf6@sh006.hostgator.net`) para conectar. Mientras tanto, verificación SQL se hace por `ssh ... mysql -e "..."` directo (así se hizo en toda la Fase 1) | ⏳ EN CURSO |
| T0.4 | Inicializar Git y tags por fase | [x] `git init` + primer commit + push a `origin/main` (2026-08-30) · [ ] tags por fase (`v1-infra`, ...) | ⏳ EN CURSO |

## Cierre de fase

| Fase | CHANGELOG ✔ |
|---|---|
| 0 | ☐ |
| 1 | ☑ |
| 2 | ☑ HECHO |
| 3 | ☑ (parcial: 2.5/4, bloqueada en T3.3) |
| 4 | ☐ |
| 5 | ☑ (parcial: 2/4) |
| 6 | ☑ (parcial: 3/9, T6.1/T6.3/T6.5 adelantadas) |