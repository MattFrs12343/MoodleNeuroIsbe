# SDD-00 — Variables Fijas del Proyecto

> **Parámetros inmutables**. El agente debe leer este archivo antes de cada tarjeta.
> Solo se completa/confirma UNA vez. Cualquier cambio debe anotarse aquí (no en las fases).

## 1. Identidad

| Variable | Valor | Estado |
|---|---|---|
| `DOMINIO` | `portal.examenes-neuro.com` | Confirmado 2026-08-30 |
| `NOMBRE_PLATAFORMA` | `Plataforma Educativa` (visible al usuario: definir en pt-BR) | Confirmar con cliente |
| `SHORTNAME_SITIO` | `plataforma` | Fijo |
| `ZONA_HORARIA` | `America/Sao_Paulo` (audiencia brasileña) | Fijo |

## 2. Servidor y BD

> ⚠️ Esta cuenta de hosting (`matiasf6@sh006.hostgator.net`) es **compartida con otros
> proyectos ya en producción** (otros subdominios de `examenes-neuro.com` con sus propias
> BD: `matiasf6_cuestionario_bd`, `matiasf6_pedidos_db`). Todo lo de Moodle debe quedar
> **estrictamente dentro** de `portal.examenes-neuro.com` y `matiasf6_moodle_*`. Nunca tocar
> los otros subdominios/BD/cron de esa cuenta.

| Variable | Valor | Verificado |
|---|---|---|
| `SSH` | `ssh matiasf6@sh006.hostgator.net` (auth por llave, sin password) | 2026-08-30 |
| `PHP_VERSION` | **8.2** — fijado por vhost vía `uapi LangPHP php_set_vhost_versions` (el CLI por defecto de la cuenta es 8.3; usar siempre el binario explícito `/opt/cpanel/ea-php82/root/usr/bin/php` en cron y CLI) | 2026-08-30 |
| `DB_ENGINE` | **MySQL 8.0.46 real** (NO MariaDB, pese a lo que sugería el stack fijado). `--dbtype=mysqli` en el instalador; `--dbtype=mariadb` fallaba el chequeo de ambiente (exige MariaDB ≥10.6.7, y `innodb_file_format` ni siquiera existe en MySQL 8) | 2026-08-30 |
| `DB_HOST` | `localhost` | 2026-08-30 |
| `DB_NAME` | `matiasf6_moodle_db` (cPanel exige prefijo `matiasf6_`; creada vía `uapi Mysql create_database name=matiasf6_moodle_db` — **NO** usar `uapi Mysql setup_db_and_user`, genera nombres/clave aleatorios propios) | 2026-08-30 |
| `DB_USER` | `matiasf6_moodle_user` (privilegios `ALL PRIVILEGES` solo sobre `matiasf6_moodle_db`) | 2026-08-30 |
| `DB_PASS` | `<SECRETO>` — generado 2026-08-30, ver `.env.secrets` (NO versionado, cubierto por `.gitignore`) | — |
| `PREFIX` | `mdl_` | 2026-08-30 |
| `APP_DIR` | `/home1/matiasf6/portal.examenes-neuro.com` (DocumentRoot propio del subdominio, NO está bajo `public_html/`) | 2026-08-30 |
| `DATAROOT` | `/home1/matiasf6/moodledata_portal` (fuera del docroot). Permisos **`0700`** (no `0777`: PHP corre como el propio usuario `matiasf6` vía suPHP/FPM, confirmado por ownership `matiasf6:nobody` del docroot — 0777 sería inseguro sin aportar nada). `$CFG->directorypermissions = 0700` en `config.php` (el default 02777 del instalador choca con el umask 0022 del sistema) | 2026-08-30 |
| `LOCALREQUESTDIR` | `$CFG->dataroot . '/localrequest'` — **override obligatorio** en `config.php`. El default de Moodle (`sys_get_temp_dir().'/requestdir'` = `/tmp/requestdir`) está "envenenado": ya existe, creado por OTRA cuenta cPanel (uid ajeno) en el `/tmp` compartido del servidor, y da `Permission denied` real pese a mostrar `777` por `ls` (artefacto típico de CageFS/CloudLinux). Sin este override, `admin/cli/cron.php` falla con `invaliddatarootpermissions` | 2026-08-30 |
| `CRON_EJEC_S` | **`*/5 * * * *`** (no `* * * * *`) — este hosting **reescribe solo, sin avisar, cualquier cron `* * * * *` a un intervalo mayor** (se detectó cambiado a `*/17 * * * *` de un día para otro sin que nadie lo tocara; es throttling de cron del proveedor en shared hosting, no un bug nuestro). Se fijó explícitamente en `*/5` como intervalo razonable; si se reescribe de nuevo, revisar `crontab -l` — pedir a soporte de HostGator una excepción si se necesita cron cada 1 min real. Vía `crontab` de usuario (no hay módulo `Cron` en `uapi` en esta cuenta): `/opt/cpanel/ea-php82/root/usr/bin/php /home1/matiasf6/portal.examenes-neuro.com/admin/cli/cron.php >/dev/null 2>&1` | 2026-08-31 |
| `BACKUPS` | `/home1/matiasf6/moodle_backups/{db,dataroot}/` + `.my.cnf` (permisos 600, credenciales de mysqldump) — carpeta **propia**, separada de `~/backups/` y `~/nm-backups/` (de otros proyectos de la misma cuenta compartida). Cron: BD diaria 00:00 (retención 7 días), dataroot semanal domingo 03:00 (retención 4 semanas) | 2026-08-31 |

## 3. Moodle

| Variable | Valor |
|---|---|
| `MOODLE_MAJOR` | 4.5 (MOODLE_405_STABLE, última revision) |
| `MOODLE_VERSION` | `4.5.x` (release más reciente del branch) |
| `LANG_SITIO` | `pt_br` (único activo) |
| `THEME` | `plataforma` |
| `FORMAT_CURSO` | `topics` |
| `NUM_SECCIONES_BASE` | 6 (5 unidades + sección 0) |
| `SQL_USUARIO_ADMIN` | `admin` (desarrollador, `dev@somoscdv.com` temporal) |
| `NOMBRE_MDLS_USER` | `dr_jcabello` — **Dr. Juan Marcelo Cabello Mérida**, `jmcabello_merida@hotmail.com` (confirmado y creado 2026-08-30) |

## 4. Estructura de contenido

| Variable | Valor |
|---|---|
| `CAT_RAIZ` | `Plataforma` |
| `CAT_MODULOS` | `Módulos` (dentro de la raíz) |
| `NOMBRE_CURSO` | `Módulo NN — <Tema>` (NN = 01, 02, ...) |
| `SHORTNAME_CURSO` | `modNN` |
| `SEC_UNIDAD` | `Unidade N — <Subtema>` |
| `SEC_0` | `Avisos e Introdução` |
| `PREFIJO_VIDEO` | `Vídeo da aula N` |
| `PREFIJO_AUDIO` | `Áudio da aula N` |
| `PREFIJO_DOC` | `<Tipo> — <descripción>` |

## 5. Servicios externos

| Variable | Valor |
|---|---|
| `YOUTUBE_EMBED_BASE` | `https://www.youtube-nocookie.com/embed/<VIDEO_ID>` |
| `YOUTUBE_VISIBILIDAD` | `Não listado` (No listado) |
| `DROPBOX_MODO` | Enlace privado (ver/descargar, no editar) |
| `DROPBOX_ESTRUCTURA` | `MÓDULO NN/Unidade N/<archivo>` (espejo de la plataforma) |

## 6. Presupuesto cerrado

| Variable | Valor |
|---|---|
| `TOTAL_BS` | 6,000 |
| `PAGO_1` | 50% arranque = 3,000 |
| `PAGO_2` | 50% entrega = 3,000 |
| `HITOS` | Ver `docs/10-presupuesto.md` |

## 7. Decisiones bloqueadas (no cambiar sin anotar)
| Regla | Detalle |
|---|---|
| NO tocar core | `theme/boost/*`, `lib/base`, `admin/*`, `mod/*` oficiales |
| Videos | SOLO embed YouTube (no subir al hosting) |
| Descargas de video | SOLO vía Dropbox (no alojar mp4 en host) |
| Contenido del curso | **RESUELTO (2026-08-30)**: licencia de uso personal, **un solo usuario** (el Dr.). Auto-registro DESHABILITADO; sin altas de terceros. |
| Idiomas | pt_BR único visible |