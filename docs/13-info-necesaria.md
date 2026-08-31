# Información pendiente antes de programar

> Checklist de datos que el desarrollador debe reunir ante de ejecutar las fases SDD.
> Tener todo esto evita bloqueos y reprocesos durante la implementación.

## 1. Identidad y dominio
- [x] **Dominio real** del sitio: `portal.examenes-neuro.com` (respondido 2026-08-30, volcado en `specs/00-variables-fijas.md`).
- [x] ¿El hosting ya tiene el subdominio apuntado e SSL? Sí, ambos ya estaban listos (SSL Let's Encrypt válido).
- [ ] **Nombre visible de la plataforma** en pt-BR (ej. "Portal de Estudos do Dr. Fulano"). Se usó temporalmente "Plataforma de Estudos" para no bloquear la instalación (Fase 1) — cambiar cuando el Dr. confirme el nombre final (campo trivial de editar en Moodle).

## 2. Credenciales y acceso (no versionar)
- [x] Acceso al hosting: SSH por llave (`matiasf6@sh006.hostgator.net`), sin necesidad de cPanel UI (se usó `uapi` por SSH para todo lo que normalmente requeriría el panel).
- [x] Datos de BD: creada `matiasf6_moodle_db` / `matiasf6_moodle_user`, clave en `.env.secrets` (no versionado).
- [x] Correo del administrador: `dev@somoscdv.com` (del desarrollador) es correcto y
  **permanente** — según el modelo de roles (`docs/02-autenticacion.md`), el admin del
  sitio es el desarrollador, no el Dr. El Dr. tiene su **propia cuenta separada**
  (`dr_jcabello`, `jmcabello_merida@hotmail.com`, rol `user`), creada 2026-08-30.
- [ ] Acceso al **canal de YouTube del Dr.** (subir videos no listados).
- [ ] Acceso al **Dropbox del Dr.** (estructura de carpetas).

## 3. Contenido (dr. entrega)
- [ ] **Número real de módulos** y su orden (ej. Módulo 01..05).
- [ ] **Cantidad real de videos** por módulo y su duración/tamaño.
- [ ] **Cantidad de audios MP3** (o "no hay").
- [ ] **Cantidad de documentos** PDF/DOCX/TXT.
- [ ] ¿Carpetas de Dropbox ya organizadas por módulo/unidad o hay que organizarlas?

## 4. Legal (bloqueante de acceso múltiple)
- [x] **Licencia del curso comprado**: uso **personal, un solo usuario** (respondido 2026-08-30).
  → la plataforma queda de uso del Dr. (1 cuenta principal, sin auto-registro).
- [ ] Datos de contacto para el aviso de privacidad (email de contacto).

## 5. Diseño / identidad visual
- [ ] **Logo** del Dr./consultorio (SVG o PNG).
- [ ] **Paleta de colores** (o dejamos la propuesta del tema: azul `#1565c0`).
- [ ] Frase de bienvenida del hero (pt-BR) y texto del pie (footer).

## 6. Técnico del hosting
- [x] ¿PHP 8.1+ activo y extensiones? Sí — PHP 8.2 fijado para el subdominio, todas las extensiones necesarias presentes.
- [x] ¿Se puede ubicar `moodledata/` FUERA de `public_html`? Sí — de hecho el docroot del subdominio ni siquiera está bajo `public_html/` (estructura propia por subdominio en esta cuenta).
- [x] ¿`upload_max_filesize` permite ≥256 MB? Sí, 512 MB.
- [x] ¿cPanel permite Cron Jobs? Sí, vía `crontab` de usuario por SSH (no vía `uapi`, ese módulo no está expuesto en esta cuenta).

## 7. Observaciones / decisiones menores aún abiertas
- [x] ¿Auto-registro abierto a cualquiera o solo invitación del admin? → **Ninguno**:
  self-registration deshabilitado; el admin crea manualmente la única cuenta (el Dr.),
  consecuencia directa de la licencia de uso personal (§4).
- [ ] ¿Los usuarios necesitan ver "Mis cursos" en el menú o bastan las tarjetas de portada?
- [ ] Zona horaria final.

> Nota: una vez respondido todo, completar `specs/00-variables-fijas.md` y arrancar
> la **Fase 1** (`specs/01-fase-infra.md`).