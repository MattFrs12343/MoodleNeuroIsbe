# Información pendiente antes de programar

> Checklist de datos que el desarrollador debe reunir ante de ejecutar las fases SDD.
> Tener todo esto evita bloqueos y reprocesos durante la implementación.

## 1. Identidad y dominio
- [ ] **Dominio real** del sitio (ej. `portal.consultorio.com.br`) → reemplazar en `specs/00-variables-fijas.md`.
- [ ] ¿El hosting ya tiene el subdominio apuntado e SSL?
- [ ] **Nombre visible de la plataforma** en pt-BR (ej. "Portal de Estudos do Dr. Fulano").

## 2. Credenciales y acceso (no versionar)
- [ ] Acceso cPanel (usuario/clave).
- [ ] Datos de BD a crear (`moodle_db`, `moodle_user`, clave).
- [ ] Correo del administrador (para `--adminemail`).
- [ ] Acceso al **canal de YouTube del Dr.** (subir videos no listados).
- [ ] Acceso al **Dropbox del Dr.** (estructura de carpetas).

## 3. Contenido (dr. entrega)
- [ ] **Número real de módulos** y su orden (ej. Módulo 01..05).
- [ ] **Cantidad real de videos** por módulo y su duración/tamaño.
- [ ] **Cantidad de audios MP3** (o "no hay").
- [ ] **Cantidad de documentos** PDF/DOCX/TXT.
- [ ] ¿Carpetas de Dropbox ya organizadas por módulo/unidad o hay que organizarlas?

## 4. Legal (bloqueante de acceso múltiple)
- [ ] **Licencia del curso comprado**: ¿permite acceso a terceros o es solo uso personal del Dr.?
  - Si es solo personal → la plataforma queda de uso del Dr. (1 cuenta principal).
- [ ] Datos de contacto para el aviso de privacidad (email de contacto).

## 5. Diseño / identidad visual
- [ ] **Logo** del Dr./consultorio (SVG o PNG).
- [ ] **Paleta de colores** (o dejamos la propuesta del tema: azul `#1565c0`).
- [ ] Frase de bienvenida del hero (pt-BR) y texto del pie (footer).

## 6. Técnico del hosting
- [ ] ¿PHP 8.1+ activo y extensiones? (intl, zip, gd, mbstring, curl, soap, opcache)
- [ ] ¿Se puede ubicar `moodledata/` FUERA de `public_html`? (en cPanel: carpeta en home)
- [ ] ¿`upload_max_filesize` permite ≥256 MB? (por MP3)
- [ ] ¿cPanel permite Cron Jobs?

## 7. Observaciones / decisiones menores aún abiertas
- [ ] ¿Auto-registro abierto a cualquiera o solo invitación del admin? (opción "only invited" = disabled public signup)
- [ ] ¿Los usuarios necesitan ver "Mis cursos" en el menú o bastan las tarjetas de portada?
- [ ] Zona horaria final.

> Nota: una vez respondido todo, completar `specs/00-variables-fijas.md` y arrancar
> la **Fase 1** (`specs/01-fase-infra.md`).