# Información pendiente antes de programar

> Checklist de datos que el desarrollador debe reunir ante de ejecutar las fases SDD.
> Tener todo esto evita bloqueos y reprocesos durante la implementación.

## 1. Identidad y dominio
- [x] **Dominio real** del sitio: `portal.examenes-neuro.com` (respondido 2026-08-30, volcado en `specs/00-variables-fijas.md`).
- [x] ¿El hosting ya tiene el subdominio apuntado e SSL? Sí, ambos ya estaban listos (SSL Let's Encrypt válido).
- [x] **Nombre visible de la plataforma**: **"NeuroIsbe — Portal de Estudos"** (resuelto 2026-08-31, coincide con el subdominio `neuroisbe.examenes-neuro.com` ya existente en la cuenta — es una marca establecida del cliente, no un nombre inventado).

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
- [x] **Logo**: recibido 2026-08-31 (`assets/img3.png`), usado en el tema (ver
  `specs/05-fase-tema.md` SDD-TEM-04). También se recibió una imagen para el fondo del
  hero (`img6.png`), ya aplicada.
- [x] **Paleta de colores**: definida por el desarrollador (2026-08-31) — azules
  clínicos acordes a un proyecto de un doctor: azul marino `#0A2540`, azul oscuro
  `#134074` (primario), azul medio `#1B5E9B`, celeste `#3E92CC` / `#8ECAE6` (acento),
  gris `#5C6B7A`, blanco azulado `#F1F5FA`. Reemplaza la propuesta teal anterior.
- [x] Frase de bienvenida del hero y texto del pie (pt-BR): hero "NeuroIsbe" / "Videoaulas,
  materiais e acompanhamento…"; footer "NeuroIsbe · © 2026" + enlaces legales. Ajustables.

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