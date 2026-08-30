# SDD-06 — Fase 6: QA, Seguridad, Backups y Producción

> Prereq: Fases 1–5 completas. ESD ref: `docs/07-seguridad-backups.md`, `docs/09-migracion-runbook.md`,
> `docs/manual-de-uso-ptbr.md`.

---

## Parte A — QA global

### SDD-QA-01 — Ejecutar matriz de pruebas de todas las ESD
- **Pasos**
  1. Ejecutar la matriz `TC-` de: `01-infraestructura`, `02-autenticacion`, `03-modulos-cursos`,
     `04-videos-embed`, `05-audio-documentos`, `06-portada-tema`, `08-legal-lgpd`.
  2. Anotar fallos en `docs/CHANGELOG.md` con su fix.
- **Verificación**
  - 100% de casillas TC en ☑ (verde).
- **DoD**
  - [ ] Todo TC aprobado.

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

### SDD-QA-03 — Endurecimiento final
- **Pasos**
  1. `php admin/cli/cfg.php --name=debug --value=0`
  2. UI: *Site admin → Security*: revisar informe completo: contraseñas fuertes, `login_attempt` activo,
     antivirus, permisos de archivos (`config.php` 400/440).
  3. Deshabilitar edición del tema desde la UI (solo por archivos) — según preferencia.
- **Verificación**
  - Informe de seguridad sin rojos.
- **DoD**
  - [ ] Checklist de seguridad completado.

### SDD-QA-04 — Suscripción a seguridad de Moodle
- **Pasos**
  1. Registrarse en [Moodle Security Announcements](https://moodle.org/plugins/security_announcements) (lista oficial).
- **Verificación**
  - Confirmación de suscripción en el correo.
- **DoD**
  - [ ] Suscripción activa.

---

## Parte C — Backups

### SDD-QA-05 — Backups automáticos (BD diaria + dataroot semanal)
- **Pasos (cPanel → Cron Jobs)**
  ```bash
  # BD DIARIA (retención 7d)
  0 0 * * * mysqldump -u moodle_user -p'<DB_PASS>' moodle_db | gzip > /home/USER/backups/bd_$(date +\%F).sql.gz && find /home/USER/backups -name 'bd_*.sql.gz' -mtime +7 -delete
  # DATAROOT SEMANAL (retención 4w)
  0 3 * * 0 tar czf /home/USER/backups/dataroot_$(date +\%F).tar.gz /home/USER/moodledata && find /home/USER/backups -name 'dataroot_*.tar.gz' -mtime +28 -delete
  ```
- **Verificación**
  - `ls -la /home/USER/backups` → backups recientes presentes.
- **DoD**
  - [ ] BD diaria y dataroot semanal automáticos.

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