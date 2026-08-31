# SDD-01 — Fase 1: Infraestructura (Despliegue Moodle)

> Prereq: `00-variables-fijas.md` completado. ESD ref: `docs/01-infraestructura.md`.

---

### SDD-INF-01 — Verificar requisitos del hosting ✅ (2026-08-30)
- **Entrada**: acceso SSH (`ssh matiasf6@sh006.hostgator.net`, auth por llave).
- **Pasos ejecutados** (vía `uapi` por SSH, sin UI de cPanel):
  1. PHP fijado a **8.2** solo para `portal.examenes-neuro.com`: `uapi LangPHP php_set_vhost_versions vhost-0=portal.examenes-neuro.com version=ea-php82` (el default de la cuenta es PHP 8.3; usar siempre el binario explícito `/opt/cpanel/ea-php82/root/usr/bin/php`).
  2. Extensiones verificadas activas bajo ea-php82: intl, zip, gd, mbstring, curl, soap, iconv, opcache, openssl — todas presentes.
  3. `memory_limit=512M`, `upload_max_filesize=512M`, `post_max_size=516M` — de sobra.
  4. El subdominio **ya existía** (creado por el desarrollador antes de darme acceso), DocumentRoot propio en `/home1/matiasf6/portal.examenes-neuro.com` (NO bajo `public_html/`). SSL Let's Encrypt ya activo y válido.
- **Verificación**
  - `curl -Is https://portal.examenes-neuro.com` → `200`.
  - Certificado confirmado vía `uapi SSL list_certs` (Let's Encrypt, no self-signed).
- **DoD**
  - [x] PHP 8.2 con extensiones activas.
  - [x] límites PHP correctos.
  - [x] Subdominio + SSL operativo.

---

### SDD-INF-02 — Crear BD y usuario ✅ (2026-08-30)
- **Entradas**: `DB_*` de variables fijas (motor real: **MySQL 8.0.46**, no MariaDB).
- **Pasos ejecutados** (`uapi`, no SQL directo — el usuario shell no tiene root de mysql):
  ```bash
  uapi Mysql create_database name='matiasf6_moodle_db'
  uapi Mysql create_user name='matiasf6_moodle_user' password='<DB_PASS>'
  uapi Mysql set_privileges_on_database user='matiasf6_moodle_user' database='matiasf6_moodle_db' privileges='ALL PRIVILEGES'
  ```
  ⚠️ **NO usar `uapi Mysql setup_db_and_user`**: ignora los nombres pedidos y genera
  nombre de BD/usuario/clave aleatorios propios (se probó, se detectó y se limpió).
  ⚠️ cPanel exige que `name=` ya incluya el prefijo `matiasf6_`; sin él, error explícito
  (no crea nada, falla limpio).
- **Verificación**
  - `uapi Mysql list_databases` → `matiasf6_moodle_db` con usuario `matiasf6_moodle_user`.
  - `uapi Mysql get_privileges_on_database user=... database=...` → `ALL PRIVILEGES`.
  - Confirmado que `matiasf6_cuestionario_bd` y `matiasf6_pedidos_db` (otros proyectos de
    la misma cuenta) quedaron intactos.
- **DoD**
  - [x] BD y usuario creados.
  - [x] Permisos solo sobre `matiasf6_moodle_db`.

---

### SDD-INF-03 — Descargar y desplegar Moodle 4.5 ✅ (2026-08-30)
- **Entradas**: `MOODLE_VERSION`, `APP_DIR`.
- **Pasos ejecutados** (directo por SSH en el servidor, sin subir el .tgz desde local):
  ```bash
  cd /home1/matiasf6
  curl -sSL -o moodle-405.tgz "https://download.moodle.org/download.php/direct/stable405/moodle-latest-405.tgz"
  tar -xzf moodle-405.tgz -C portal.examenes-neuro.com --strip-components=1
  rm -f moodle-405.tgz
  mkdir -p /home1/matiasf6/moodledata_portal && chmod 0700 /home1/matiasf6/moodledata_portal
  ```
  ⚠️ **`chmod 0700`, no `0777`**: en esta cuenta PHP corre como el propio usuario
  `matiasf6` (confirmado por el ownership `matiasf6:nobody` del docroot, típico de
  suPHP/FPM por cuenta) — no hay un usuario `www-data` separado que necesite bits de
  grupo/otros. `0777` sería un riesgo sin ningún beneficio aquí.
- **Verificación**
  - Versión real instalada: `4.5.13+ (Build: 20260818)`, branch `405`, `MATURITY_STABLE`.
  - `admin/cli/install.php` y `moodledata_portal/` existen.
- **DoD**
  - [x] Código 4.5.x en su directorio.
  - [x] Dataroot creado y protegido.

---

### SDD-INF-04 — Instalar Moodle por CLI (no interactivo) ✅ (2026-08-30)
- **Entradas**: todas las variables reales (ver `specs/00-variables-fijas.md` §2).
- **Comando real ejecutado**:
  ```bash
  /opt/cpanel/ea-php82/root/usr/bin/php -d max_input_vars=5000 admin/cli/install.php \
    --lang=pt_br \
    --wwwroot=https://portal.examenes-neuro.com \
    --dataroot=/home1/matiasf6/moodledata_portal \
    --dbtype=mysqli --dbhost=localhost \
    --dbname=matiasf6_moodle_db --dbuser=matiasf6_moodle_user --dbpass='<DB_PASS>' \
    --prefix=mdl_ \
    --fullname='Plataforma de Estudos' \
    --shortname=plataforma \
    --adminuser=admin --adminpass='<ADMIN_PASS>' \
    --adminemail=dev@somoscdv.com \
    --non-interactive --agree-license
  ```
  ⚠️ **Diferencias con la plantilla genérica que costaron un reintento**:
  1. `--dbtype=mariadb` **falla** el chequeo de ambiente en este hosting: el motor real
     es MySQL 8.0.46 (no MariaDB), y `mariadb` exige ≥10.6.7. Usar `--dbtype=mysqli`.
  2. `max_input_vars` por defecto (1000) no alcanza el mínimo de Moodle (5000) → pasar
     `-d max_input_vars=5000` al invocar el CLI, y dejar un `.user.ini` en el docroot
     con `max_input_vars = 5000` para que aplique también a las peticiones web (FPM).
  3. Usar siempre el binario **`/opt/cpanel/ea-php82/root/usr/bin/php`** explícito, no
     `php` a secas (el CLI por defecto de la cuenta es PHP 8.3).
  4. `--adminemail` real del Dr. aún no confirmado (`docs/13-info-necesaria.md` §2) → se
     usó temporalmente el email del desarrollador (`dev@somoscdv.com`); cambiar en
     *Site admin → Users → admin* cuando el Dr. confirme el suyo (campo trivial de editar).
- **Verificación**
  - `Instalação terminada com sucesso.` en consola.
  - `SELECT value FROM mdl_config WHERE name IN ('version','release','lang')` →
    `2024100713.01` / `4.5.13+ (Build: 20260818)` / `pt_br`.
  - `https://portal.examenes-neuro.com` → `200 OK`.
- **DoD**
  - [x] Instalación completa sin warnings críticos.
  - [x] Login admin funciona (cuenta `admin`, clave en `.env.secrets`).

---

### SDD-INF-05 — Configurar `config.php` (producción) ✅ (2026-08-30)
- **Pasos ejecutados** (insertado antes de `require_once(__DIR__.'/lib/setup.php')`):
  ```php
  $CFG->debug = 0;
  $CFG->debugdisplay = 0;
  $CFG->perfdebug = 0;
  $CFG->cachejs = 1;
  $CFG->enablecronkey = 1;
  $CFG->localrequestdir = $CFG->dataroot . '/localrequest';
  $CFG->directorypermissions = 0700;
  ```
  ⚠️ **Dos directivas adicionales no previstas en la plantilla, imprescindibles en este
  hosting** (sin ellas, `admin/cli/cron.php` falla con `invaliddatarootpermissions`):
  - `directorypermissions = 0700`: el default del instalador (`02777`) choca con el
    `umask 0022` del proceso y con el ownership real (PHP corre como `matiasf6`).
  - `localrequestdir`: el default de Moodle (`sys_get_temp_dir().'/requestdir'` =
    `/tmp/requestdir`) apunta a un directorio **compartido de todo el servidor**, ya
    creado por OTRA cuenta cPanel (uid ajeno) — dentro de la propia CageFS/CloudLinux
    de `matiasf6` aparece con permisos `777` por `ls`, pero da `Permission denied` real.
    Redirigirlo a un subdirectorio propio dentro de `dataroot` lo resuelve del todo.
- **Verificación**
  - `php -l config.php` → sin error.
  - `admin/cli/cron.php` corre limpio, sin `invaliddatarootpermissions` (ver SDD-INF-06).
- **DoD**
  - [x] `debug=0`, sin trazas al usuario.

---

### SDD-INF-06 — Programar cron cada 1 minuto ✅ (2026-08-30)
- **Pasos ejecutados**: esta cuenta **no expone módulo `Cron` en `uapi`**, así que se usó
  `crontab` de usuario directamente por SSH (verificando antes con `crontab -l`, vacío,
  que no había nada que pisar):
  ```bash
  (crontab -l 2>/dev/null; echo "* * * * * /opt/cpanel/ea-php82/root/usr/bin/php /home1/matiasf6/portal.examenes-neuro.com/admin/cli/cron.php >/dev/null 2>&1") | crontab -
  ```
- **Verificación**
  - Ejecución manual de `admin/cli/cron.php`: `Cron run completed correctly` (tras aplicar
    el fix de `localrequestdir` de SDD-INF-05; antes de ese fix fallaba con
    `invaliddatarootpermissions`).
  - `crontab -l` confirma la línea activa, sin afectar otros cron jobs de la cuenta (no
    había ninguno previo).
- **DoD**
  - [x] Cron activo y sin errores.

---

### SDD-INF-07 — Instalar paquete de idioma pt-BR ✅ (2026-08-30)
- **Nota**: Moodle 4.5 **ya no trae** `admin/tool/langimport/cli/langimport.php` (no
  existe ese script en esta versión, solo la página de UI `admin/tool/langimport/index.php`).
  Sin embargo, pasar `--lang=pt_br` a `admin/cli/install.php` (SDD-INF-04) **ya descarga
  e instala el paquete automáticamente** durante la instalación — no hace falta un paso
  aparte.
- **Verificación**
  - El paquete vive en `$CFG->dataroot/lang/pt_br/` (¡NO en `<docroot>/lang/`, que solo
    trae el inglés `en` incluido con el código!). Confirmado no vacío (~200 archivos).
  - `SELECT value FROM mdl_config WHERE name='lang'` → `pt_br`.
- **DoD**
  - [x] Paquete pt_br instalado (activación en Fase 2).