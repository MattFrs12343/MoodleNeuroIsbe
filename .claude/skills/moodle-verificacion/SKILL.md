---
name: moodle-verificacion
description: Verificación estándar al cerrar el DoD de cualquier tarjeta SDD-* o al diagnosticar un error de Moodle en esta plataforma. Usar siempre antes de marcar una tarjeta como hecha.
---

# Verificación Moodle — Plataforma Educativa

> Referencia: `CLAUDE.md` §3.4 (verificación obligatoria) y `docs/15-prevencion-regresiones.md`.
> Ninguna tarjeta SDD se marca ☑ sin pasar por aquí.

## Orden canónico de verificación

1. **Lint PHP** de cualquier archivo tocado:
   ```
   php -l ruta/al/archivo.php
   ```
2. **Purgar cachés** tras cambios de config/tema/plugin:
   ```
   php admin/cli/purge_caches.php
   ```
3. **Cron** (si el cambio afecta tareas programadas o mensajería):
   ```
   php admin/cli/cron.php
   ```
4. **Estado de versión/config** vía SQL (MCP `mysql` o cliente CLI):
   ```sql
   SELECT value FROM mdl_config WHERE name IN ('version','theme','lang');
   ```
5. **Sin errores visibles**: confirmar `$CFG->debug = 0` y `$CFG->debugdisplay = 0` en producción
   (`docs/07-seguridad-backups.md`). En staging se puede subir el nivel temporalmente, nunca en producción.
6. **Test Harness de la spec**: ejecutar la matriz `TC-*` del documento ESD relacionado
   (`docs/0X-*.md`) — no basta con "parece que funciona".
7. **Regla de cercanía** (`docs/15-prevencion-regresiones.md` §4): re-ejecutar también los TC
   de los módulos vecinos, no solo el tocado.

## Reporte

Al terminar, reportar por escrito: qué TC se corrieron, cuáles pasaron/fallaron, y si algo
que antes funcionaba se rompió (en ese caso: PARAR y revertir antes de seguir, no parchear).
