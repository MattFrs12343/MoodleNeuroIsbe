# CHANGELOG — Plataforma Educativa

> Registrar avances por fase (Spec-Driven Development). Actualizar con cada tarjeta SDD cerrada.

## Fase 0 — Variables fijas
- [ ] `specs/00-variables-fijas.md` completado (dominio, credenciales).

## Fase 1 — Infraestructura (SDD-INF-*)
- [x] SDD-INF-01..07 verificadas en hosting real (`portal.examenes-neuro.com`), 2026-08-30.
  Detalle y desajustes reales vs. plantilla genérica: `specs/01-fase-infra.md`.

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
| 2026-08-30 | Auditoría completa de `docs/`, `specs/`, `tasks/` y `src/`. Gaps completados: (1) creado `specs/plantillas/lista_ejemplo.csv` (faltaba, referenciado desde 3 archivos); (2) corregido bug en `src/local/importcontenido/cli/importar.php` (faltaba `require_once course/modlib.php`, `create_module()` habría fallado con fatal error); (3) corregido typo en `specs/04-fase-carga.md`; (4) creadas las 5 skills de `docs/14-mcps-skills.md` en `.claude/skills/` (T0.3); (5) creado `.mcp.json` (Playwright + MySQL, sin credenciales embebidas). Pendiente de decisión del desarrollador (no se improvisó): wiring de `local_importcontenido_render_modulos_grid()` (hoy no está conectada a ninguna página/filtro; v1 de `docs/06-portada-tema.md` opta por bloques nativos), assets de marca (`pix/logo.svg`/`favicon.ico`, bloqueados por `docs/13-info-necesaria.md` §5). Confirmado con el desarrollador: mantener la decisión v1 de portada (bloques nativos) tal cual, sin código nuevo por ahora. |
| 2026-08-30 | Git inicializado (`git init`), primer commit con el estado completo del repo (53 archivos) y push a `origin/main` (`https://github.com/MattFrs12343/MoodleNeuroIsbe.git`, repo remoto estaba vacío). Se agregó `.gitignore`. T0.4 pasa a EN CURSO (faltan tags por fase). |
| 2026-08-30 | Instalación de MCP + skills (T0.3): verificado que el entorno tiene Node/npm/npx pero NO Python/`uv`. Por eso se cambió el MCP de MySQL de `uvx mcp-server-mysql` (Python) a `@benborla29/mcp-server-mysql` (Node, vía `npx`) — mismo contrato de variables de entorno (`MYSQL_HOST/PORT/USER/PASS/DB`), decisión confirmada con el desarrollador. Ambos paquetes (`@playwright/mcp@0.0.79` y `@benborla29/mcp-server-mysql@2.0.9`) verificados como resolubles en el registro npm. Pendiente real (no de instalación): probar contra un Moodle/BD real, que no existe hasta Fase 1. `docs/14-mcps-skills.md` actualizado con la decisión y el checklist real. |
| 2026-08-30 | Decisión bloqueante resuelta (`docs/13-info-necesaria.md` #4): la licencia del curso comprado por el Dr. es de uso personal, un solo usuario. Esto cambia ADR-4 (`docs/00-maestro.md`): ya no hay auto-registro publico. Actualizados en cascada para eliminar la contradiccion: `docs/02-autenticacion.md` (self-registration OFF, cuenta unica creada por el admin, TC-A-01/DoD/riesgos reescritos), `specs/02-fase-config.md` (SDD-CFG-02 reescrita para deshabilitar self-registration en vez de activarlo con captcha; SDD-CFG-06 ajustada), `docs/08-legal-lgpd.md` (RF-L-08 y DoD marcados resueltos), `specs/00-variables-fijas.md` #7. Sigue pendiente el resto de `docs/13-info-necesaria.md` (dominio, credenciales, contenido, diseno, hosting); Fase 0 sigue bloqueada hasta completarlo. |
| 2026-08-30 | **Fase 1 (Infraestructura) completada y verificada sobre hosting real.** Dominio confirmado por el desarrollador: `portal.examenes-neuro.com`, acceso SSH a `matiasf6@sh006.hostgator.net`. Cuenta compartida con otros proyectos en produccion (`matiasf6_cuestionario_bd`, `matiasf6_pedidos_db`, varios subdominios de `examenes-neuro.com`) — todo el trabajo se aislo estrictamente a `portal.examenes-neuro.com` y `matiasf6_moodle_*`, sin tocar nada mas. Ejecutado por SSH via `uapi` (sin UI de cPanel): PHP 8.2 fijado solo para el subdominio; BD `matiasf6_moodle_db` + usuario creados (nota: `uapi Mysql setup_db_and_user` genera nombres aleatorios propios y se descarto tras probarlo y limpiarlo; se uso `create_database`/`create_user`/`set_privileges_on_database` con nombres explicitos); Moodle **4.5.13+ (405, stable)** descargado e instalado por CLI; idioma pt_br instalado automaticamente por el instalador. Tres desajustes reales encontrados y corregidos vs. la plantilla generica (detalle en `specs/01-fase-infra.md` y `specs/00-variables-fijas.md` #2): (1) el motor de BD real es **MySQL 8.0**, no MariaDB, por lo que se uso `--dbtype=mysqli`; (2) `$CFG->directorypermissions` se bajo de `02777` a `0700` (choque con umask del sistema, y ademas innecesario porque PHP corre como el propio usuario de la cuenta); (3) `$CFG->localrequestdir` tuvo que redirigirse a un directorio propio dentro de `dataroot`, porque el default de Moodle (`/tmp/requestdir`) esta "contaminado" por otra cuenta cPanel en el `/tmp` compartido del servidor (permisos `777` aparentes por `ls`, pero acceso real denegado). Cron programado cada minuto via `crontab` de usuario (esta cuenta no expone modulo `Cron` en `uapi`) y verificado sin errores. Sitio responde `200 OK` por HTTPS. Login temporal usado para el admin: email `dev@somoscdv.com` (del desarrollador, no del Dr. — cambiar cuando se confirme, ver `docs/13-info-necesaria.md` #2) y nombre de sitio "Plataforma de Estudos" (cambiar cuando el Dr. confirme el nombre final, `docs/13-info-necesaria.md` #1). Actualizados: `specs/01-fase-infra.md` (las 7 tarjetas SDD-INF cerradas con el detalle real), `docs/01-infraestructura.md` (DoD), `tasks/01-infra.md`, `tasks/00-estado-general.md` (punto de continuidad pasa a Fase 2), `docs/13-info-necesaria.md` (secciones 1, 2 parcial y 6 resueltas). |