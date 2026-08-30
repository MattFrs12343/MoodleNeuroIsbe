# SDD-01 — Fase 1: Infraestructura (Despliegue Moodle)

> Prereq: `00-variables-fijas.md` completado. ESD ref: `docs/01-infraestructura.md`.

---

### SDD-INF-01 — Verificar requisitos del hosting
- **Entrada**: credenciales cPanel.
- **Pasos**
  1. En cPanel → `MultiPHP Manager` (o "Select PHP Version") confirmar **PHP 8.1+**.
  2. Activar extensiones: `intl, zip, gd, mbstring, curl, soap, iconv, opcache, openssl`.
  3. Confirmar `memory_limit ≥ 256M` y `upload_max_filesize ≥ 256M` (editar `php.ini` o `MultiPHP INI Editor`).
  4. Crear **subdominio** `portal.<dominio>` con DocumentRoot `public_html/portal` y forzar HTTPS.
- **Verificación**
  - `php -i | grep -Ei "memory_limit|upload_max"` → valores correctos.
  - `https://portal.<dominio>` responde (puede ser página del proveedor aún).
- **DoD**
  - [ ] PHP 8.1+ con extensiones activas.
  - [ ] límites PHP correctos.
  - [ ] Subdominio + SSL operativo.

---

### SDD-INF-02 — Crear BD y usuario en MariaDB
- **Entradas**: `DB_*` de variables fijas.
- **Pasos** (phpMyAdmin → SQL o terminal)
  ```sql
  CREATE DATABASE moodle_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
  CREATE USER 'moodle_user'@'localhost' IDENTIFIED BY '<DB_PASS>';
  GRANT ALL PRIVILEGES ON moodle_db.* TO 'moodle_user'@'localhost';
  FLUSH PRIVILEGES;
  ```
- **Verificación**
  ```sql
  SELECT 1 FROM information_schema.schemata WHERE schema_name='moodle_db';
  ```
- **DoD**
  - [ ] BD y usuario creados.
  - [ ] Permisos solo sobre `moodle_db`.

---

### SDD-INF-03 — Descargar y desplegar Moodle 4.5
- **Entradas**: `MOODLE_VERSION`, `APP_DIR`.
- **Pasos**
  1. Descargar la última release **4.5.x** desde
     `https://download.moodle.org/download.php/direct/stable405/moodle-latest-405.tgz`.
  2. Subir/extraer al DocumentRoot del subdominio (`portal/`).
  3. Crear `moodledata/` FUERA de `public_html` (o protegida):
     ```bash
     mkdir -p /home/USER/moodledata && chmod 0777 /home/USER/moodledata
     ```
  4. (Si el hosting obliga a dejarlo dentro) crear `portal/moodledata` con `.htaccess`:
     ```apache
     Deny from all
     ```
- **Verificación**
  - `ls portal/admin/cli/install.php` existe.
  - `ls moodledata` existe.
- **DoD**
  - [ ] Código 4.5.x en su directorio.
  - [ ] Dataroot creado y protegido.

---

### SDD-INF-04 — Instalar Moodle por CLI (no interactivo)
- **Entradas**: todas las variables.
- **Pasos** (desde `APP_DIR`):
  ```bash
  php admin/cli/install.php \
    --lang=pt_br \
    --wwwroot=https://portal.<dominio> \
    --dataroot=/home/USER/moodledata \
    --dbtype=mariadb --dbhost=localhost \
    --dbname=moodle_db --dbuser=moodle_user --dbpass='<DB_PASS>' \
    --prefix=mdl_ \
    --fullname='Plataforma de Estudos' \
    --shortname=plataforma \
    --adminuser=admin --adminpass='<ADMIN_PASS>' \
    --adminemail=admin@<dominio> \
    --non-interactive --agree-license
  ```
- **Verificación**
  - Sin errores en consola.
  - `SELECT value FROM mdl_config WHERE name='version'` → valor 2024xxxx (4.5).
  - Navegar `https://portal.<dominio>` → portada por defecto cargando.
- **DoD**
  - [ ] Instalación completa sin warnings críticos.
  - [ ] Login admin funciona.

---

### SDD-INF-05 — Configurar `config.php` (producción)
- **Pasos** (editar `portal/config.php` al final del archivo):
  ```php
  $CFG->debug = 0;
  $CFG->debugdisplay = 0;
  $CFG->perfdebug = 0;
  $CFG->cachejs = 1;
  $CFG->enablecronkey = 1; // ver detalle en tarjeta cron
  ```
- **Verificación**
  - `php -l config.php` → sin error.
- **DoD**
  - [ ] `debug=0`, sin trazas al usuario.

---

### SDD-INF-06 — Programar cron cada 1 minuto
- **Pasos** (cPanel → Cron Jobs)
  1. Línea:
     ```
     */1 * * * * /usr/bin/php /home/USER/public_html/portal/admin/cli/cron.php >/dev/null 2>&1
     ```
  2. Confirmar ruta de PHP con `which php` en SSH (o usar `/usr/local/bin/php` si aplica).
- **Verificación**
  - `Admin → Informe del sitio` (Site admin → Reports → Status): "El cron se ha ejecutado... hace menos de 1 minuto".
- **DoD**
  - [ ] Cron activo y sin errores.

---

### SDD-INF-07 — Instalar paquete de idioma pt-BR
- **Pasos**
  ```bash
  php admin/tool/langimport/cli/langimport.php --lang=pt_br
  ```
- **Verificación**
  - `ls portal/lang/pt_br` no vacío.
- **DoD**
  - [ ] Paquete pt_br instalado (activación en Fase 2).