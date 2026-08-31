# SDD-02 — Fase 2: Configuración Base

> Prereq: Fase 1 completa (SDD-INF-*). ESD ref: `docs/02-autenticacion.md`, `docs/08-legal-lgpd.md`.

---

### SDD-CFG-01 — Forzar idioma pt_BR (único) ✅ (2026-08-30)
- **Pasos ejecutados**:
  ```bash
  php admin/cli/cfg.php --name=lang --set=pt_br
  php admin/cli/cfg.php --name=langlist --set="pt_br"
  php admin/cli/cfg.php --name=langmenu --set=0
  ```
  ⚠️ **La spec traía `--value=`, el flag real en Moodle 4.5 es `--set=`** (confirmado con
  `admin/cli/cfg.php --help`); `--value=` da "Opções não reconhecidas".
- **Verificación**
  - `mdl_config`: `lang=pt_br`, `langlist=pt_br`, `langmenu=0`.
  - Portada y login cargan con `<title>` en portugués ("Página inicial", "Acesso ao site"),
    labels visibles en pt-BR ("Identificação de usuário", "Senha").
- **DoD**
  - [x] pt_BR forzado y selector oculto.

---

### SDD-CFG-02 — Autenticación SIN auto-registro (licencia de uso personal)
> ⚠️ Licencia del curso confirmada como **uso personal, un solo usuario** (2026-08-30,
> ver `docs/08-legal-lgpd.md` RF-L-08). Auto-registro público **queda deshabilitado**.
- **Pasos ejecutados** (CLI, `admin/cli/cfg.php --name=X --set=Y`):
  1. `auth = manual` (el instalador había dejado también `email` activo por defecto; se quitó).
  2. `registerauth` ya estaba vacío desde la instalación (self-registration nunca se activó).
  3. `guestloginbutton = 0` (estaba en `1` por defecto — el botón de invitado se mostraba).
  4. `enrol_plugins_enabled = manual` (estaba `manual,guest,self,cohort` por defecto — se quitaron `guest`/`self`/`cohort`, innecesarios en modo de un solo usuario).
  5. **Cuenta del Dr. creada** (2026-08-30), con los datos confirmados por el desarrollador:
     `Dr. Juan Marcelo Cabello Mérida`, `jmcabello_merida@hotmail.com`. Creada vía la API
     oficial `user_create_user()` (no hay CLI de un solo usuario en Moodle core; se evitó
     el formulario web para no depender de scraping de sesskey/CSRF) en un script temporal
     que se borró justo después de usarlo. Usuario: `dr_jcabello`, `auth=manual`,
     `lang=pt_br`, clave temporal generada + `auth_forcepasswordchange=1` (preferencia en
     `mdl_user_preferences`) para que el Dr. la cambie en su primer login.
- **Verificación**
  - `registerauth` vacío ✓.
  - `curl -I https://portal.examenes-neuro.com/login/signup.php` → `404` ✓.
  - `SELECT id,username,email FROM mdl_user WHERE deleted=0` → `guest` (1), `admin` (2,
    `dev@somoscdv.com`), `dr_jcabello` (3, `jmcabello_merida@hotmail.com`) — exactamente
    los 3 esperados, ningún alta pública.
  - Login real probado por `curl`: `303` → `Location: .../login/change_password.php`
    (fuerza cambio de clave en el primer ingreso, como se esperaba).
- **DoD**
  - [x] Self-registration deshabilitado y verificado.
  - [x] Guest deshabilitado (login button + enrolment plugin).
  - [x] Cuenta única del Dr. creada manualmente y login funcional.

---

### SDD-CFG-03 — Roles y permisos mínimos ✅ (2026-08-30, sin cambios necesarios)
- **Verificación** (todo ya venía correcto por defecto del instalador)
  - `defaultuserroleid = 7` → rol `user` (Authenticated user). Correcto.
  - `mdl_role_assignments` → vacío (nadie tiene `teacher`/`editingteacher`/`manager` asignado).
  - `siteadmins = 2` → solo el usuario `admin`.
- **DoD**
  - [x] Solo roles `admin` y `user` en uso (no hay usuarios `user` reales todavía, pero la
        configuración base es correcta para cuando se cree la cuenta del Dr.).

---

### SDD-CFG-04 — Seguridad base (debug off, captcha, políticas) ✅ (2026-08-30)
- **Pasos ejecutados**:
  1. `protectusernames=1`, `passwordpolicy=1` (vía `admin/cli/cfg.php --set=`, no `--value=`).
  2. `$CFG->preventexecpath = true;` agregado a `config.php` (resuelve el aviso "Caminhos
     dos executáveis" — no necesitamos configurar rutas de ejecutables externos).
  3. **Hardening extra no previsto en la spec, encontrado al revisar el Informe de
     seguridad** (`Site admin → Reports → Security overview`): 12 archivos internos del
     código de Moodle eran descargables públicamente sin autenticación —
     `composer.json`, `composer.lock`, `*.dist` (`phpunit.xml.dist`, `phpcs.xml.dist`),
     `behat.yml.dist`, `admin/environment.xml`, `db/install.xml` (de varios plugins),
     varios `README*`/`readme.txt`/`readme_moodle.txt`, `upgrade.txt`/`UPGRADING*.md`, y
     todo el contenido de cualquier carpeta `tests/` (incluyendo `tests/behat/` y
     `tests/fixtures/`). Ninguno contenía secretos, pero es exposición innecesaria de
     estructura interna — se corrigió agregando reglas a `.htaccess` (Apache, ya soportado
     por este hosting):
     ```apache
     <IfModule mod_rewrite.c>
     RewriteEngine On
     RewriteRule ^(.*/)?tests/ - [F,L]
     </IfModule>
     <FilesMatch "(\.dist$|^composer\.(json|lock)$)">
     Require all denied
     </FilesMatch>
     <FilesMatch "(?i)^(install\.xml|environment\.xml|readme\.txt|readme_moodle\.txt|readme\.md|upgrade\.txt|upgrading(-current)?\.md)$">
     Require all denied
     </FilesMatch>
     ```
     Se eliminó además `behat.yml.dist` del docroot (scaffold de pruebas, no se usa en producción).
     Verificado por `curl` que las ~20 rutas antes públicas devuelven ahora `403` (Moodle
     documenta explícitamente en `lib/classes/check/environment/publicpaths.php` que
     "a 404 é o ideal, mas um 403 também é aceitável").
- **Verificación**
  - `mdl_config`: `protectusernames=1`, `passwordpolicy=1`, `auth=manual`,
    `guestloginbutton=0`, `enrol_plugins_enabled=manual`.
  - Todas las rutas antes expuestas devuelven `403` (verificado con `curl` una por una).
  - **Nota abierta**: el badge de "Verificar todos os caminhos públicos/privados" en la UI
    del Informe de seguridad sigue mostrando "Erro" pese a que cada ruta individual listada
    ya reporta "(Retornado 403, o ideal deveria ser 404)" — que según el propio código del
    check debería bajar la severidad a informativa. No se encontró la causa exacta (se
    descartó como caché tras `purge_caches.php`, sin éxito) y no se siguió investigando
    para no perder tiempo en un posible artefacto de renderizado. El estado real verificado
    por fuera de la UI (vía `curl`, archivo por archivo) es correcto.
- **DoD**
  - [x] Endurecimiento de config aplicado y verificado por `curl`/SQL.
  - [~] Informe de seguridad de la UI: sin alertas fuera de esta única fila, cuya causa
        exacta del badge queda como duda abierta (no bloqueante, ver nota arriba).

---

### SDD-CFG-05 — Textos legales LGPD (aviso + cookies + términos) ⏸️ PENDIENTE (no ejecutada)
> **Por qué se dejó pendiente en vez de improvisar**: esta tarjeta es redacción de
> contenido legal real (Aviso de Privacidade, Termos de Uso), no configuración técnica.
> `docs/08-legal-lgpd.md` ya da la estructura y el contenido mínimo, pero falta el dato
> de contacto real para el aviso (`docs/13-info-necesaria.md` §4, aún sin responder) y
> se prefiere revisar el texto con el desarrollador antes de publicarlo en el sitio real
> (a diferencia de una config técnica, un texto legal mal redactado si se publica
> "provisionalmente" puede generar confusión o compromiso real frente al usuario).
> Además, como ya NO hay auto-registro (RF-A-02 actualizado), el checkbox "Li e aceito"
> en el registro (paso 3 original) no aplica tal cual — el mecanismo correcto en Moodle es
> el **Site Policy** (`admin/tool/policy`), que pide aceptación en el **primer login** de
> cualquier cuenta (incluida la única cuenta del Dr., creada manualmente).
- **Pasos pendientes** (cuando se resuelva el bloqueo):
  1. Redactar Aviso de Privacidade y Termos de Uso en pt-BR (base: `docs/08-legal-lgpd.md`).
  2. Cargarlos como **Site Policy** vía *Site admin → Users → Policies → Manage policies*
     (o `admin/tool/policy/managedocs.php`), marcando como obligatoria.
  3. Banner de cookies: evaluar si el nativo de Moodle 4.5 alcanza o hace falta un plugin
     ligero; documentar la decisión aquí.
- **DoD**
  - [ ] Aviso, términos y banner operativos en pt-BR. **No iniciado.**

---

### SDD-CFG-06 — Verificación transversal de idioma ✅ (2026-08-30)
- **Verificación realizada** (vía `curl`, anónimo y logado como `admin`):
  - Portada: `<title>Página inicial | plataforma</title>`.
  - Login: `<title>Acesso ao site | plataforma</title>`, labels visibles
    "Identificação de usuário" / "Senha" (los matches de "username"/"password" en el HTML
    son atributos técnicos `name=`/`id=`, no texto visible).
  - Informe de seguridad (admin): `<title>Verificações de segurança | ...</title>`.
  - No se encontró texto de UI en inglés en ninguna de las 3 páginas. (No hay página de
    registro público que revisar — self-registration deshabilitado.)
- **DoD**
  - [x] Idiomas no deseados ausentes.