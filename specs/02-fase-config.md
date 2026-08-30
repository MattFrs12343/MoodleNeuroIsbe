# SDD-02 — Fase 2: Configuración Base

> Prereq: Fase 1 completa (SDD-INF-*). ESD ref: `docs/02-autenticacion.md`, `docs/08-legal-lgpd.md`.

---

### SDD-CFG-01 — Forzar idioma pt_BR (único)
- **Pasos** (CLI desde `APP_DIR`):
  ```bash
  php admin/cli/cfg.php --name=lang --value=pt_br
  php admin/cli/cfg.php --name=langlist --value="pt_br"
  php admin/cli/cfg.php --name=langmenu --value=0
  ```
- **Verificación**
  - `SELECT value FROM mdl_config WHERE name='lang'` → `pt_br`.
  - Visitar portada → sin selector de idioma; texto en portugués.
- **DoD**
  - [ ] pt_BR forzado y selector oculto.

---

### SDD-CFG-02 — Autenticación + auto-registro
- **Pasos** (UI: *Site admin → Plugins → Authentication*)
  1. Activar `Manual accounts` (manual).
  2. Activar *"Email-based self registration"* (email) y elegir reCAPTCHA.
  3. En *Common settings → allowaccountssameemail = No*; `allowguestmaccess = 0`.
- **Verificación**
  - `SELECT value FROM mdl_config WHERE name='auth'` → incluye `email,manual`.
  - `/login/index.php` muestra "Criar conta".
- **DoD**
  - [ ] Registro por email activo con captcha.
  - [ ] Guest deshabilitado.

---

### SDD-CFG-03 — Roles y permisos mínimos
- **Pasos** (UI: *Site admin → Users → Permissions*)
  1. Verificar que el rol por defecto es `Authenticated user` (sin capacidades de edición).
  2. En *User policies*: dejar el rol por defecto como `user` (no asignar rol de profesor).
  3. Comprobar que el rol `teacher` NO está asignado a ningún usuario no-admin.
- **Verificación**
  - `SELECT * FROM mdl_role WHERE shortname='user'` existe y con bajo `sortorder`.
  - Usuario estándar: al abrir un curso no ve botón "Editar".
- **DoD**
  - [ ] Solo roles `admin` y `user` en uso.

---

### SDD-CFG-04 — Seguridad base (debug off, captcha, políticas)
- **Pasos** (UI + CLI)
  1. CLI: `php admin/cli/cfg.php --name=protectusernames --value=1`
     `php admin/cli/cfg.php --name=passwordpolicy --value=1`
  2. UI: *Site admin → Security* → `antivirus` = configurar (o dejar plugin por defecto).
  3. UI: *Site admin → Server → File types*: dejar solo tipos necesarios (ver Fase 4).
- **Verificación**
  - `Admin → Informe de seguridad` (Site admin → Reports → Security overview) sin rojos.
- **DoD**
  - [ ] Informe de seguridad sin alertas críticas.

---

### SDD-CFG-05 — Textos legales LGPD (aviso + cookies + términos)
- **Pasos**
  1. Crear **recurso "Página"** en la portada o archivo estático `legal/privacidade` y `legal/termos` (contenido pt-BR, ver `docs/08-legal-lgpd.md`).
  2. UI: *Site admin → Users → Policies → User policies*:
     - `Sitio uses 'banner de privacidad'`? → activar banner de cookies (o plugin ligero solo-CSS si el banner nativo no aplica a 4.5).
     - Marcar consentimiento en registro.
  3. Verificar que el registro muestra checkbox "Li e aceito o Aviso de Privacidade".
- **Verificación**
  - Página de registro: checkbox obligatorio presente.
  - Portada: banner cookies con "Aceitar / Recusar".
- **DoD**
  - [ ] Aviso, términos y banner operativos en pt-BR.

---

### SDD-CFG-06 — Verificación transversal de idioma
- **Pasos**
  1. Entrar como usuario anónimo y como usuario logado (crear cuenta de prueba).
  2. Revisar 4 páginas clave: portada, login, curso, registro → sin texto en inglés.
- **Verificación**
  - Capturas/comprobación visual: 100% pt-BR.
- **DoD**
  - [ ] Idiomas no deseados ausentes (inglés/ español ignorados).