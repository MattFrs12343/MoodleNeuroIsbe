# ESD-09 — Migración de Hosting y Runbook

> **Especificación de Ingeniería · Harness Engineer**
> Versión 1.0 · 2026-08-28 · Prioridad: **Media**
> Relacionada con: `00-maestro.md` (CQ-5), `07-seguridad-backups.md`

---

## 1. Propósito
Definir el **proceso reproducible** para mover la plataforma entre servidores/dominios
(sin pérdida de datos ni enlaces rotos) y el **runbook operativo** diario del único admin.

## 2. Alcance
- Migración: exportación, transferencia, importación y validación.
- Cambio de dominio (situaciones de URL).
- Runbook: tareas diarias, semanales, mensuales del desarrollador.
- Guía de emergencia (caída, hackeo, borrado accidental).

## 3. Migración de hosting (procedimiento)

### 3.1 Paquete de migración
| Ítem | Contenido | Nota |
|---|---|---|
| BD | `mysqldump -uUSER -p DB > moodle.sql.gz` | Incluye `mdl_*` completo |
| Dataroot | ZIP/`tgz` de `moodledata/` | Incluye `filedir/`, `cache/` |
| Código | ZIP de la raíz de la app (sin `config.php` si se regenera) | Incluye `theme_nombre` |
| Credenciales | Usuario/clave nueva BD y hosting | No versionar |

### 3.2 Secuencia (harness)
1. **Parar escritura**: poner site en mantenimiento (`admin/cli/maintenance.php --enable`).
2. **Backup final**: BD + dataroot (Spec-07).
3. **Alta en destino**: crear BD, usuario, subir archivos, apuntar DNS/subdominio.
4. **Configurar** `config.php` del destino (nuevas rutas, credenciales, wwwroot).
5. **Ajustar URLs**: si cambia el dominio, ejecutar:
   `php admin/cli/cfg.php` o el reemplazo seguro de `wwwroot`:
   `php admin/cli/migrate_wwwroot.php --source=https://viejo --target=https://nuevo`
   (método **oficial** de Moodle; evita romper enlaces de recursos y menús).
6. **Limpiar cachés**: `php admin/cli/purge_caches.php`.
7. **Validación** (Tabla de pruebas abajo).

> ⚠️ NO usar `admin/tool/replace` (o hacerlo solo si migrar wwwroot CLI falla):
> `replace` reescribe la BD buscando cadenas y puede perder datos binarios.

### 3.3 Cambio de dominio (TIP)
- Los **embeds de YouTube no cambian** (viven en Google).
- Los **enlaces de Dropbox no cambian**.
- Solo cambian URLs de archivos locales del hosting (ya resueltos con `migrate_wwwroot`).

## 4. Validación post-migración (Test Harness)

| ID | Prueba | Pasos | Resultado esperado | Estado |
|---|---|---|---|---|
| TC-G-01 | Login tras migración | Acceder con usuario admin | Login OK en el nuevo hosting | ☐ |
| TC-G-02 | Recursos de curso | Abrir módulo con video+pdf | Recursos funcionan (embed/descarga) | ☐ |
| TC-G-03 | Enlaces internos | Navegar curso→sección→recurso | No hay URLs hacia el dominio viejo | ☐ |
| TC-G-04 | Archivos dataroot | Descargar un MP3/DOCX | Descarga correcta y completa | ☐ |
| TC-G-05 | Cron nuevo | Ejecutar cron en destino | Sin errores; estado "cron ok" | ☐ |
| TC-G-06 | Restaurar BD vacía | Importar `moodle.sql.gz` | Import sin errores | ☐ |

## 5. Runbook operativo (admin único)

### 5.1 Diario (automatizable)
| Hora | Tarea | Herramienta |
|---|---|---|
| 00:00 | Backup BD (gzip) con retención 7 días | cron + `mysqldump` |

### 5.2 Semanal
- Backup `dataroot` (ZIP) con retención 4 semanas.
- Revisar `Admin → Reportes → Registros` (errores, logins extraños).
- Comprobar `Admin → Informe del sitio` (estado cron, disco, actualizaciones).

### 5.3 Mensual
- Revisar `Admin → Informe de seguridad`.
- Revisar espacio en disco y en Dropbox del Dr.
- Revisar enlaces de descarga Dropbox (HEAD 200) y estado de los videos YouTube.

### 5.4 Trimestral
- **Prueba de restauración** en staging (< 30 min).
- Revisar suscripciones al boletín de seguridad Moodle.
- Rotar contraseñas (hosting, BD, admin).

## 6. Guía de emergencia (incidentes)

| Incidente | Acción inmediata | Tiempo |
|---|---|---|
| Sitio caído / 500 | Revisar logs PHP (`error_log`), `debug`, estado disco | 15 min |
| Hackeo sospechoso | Cambiar credeenciales, entrar en mantenimiento, restaurar backup limpio | 1 h |
| Borrado de BD | Restaurar último backup BD + dataroot | 30 min |
| Disco lleno | Borrar cachés/temporales, revisar `filedir`; descargar backups a Dropbox | 1 h |
| Dominio/SSL caído | Renovar certificado; contacto del proveedor | Varía |

## 7. Definition of Done (DoD)
- [ ] Procedimiento de migración probado al menos 1 vez en staging.
- [ ] `migrate_wwwroot.php` verificado (no `replace`).
- [ ] Runbook accesible (este documento) y copia en Dropbox del Dr.
- [ ] Cron de backup diario activo y monitoreado.
- [ ] Plantilla de checklist "migración" lista para uso.