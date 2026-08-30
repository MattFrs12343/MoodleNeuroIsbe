# SDD-00 — Variables Fijas del Proyecto

> **Parámetros inmutables**. El agente debe leer este archivo antes de cada tarjeta.
> Solo se completa/confirma UNA vez. Cualquier cambio debe anotarse aquí (no en las fases).

## 1. Identidad

| Variable | Valor | Estado |
|---|---|---|
| `DOMINIO` | `https://portal.MIDOMINIO.com` | ⚠️ REEMPLAZAR por dominio real |
| `NOMBRE_PLATAFORMA` | `Plataforma Educativa` (visible al usuario: definir en pt-BR) | Confirmar con cliente |
| `SHORTNAME_SITIO` | `plataforma` | Fijo |
| `ZONA_HORARIA` | `America/Sao_Paulo` (audiencia brasileña) | Fijo |

## 2. Servidor y BD

| Variable | Valor |
|---|---|
| `PHP_VERSION` | 8.2 (mínimo 8.1) |
| `DB_ENGINE` | `mariadb` (MariaDB 10.x; acepta `mysqli`) |
| `DB_HOST` | `localhost` (o valor del cPanel) |
| `DB_NAME` | `moodle_db` |
| `DB_USER` | `moodle_user` |
| `DB_PASS` | `<SECRETO>` (no versionar; guardar en gestor) |
| `PREFIX` | `mdl_` |
| `APP_DIR` | `public_html/` (DocumentRoot del subdominio) |
| `DATAROOT` | `/home/USER/moodledata` ⚠️ FUERA de `public_html` si el hosting lo permite; si no, dentro con `.htaccess` de bloqueo |
| `CRON_EJEC_S` | `*/1 * * * *` |

## 3. Moodle

| Variable | Valor |
|---|---|
| `MOODLE_MAJOR` | 4.5 (MOODLE_405_STABLE, última revision) |
| `MOODLE_VERSION` | `4.5.x` (release más reciente del branch) |
| `LANG_SITIO` | `pt_br` (único activo) |
| `THEME` | `plataforma` |
| `FORMAT_CURSO` | `topics` |
| `NUM_SECCIONES_BASE` | 6 (5 unidades + sección 0) |
| `SQL_USUARIO_ADMIN` | Definir: `admin` |
| `NOMBRE_MDLS_USER` | `dr_{nombre}` o según cliente |

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
| Contenido del curso | Acceso restringido a usuarios autorizados (licencia = BLOQUEANTE) |
| Idiomas | pt_BR único visible |