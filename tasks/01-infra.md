# Fase 1 — Infraestructura

> Spec: `specs/01-fase-infra.md` · ESD: `docs/01-infraestructura.md`
> Completada 2026-08-30. Detalle real de ejecución en `specs/01-fase-infra.md` (cada
> tarjeta anota los desajustes encontrados vs. la plantilla genérica).

**Progreso:** ✅ 100% (7/7) · **Estado:** HECHO

## ▶ Próxima tarea
Fase 2 (`tasks/02-config.md`) — SDD-CFG-01.

---

## T1.1 · Verificar requisitos del hosting (SDD-INF-01)
Estado: ✅
- [x] PHP 8.2 activo (fijado por vhost) y extensiones (intl, zip, gd, mbstring, curl, soap, opcache)
- [x] `memory_limit=512M`, `upload_max_filesize=512M`
- [x] Subdominio `portal.examenes-neuro.com` con SSL (Let's Encrypt, ya existía)

## T1.2 · Crear BD y usuario (SDD-INF-02)
Estado: ✅
- [x] Database `matiasf6_moodle_db` (motor real: MySQL 8.0.46, no MariaDB)
- [x] Usuario `matiasf6_moodle_user` solo sobre esa BD (ALL PRIVILEGES)
- Verificación: `uapi Mysql list_databases` / `get_privileges_on_database`

## T1.3 · Descargar y desplegar Moodle 4.5 (SDD-INF-03)
Estado: ✅
- [x] Release **4.5.13+ (Build: 20260818)**, branch 405, en el DocumentRoot
- [x] `moodledata_portal/` fuera del docroot, permisos `0700`
- Verificación: `admin/cli/install.php` existe, versión confirmada

## T1.4 · Instalar Moodle por CLI (SDD-INF-04)
Estado: ✅
- [x] `install.php --non-interactive` completo (`--dbtype=mysqli`, no `mariadb`)
- [x] Login admin funciona (clave en `.env.secrets`)
- Verificación: `SELECT version,release,lang FROM mdl_config` OK

## T1.5 · config.php producción (SDD-INF-05)
Estado: ✅
- [x] `$CFG->debug = 0` + `directorypermissions=0700` + `localrequestdir` propio (fix necesario, ver spec)
- Verificación: `php -l config.php` sin error

## T1.6 · Cron 1 min (SDD-INF-06)
Estado: ✅
- [x] `crontab` de usuario activo (esta cuenta no expone `uapi Cron`)
- Verificación: ejecución manual → "Cron run completed correctly"

## T1.7 · Instalar idioma pt-BR (SDD-INF-07)
Estado: ✅
- [x] Instalado automáticamente por `install.php --lang=pt_br` (no existe `langimport.cli` en Moodle 4.5)
- Verificación: `$CFG->dataroot/lang/pt_br/` con ~200 archivos
