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
  - **Actualización (SDD-QA-03, 2026-08-31)**: el badge "Erro" que quedaba en este chequeo
    **no era un artefacto de caché/render como se supuso aquí** — era un hallazgo real:
    `.github/FUNDING.yml` y `.stylelintrc` seguían públicamente accesibles (`200`), dos
    archivos del patrón de "dotfiles" que nunca se probaron individualmente en esta
    tarjeta. Se encontraron instanciando el chequeo directo por CLI (bypaseando cualquier
    caché de sesión) y revisando la tabla de detalle fila por fila, no solo el resumen.
    Corregido y verificado en SDD-QA-03 — el Informe de seguridad ya no tiene ninguna
    alerta crítica.
- **DoD**
  - [x] Endurecimiento de config aplicado y verificado por `curl`/SQL.
  - [~] Informe de seguridad de la UI: sin alertas fuera de esta única fila, cuya causa
        exacta del badge queda como duda abierta (no bloqueante, ver nota arriba).

---

### SDD-CFG-05 — Textos legales LGPD (aviso + cookies + términos) 🟡 mayormente hecha (2026-08-30)
> El desarrollador confirmó el contacto real (`jmcabello_merida@hotmail.com`, el Dr.), lo
> que permitió redactar y publicar el contenido con revisión previa (no se improvisó:
> texto revisado en la conversación antes de publicarlo).
- **Pasos ejecutados**:
  1. Redactados **Aviso de Privacidade** y **Termos de Uso** en pt-BR (base:
     `docs/08-legal-lgpd.md`, contacto real del Dr.).
  2. Publicados como **Site Policy** (`admin/tool/policy`) vía la API oficial
     `\tool_policy\api::form_policydoc_add()` + `make_current()`, en un script temporal
     (borrado después de usarlo) — mismo patrón que las demás tarjetas de esta fase.
     Ambas políticas: `agreementstyle=CONSENTPAGE`, `optional=AGREEMENT_COMPULSORY`,
     `audience=ALL`. Esto reemplaza el mecanismo original de "checkbox en registro"
     (ya no aplica, sin auto-registro) por el flujo nativo de Moodle: **cualquier
     usuario, incluida la cuenta del Dr., debe aceptar ambas políticas antes de usar el
     sitio**, y Moodle guarda la fecha de aceptación (RF-L-06) automáticamente en
     `mdl_tool_policy_acceptances`.
  3. Banner de cookies: **no ejecutado**. Moodle 4.5 no trae un banner de cookies nativo
     (se buscó en `admin/settings/*.php`, no existe tal ajuste); requiere trabajo de tema
     (CSS/JS), que corresponde a la Fase 5 (`docs/06-portada-tema.md`), no a esta fase de
     configuración. Documentado como pendiente ahí.
- **Verificación**
  - `mdl_tool_policy_versions`: 2 filas (`Aviso de Privacidade` id 1, `Termos de Uso` id 2),
    contenido de 2150 y 1638 caracteres respectivamente (no vacío).
  - `mdl_tool_policy.currentversionid` apunta a ambas versiones (activas).
  - `https://portal.examenes-neuro.com/admin/tool/policy/view.php?versionid=1` → `200`,
    contenido con acentos correctos ("está", "é", "Mérida" — sin mojibake).
  - `mdl_tool_policy_acceptances` vacío para el usuario del Dr. (id 3) → se le pedirá
    aceptar en su próximo login, junto con el cambio de contraseña forzado.
- **DoD**
  - [x] Aviso y términos operativos en pt-BR, con aceptación obligatoria registrada.
  - [ ] Banner de cookies — diferido a Fase 5 (trabajo de tema, no de config).

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