# Fase 1 — Infraestructura

> Spec: `specs/01-fase-infra.md` · ESD: `docs/01-infraestructura.md`
> Bloqueante: Fase 0 (datos del hosting/dominio).

**Progreso:** ⬜ 0% (0/7) · **Estado:** PENDIENTE

## ▶ Próxima tarea
T1.1 (verificar hosting) — requiere "cPanel access" de la Fase 0.

---

## T1.1 · Verificar requisitos del hosting (SDD-INF-01)
Estado: ⬜
- [ ] PHP 8.1+ activo y extensiones (intl, zip, gd, mbstring, curl, soap, opcache)
- [ ] `memory_limit ≥ 256M`, `upload_max_filesize ≥ 256M`
- [ ] Subdominio `portal.<dominio>` con SSL
- Verificación: TC-I-01/02

## T1.2 · Crear BD y usuario MariaDB (SDD-INF-02)
Estado: ⬜
- [ ] Database `moodle_db` (utf8mb4)
- [ ] Usuario `moodle_user` solo sobre esa BD
- Verificación: SELECT information_schema

## T1.3 · Descargar y desplegar Moodle 4.5 (SDD-INF-03)
Estado: ⬜
- [ ] Release 4.5.x en el DocumentRoot
- [ ] `moodledata/` fuera de public_html (o protegida con .htaccess)
- Verificación: `ls admin/cli/install.php`

## T1.4 · Instalar Moodle por CLI (SDD-INF-04)
Estado: ⬜
- [ ] `install.php` completo (--lang=pt_br, credenciales)
- [ ] Login admin funciona
- Verificación: `SELECT version FROM mdl_config`

## T1.5 · config.php producción (SDD-INF-05)
Estado: ⬜
- [ ] `$CFG->debug = 0`
- Verificación: `php -l config.php`

## T1.6 · Cron 1 min (SDD-INF-06)
Estado: ⬜
- [ ] Cron Jobs cPanel activo
- Verificación: informe de estado "cron hace < 1 min"

## T1.7 · Instalar idioma pt-BR (SDD-INF-07)
Estado: ⬜
- [ ] `langimport.cli` con `--lang=pt_br`
- Verificación: `ls lang/pt_br`