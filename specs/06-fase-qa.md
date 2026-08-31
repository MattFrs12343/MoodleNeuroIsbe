# SDD-06 — Fase 6: QA, Seguridad, Backups y Producción

> Prereq: Fases 1–5 completas. ESD ref: `docs/07-seguridad-backups.md`, `docs/09-migracion-runbook.md`,
> `docs/manual-de-uso-ptbr.md`.

---

## Parte A — QA global

### SDD-QA-01 — Ejecutar matriz de pruebas de todas las ESD 🟡 parcial (2026-08-31)
> Adelantada parcialmente junto con SDD-QA-03/05. El 100% de las TC **no puede** estar en
> verde todavía: `04-videos-embed`, `05-audio-documentos` y `06-portada-tema` dependen por
> completo de contenido (Fase 4) y tema (Fase 5), que no existen aún — no se marcan como
> aprobadas sin haberlas verificado de verdad.
- **Ejecutado y verificado** (vía `curl`/SQL contra el sitio real, no solo lectura de código):
  - `01-infraestructura`: **4/5 en verde** (TC-I-01/02/03/05); TC-I-04 (cron) queda en 🟡
    porque corre cada 5 min, no cada 1 min (limitación del hosting, ver Fase 1).
  - `02-autenticacion`: **5/7 en verde** (TC-A-01/02/03/04/06); TC-A-05 y TC-A-07 en 🟡,
    bloqueados por no existir aún un curso real visible (Fase 3) ni la portada custom (Fase 5).
  - `03-modulos-cursos`: **2/6 en verde** (TC-M-02/04); el resto bloqueado por Fase 3/4/5.
  - `08-legal-lgpd`: **1/6 en verde** (TC-L-06); TC-L-01/03/04 en 🟡 (config lista, falta
    integración de UI/procedimiento); TC-L-02/05 bloqueados por Fase 4/5.
  - `04-videos-embed`, `05-audio-documentos`, `06-portada-tema`: **sin ejecutar**, bloqueados
    en su totalidad por Fases 4 y 5 (no hay contenido ni tema todavía).
- **DoD**
  - [ ] Todo TC aprobado — **no alcanzable hasta cerrar Fases 4 y 5**; lo verificable con
        el estado actual del sitio ya se hizo y está anotado en cada ESD.

### SDD-QA-02 — Test de usabilidad clave (3 usuarios piloto)
- **Pasos**
  1. Pedir a 2–3 usuarios pilotos seguir el manual `docs/manual-de-uso-ptbr.md`.
  2. Recoger: tiempo para llegar a un video (objetivo ≤ 2 clics), dudas de navegación.
- **Verificación**
  - Feedback sin bloqueos críticos.
- **DoD**
  - [ ] Pilotaje sin incidencias graves.

---

## Parte B — Seguridad

### SDD-QA-03 — Endurecimiento final ✅ (2026-08-31, adelantada)
> Adelantada junto con SDD-QA-05, a pedido del desarrollador de enfocarse en backend
> mientras se espera contenido/diseño del Dr.
- **Pasos ejecutados**:
  1. `debug=0` — ya estaba desde Fase 1 (SDD-INF-05). Confirmado sin cambios.
  2. **Bloqueo de cuenta tras intentos fallidos**: estaba **deshabilitado**
     (`lockoutthreshold=0`). Se activó: `lockoutthreshold=10` (con `lockoutwindow=1800`
     y `lockoutduration=1800` ya presentes por defecto).
  3. **Antivirus**: NO viable en este hosting — se verificó que no existe `clamscan`/
     `clamdscan` en el `$PATH` de la cuenta (shared hosting sin acceso root para instalar
     ClamAV). Mitigación de reemplazo: la lista blanca de extensiones de archivo
     (`SDD-CAR-06`, `mp3/pdf/docx/txt` solamente) reduce igual la superficie de ataque de
     subida de archivos, aunque sin escaneo de contenido malicioso.
  4. **`config.php` protegido**: estaba en `640` (rw-r-----), se bajó a **`400`**
     (r--------, ni siquiera el propio dueño puede escribir sin `chmod` antes). Nota
     operativa: cualquier edición futura de `config.php` por SSH requiere
     `chmod 600 config.php`, editar, y volver a `chmod 400`.
  5. **Edición de tema desde la UI**: no se deshabilitó (queda "según preferencia" del
     desarrollador, sin acción tomada).
  6. **Re-revisión completa del Informe de seguridad**: se encontró y corrigió el "Erro"
     que había quedado abierto desde SDD-CFG-04 (Fase 2) — **no era un artefacto de caché
     como se supuso entonces**. Instanciando el chequeo `\core\check\environment\publicpaths`
     directo por CLI (bypaseando cualquier caché de sesión/render) y revisando la tabla de
     detalle fila por fila (no solo el resumen con deduplicación, que ocultaba el
     verdadero culpable), se encontraron **dos archivos reales aún públicos**:
     `.github/FUNDING.yml` y `.stylelintrc` (`200`, nunca antes probados individualmente).
     Corregido agregando una regla `.htaccess` que bloquea cualquier segmento de ruta que
     empiece con un punto: `RewriteRule (^|/)\. - [F,L]` (cubre `.git/`, `.github/`,
     `.stylelintrc`, `.upgradenotes/`, etc. de una sola vez).
- **Verificación**
  - `mdl_config`: `lockoutthreshold=10`.
  - `config.php`: `-r--------` (400), `php -l` sin error, sitio responde `200` tras el cambio.
  - `curl` a `.github/FUNDING.yml` y `.stylelintrc` → `403` (antes `200`).
  - **Informe de seguridad final: 0 alertas críticas** (17 OK, 1 Info — solo cantidad de
    administradores —, 1 Aviso — capacidad de backup de datos de usuario, warning de
    baseline estándar de Moodle sin acción específica requerida).
- **DoD**
  - [x] Checklist de seguridad completado (antivirus documentado como no viable en este hosting).

### SDD-QA-04 — Suscripción a seguridad de Moodle
- **Pasos**
  1. Registrarse en [Moodle Security Announcements](https://moodle.org/plugins/security_announcements) (lista oficial).
- **Verificación**
  - Confirmación de suscripción en el correo.
- **DoD**
  - [ ] Suscripción activa.

---

## Parte C — Backups

### SDD-QA-05 — Backups automáticos (BD diaria + dataroot semanal) ✅ (2026-08-31)
- **Adelantada respecto al orden de fases** (no depende de contenido/tema, y proteger desde
  ya lo que existe — categorías, plantilla, cuenta del Dr., políticas legales — es mejor
  que esperar a Fase 6). Ejecutada por SSH, adaptada al hosting real:
  1. Carpeta **propia** `/home1/matiasf6/moodle_backups/{db,dataroot}/` (permisos 700) —
     **NO** se reutilizó `~/backups/` ni `~/nm-backups/`, que son de otros proyectos de la
     misma cuenta compartida (llenos de archivos `pedidos_*` y `predeploy-*`).
  2. Credenciales de `mysqldump` en `~/moodle_backups/.my.cnf` (permisos 600), NO en el
     crontab en texto plano.
  3. Cron real instalado:
     ```bash
     0 0 * * * /usr/bin/mysqldump --defaults-extra-file=/home1/matiasf6/moodle_backups/.my.cnf matiasf6_moodle_db | gzip > /home1/matiasf6/moodle_backups/db/bd_$(date +\%F).sql.gz && find /home1/matiasf6/moodle_backups/db -name "bd_*.sql.gz" -mtime +7 -delete
     0 3 * * 0 tar czf /home1/matiasf6/moodle_backups/dataroot/dataroot_$(date +\%F).tar.gz -C /home1/matiasf6 moodledata_portal && find /home1/matiasf6/moodle_backups/dataroot -name "dataroot_*.tar.gz" -mtime +28 -delete
     ```
- **Verificación**
  - Backup manual de prueba ejecutado y confirmado: `bd_2026-08-31.sql.gz` (298 KB, 494
    `CREATE TABLE` + 68 `INSERT INTO`, `gunzip -t` sin corrupción) y
    `dataroot_2026-08-31.tar.gz` (4 MB, contenido verificado con `tar tzf`).
  - `mysqldump` mostró un warning benigno ("Access denied... PROCESS privilege... al
    intentar volcar tablespaces") — normal en hosting compartido sin privilegios SUPER;
    no afecta el dump de datos/esquema, confirmado revisando el contenido real del dump.
- **DoD**
  - [x] BD diaria y dataroot semanal automáticos, verificados con un backup manual real.

### SDD-QA-06 — Prueba de restauración (RTO < 30 min)
- **Pasos**
  1. En un directorio de staging: restaurar último backup BD + dataroot.
  2. Cronometrar. Verificar login y un recurso.
- **Verificación**
  - Restauración completa en < 30 min.
- **DoD**
  - [ ] Restauración probada y documentada en `docs/09-migracion-runbook.md`.

---

## Parte D — Producción

### SDD-QA-07 — Puesta en producción y dominios
- **Pasos**
  1. Forzar HTTPS (redirect en `.htaccess`), cookies `Secure`.
  2. `php admin/cli/purge_caches.php`.
  3. Revisar `/admin/index.php` → sin tareas pendientes (upgrades).
- **Verificación**
  - `http://` → redirige a `https://`.
  - Sin pending upgrade.
- **DoD**
  - [ ] Producción estable + HTTPS forzado.

### SDD-QA-08 — Manual de usuario final
- **Pasos**
  1. Confirmar con el cliente el manual `docs/manual-de-uso-ptbr.md`.
  2. (Opcional) convertir a PDF para entregar.
- **Verificación**
  - Aprobado por el cliente.
- **DoD**
  - [ ] Manual entregado en pt-BR.

### SDD-QA-09 — Firma de cierre (hito 2 del pago)
- **Pasos**
  1. Presentar `docs/CHANGELOG.md` y DoD de todas las fases.
  2. Entregar credenciales y runbook al cliente (gestor externo).
  3. Emitir factura/referencia de segundo pago (Bs 3,000).
- **Verificación**
  - Cliente confirma recepción.
- **DoD**
  - [ ] Proyecto cerrado y cobrado hito 2.