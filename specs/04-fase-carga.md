# SDD-04 — Fase 4: Carga de Contenido (Script + 200 videos)

> Prereq: Fase 3 (plantilla y cursos). ESD ref: `docs/04-videos-embed.md`, `docs/05-audio-documentos.md`,
> `docs/11-justificacion-presupuesto.md` (partes 1, 5, 6).
> **Regla**: los videos NO se suben al hosting; solo YouTube (embed) + Dropbox (descarga).

---

## Parte A — Script de importación

### SDD-CAR-01 — Crear plugin local de importación
- **Estado**: esqueleto **funcional ya entregado** en `src/local/importcontenido/`
  (revisar/ajustar sobre el Moodle instalado). Plantilla de datos:
  `specs/plantillas/lista_ejemplo.csv`.
- **Estructura a crear** (es código propio, NO toca core):
  ```
  local/importcontenido/
  ├── version.php                  (component = 'local_importcontenido', pluginversion)
  ├── lib.php                      (hook mínimos: ext_local_importcontenido_after_require_login no)
  ├── cli/importar.php             (script CLI)
  └── lang/pt_br/local_importcontenido.php
  ```
- **Contrato del CSV** (`lista.csv`, separado por `;`, UTF-8 con BOM):
  ```
  modulo_shortname;modulo_fullname;modulo_summary;unidad_n;unidad_nombre;
  tipo_recurso;nombre_recurso;descripcion;url_embed;url_dropbox;archivo_relativo
  ```
  - `tipo_recurso` ∈ `video_embed | video_dropbox | audio_mp3 | doc_pdf | doc_docx | doc_txt`
  - Para `audio_mp3`/`doc_*`: `archivo_relativo` (ruta dentro del dataroot temporal).
- **Comportamiento**
  1. Crea/encuentra el curso (`modulo_shortname`).
  2. Crea/encuentra la sección `Unidade N`.
  3. Inserta el recurso:
     - `video_embed` → `mod_url` con `externalurl = YOUTUBE_EMBED_BASE/<id>`, `display=1` (embed).
     - `video_dropbox` → `mod_url` con el enlace Dropbox, `display=3` (popup/nueva).
     - `audio_mp3`/`doc_*` → `mod_resource` con `file` subido a `moodledata/temp/...`.
  4. Asigna orden por sección (orden del CSV).
  5. Ejecuta `purge_caches` al final.
- **Comando de ejecución**
  ```
  php local/importcontenido/cli/importar.php \
      --archivo=/home/USER/lista.csv \
      --categoria=Módulos \
      --identificar_log=/tmp/import.log
  ```
- **Verificación** (con SQL)
  - `SELECT COUNT(*) FROM mdl_course_modules cm JOIN mdl_course c ON cm.course=c.id WHERE c.shortname='mod01';` → ≥ nº de recursos previstos.
  - `SELECT externalurl FROM mdl_url WHERE course=(SELECT id FROM mdl_course WHERE shortname='mod01') LIMIT 3;`
- **DoD**
  - [ ] Script creado y probado con 3 filas piloto.
  - [ ] Verificación SQL sin errores.

---

### SDD-CAR-02 — Preparar la lista de carga (inventario)
- **Pasos**
  1. Inventario desde el Dropbox del Dr.: 200 videos + audios + docs.
  2. Generar `lista.csv` en `UTF-8` con la convención de `SDD-EST-04`.
  3. Para cada video: anotar `<VIDEO_ID>` de YouTube (tras subida) y su enlace Dropbox (compartido "solo ver/descargar").
- **Verificación**
  - El CSV tiene tantas filas `video_embed` como videos del curso.
  - 100% de filas pasan validación del script (dry-run).
- **DoD**
  - [ ] CSV completo (200 videos + audios + docs). 
  - [ ] Copia del CSV versionada localmente (no con secretos).

---

## Parte B — Videos (YouTube no listado)

### SDD-CAR-03 — Subir videos a YouTube
- **Pasos** (cuenta del Dr.)
  1. Por video: `youtube.com/upload` → visibilidad **"Não listado"**.
  2. Título = nombre del recurso (pt-BR). Playlist por módulo opcional.
  3. Anotar el `VIDEO_ID` en el CSV.
- **Verificación**
  - URL pública del video → el video NO aparece en búsquedas (no listado).
  - `https://www.youtube-nocookie.com/embed/<VIDEO_ID>` carga el reproductor.
- **DoD**
  - [ ] 200 videos subidos "não listados" y mapeados en CSV.

---

### SDD-CAR-04 — Enlaces Dropbox de descarga
- **Pasos**
  1. En Dropbox del Dr.: estructura `MÓDULO NN/Unidade N/<archivo>` (espejo).
  2. Compartir carpeta/archivo con **permiso de solo visualización** (no editar).
  3. Copiar enlace fijo (no "transfer") al CSV.
- **Verificación**
  - Abrir enlace sin sesión Dropbox → pide login para ver (privado).
  - Descarga funciona para usuarios autorizados.
- **DoD**
  - [ ] Un enlace estable por video en el CSV.

---

### SDD-CAR-05 — Audios y documentos
- **Pasos**
  1. Subir MP3/PDF/DOCX/TXT a una carpeta temporal del dataroot.
  2. En `lista.csv` referenciar `archivo_relativo`.
  3. Ejecutar importación → se crean `mod_resource`.
- **Verificación**
  - `SELECT COUNT(*) FROM mdl_files WHERE component='mod_resource';` > 0.
- **DoD**
  - [ ] Materiales cargados y servidos desde `filedir`.

---

### SDD-CAR-06 — Lista blanca de extensiones y límites
- **Pasos** (UI: *Site admin → Security → Site security settings*)
  1. `Extensiones de archivos permitidas` = `mp3, pdf, docx, txt`.
  2. `upload_maxfilesize` (sitio) = 256M para MP3.
- **Verificación**
  - Intentar subir `.exe` → rechazado.
  - Subir MP3 de 300 MB → rechazado por tamaño.
- **DoD**
  - [ ] Solo mp3/pdf/docx/txt aceptados.

---

### SDD-CAR-07 — Carga completa + QA del contenido
- **Pasos**
  1. Ejecutar importación completa (con `--identificar_log`).
  2. Revisar log: x filas OK, y errores (0 esperados).
  3. Recorrer 10 cursos al azar: video embebido + botón descarga + docs.
- **Verificación**
  - Log sin errores; muestreo 10 cursos sin fallos.
- **DoD**
  - [ ] Carga íntegra y verificada.
  - [ ] Matriz TC de `docs/04-videos-embed.md` (TC-V-01..10) y `docs/05-audio-documentos.md` (TC-D-01..08) ejecutada.