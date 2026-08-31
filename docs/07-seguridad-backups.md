# ESD-07 — Seguridad, Backups y Actualizaciones (Ops)

> **Especificación de Ingeniería · Harness Engineer**
> Versión 1.0 · 2026-08-28 · Prioridad: **Media**
> Relacionada con: `00-maestro.md` (GO-6, CQ-5)

---

## 1. Propósito
Establecer el régimen operativo: **seguridad**, **respaldo (backup)** y **actualizaciones**
de Moodle en hosting compartido, para que la plataforma siga operando sin pérdida de datos
ni brechas de seguridad.

## 2. Alcance
- Endurecimiento de Moodle y del hosting.
- Plan de backups (BD + dataroot) y prueba de restauración.
- Política de actualización (seguridad) sin tocar el core.
- Monitoreo básico y paneles de estado.

## 3. Requisitos de seguridad

- RF-S-01: **SSL/TLS obligatorio** y renovación automática.
- RF-S-02: Deshabilitar cuenta default/config admin de demostración.
- RF-S-03: `$CFG->debug = 0` y `$CFG->displayerrors` off; logs guardados en dataroot.
- RF-S-04: Contraseñas de BD/`config.php` no versionadas; rotación semestral.
- RF-S-05: Newsletter de seguridad de Moodle (suscribirse) y aplicar **hotfixes**.
- RF-S-06: Proteger `dataroot` (fuera de `public_html` o con `.htaccess` `deny`).
- RF-S-07: Validar archivos (`admin/tool/replace` no; mejor el checker de archivos
  `admin/tool/checkverification`/report de seguridad).
- RF-S-08: Límite de intentos de login (plugin `autounblock`/`login_attempt` nativo).

## 4. Plan de backups

### 4.1 Con qué
- BD: respaldo vía **phpMyAdmin/cPanel** o `mysqldump` (gzip).
- Archivos: `.zip`/`.tgz` del `dataroot` y del directorio de la app (excluyendo cachés).

### 4.2 Cadencia y retención
| Ítem | Frecuencia | Retención |
|---|---|---|
| BD | Diaria | 7 días |
| Dataroot (archivos) | Semanal | 4 semanas |
| Código (app + tema) | Por cambio | Última versión |
| Respaldo completo | Mensual | 3 meses |

### 4.3 Restauración (RTO)
- Meta RTO < 30 min (Spec-00, CQ-5). Documentar pasos en runbook (sección 7).

## 5. Actualizaciones (Moodle upgrades)

- RF-S-09: Antes de actualizar: **backup completo** + copia en staging.
- RF-S-10: Seguir solo el árbol **stable** (`MOODLE_4_x_STABLE`).
- RF-S-11: Revisar compatibilidad del **tema hijo** y plugins propios tras cada upgrade.
- RF-S-12: Aplicar updates de seguridad vía `admin/cli/purge_caches` y validación post-upgrade.
- RF-S-13: NO tocar el core => las actualizaciones fallan menos y se aplican sin merge.

## 6. Monitoreo básico
| Ítem | Herramienta | Frecuencia |
|---|---|---|
| Errores PHP | `mdl_log` / `admin/report/log` | Semanal |
| Disco | cPanel `Disk Usage` | Mensual |
| Cron | `admin/report/status` | Semanal |
| Seguridad | `admin/report/security` | Mensual |
| Versión | `admin/index.php` pre-check | Antes de cada release |

## 7. Runbook operativo resumido

1. **Diario (auto)**: backup BD → carpeta `backups/db/`.
2. **Semanal**: backup dataroot en ZIP.
3. **Ante upgrade**: staging → backup completo → upgrade → test (TC lista de Specs 01-06).
4. **Ante incidente de seguridad**: rotar credenciales, revisar `admin/report/security`,
   restaurar último backup limpio si es necesario.

## 8. Criterios de aceptación (Test Harness)

| ID | Prueba | Pasos | Resultado esperado | Estado |
|---|---|---|---|---|
| TC-S-01 | HTTPS forzado | Navegar `http://` | Redirige a `https://` | ☐ |
| TC-S-02 | Debug 0 | Provocar 404/error | Sin trazas PHP al usuario | ☐ |
| TC-S-03 | Backup BD | `mysqldump` + gzip | Backup < 24 h de antigüedad | ☐ |
| TC-S-04 | Restauración | Restaurar BD+dataroot en staging | Plataforma operativa < 30 min | ☐ |
| TC-S-05 | Upgrade sin regresión | Upgrade en staging + correr TC specs | Sin errores visibles | ☐ |
| TC-S-06 | Dataroot protegido | URL directa a `moodledata/file.php` | 403 o redirección | ☐ |
| TC-S-07 | Antivirus de archivos | Subir archivo de prueba malicioso | Bloqueado (cl_scan) | ☐ |

## 9. Riesgos y mitigaciones
| ID | Descripción | Prob. | Impacto | Mitigación |
|---|---|---|---|---|
| R-S-01 | Hackeo por plugin/theme desactualizado | Baja | Alto | Solo plugins oficiales; updates de seguridad |
| R-S-02 | Pérdida de dataroot (disco lleno) | Media | Alto | Monitoreo de disco + backup semanal |
| R-S-03 | Upgrade rompe tema/plag | Media | Alto | Staging + backups previos + tests |
| R-S-04 | Backups no testeados | Media | Alto | Restauración programada trimestral |

## 10. Definition of Done (DoD)
- [x] HTTPS forzado + renovación automática (Let's Encrypt gestionado por el hosting, ver Fase 1).
- [x] Backups automáticos BD (diario) y dataroot (semanal) — 2026-08-31, ver `specs/06-fase-qa.md` (SDD-QA-05).
- [ ] Restauración probada en staging (RTO < 30 min) — diferida: no hay entorno de staging
      separado en esta cuenta; se probará cuando haya contenido real que valga la pena
      ensayar restaurar (Fase 4+). Por ahora se verificó integridad de los archivos de
      backup (`gunzip -t`, `tar tzf`, conteo de tablas/inserts), no una restauración completa.
- [ ] Suscripción a boletín de seguridad de Moodle activa — pendiente, requiere que el
      desarrollador decida qué email usar para suscribirse.
- [x] Runbook operativo accesible (en este repo, `docs/09-migracion-runbook.md` y
      `specs/06-fase-qa.md`).

> ⚠️ **Hallazgo de infraestructura (2026-08-31)**: este hosting reescribe automáticamente
> cron jobs de "cada minuto" a un intervalo mayor (detectado como `*/17 min` sin que nadie
> lo tocara). Se ajustó el cron de Moodle a `*/5 min` explícitamente. Ver
> `specs/00-variables-fijas.md` §2 y `specs/01-fase-infra.md` (SDD-INF-06).