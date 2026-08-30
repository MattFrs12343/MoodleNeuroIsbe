# ESD-05 — Audios y Documentos

> **Especificación de Ingeniería · Harness Engineer**
> Versión 1.0 · 2026-08-28 · Prioridad: **Alta**
> Relacionada con: `00-maestro.md` (GO-3, GO-4)

---

## 1. Propósito
Permitir que cada **unidad** de un módulo contenga **audios MP3** reproducibles y
**documentos** (PDF, Word `.docx`, TXT `.txt`) visibles/descargables, alojados en el
**dataroot** de Moodle (el único contenido que ocupa disco propio).

## 2. Alcance
- Tipos de archivo admitidos y límites.
- Reproducción de MP3 integrada en la página.
- Visualización/previsualización y descarga de documentos.
- Gestión del espacio en hosting compartido.

## 3. Reglas de negocio

- RN-D-01: Audios en formato **MP3** (únicos admitidos en v1; compatibilidad máxima con
  el reproductor HTML5).
- RN-D-02: Documentos: **PDF**, **DOCX** (Word) y **TXT**. Se rechaza cualquier otro tipo.
- RN-D-03: Todo archivo se sube a Moodle y queda en el **dataroot** (`filedir`).
- RN-D-04: Límite por recurso: `upload_max_filesize` (Spec-01, recomendado 256 MB para
  MP3 largos; documentos hasta 50 MB).
- RN-D-05: Convención de nombres: `unidade_<N>_<tipo>_<descripcion>` (ej.
  `u3_audio_entrevista.mp3`, `u1_apuntes_aula.pdf`).
- RN-D-06: Los TXT se muestran **en línea** (previsualización); PDF con visor; DOCX solo
  descarga (Word no se previsualiza de forma nativa).

## 4. Requisitos funcionales

### 4.1 Audio
- RF-D-01: Agregar **recurso "Archivo"** (`file`) con el MP3 dentro de la unidad.
- RF-D-02: Reproducción con reproductor HTML5 (controles nativos) al pulsar.
- RF-D-03: Permitir descarga (botón) como configurable; por defecto **visible**.
- RF-D-04: Mostrar duración estimada en el nombre o descripción si es útil.

### 4.2 Documentos
- RF-D-05: Agregar **recurso "Archivo"** para PDF/DOCX/TXT.
- RF-D-06: PDF: abrir en visor nativo de Moodle (`/mod/resource/view.php?id=..&embed=1`).
- RF-D-07: DOCX: **descarga** directa (configurar visualización "Nav. hacia fuera").
- RF-D-08: TXT: previsualización en línea con `nomatch` de filtro plane.
- RF-D-09: Tipos restringidos: validar lista de extensiones permitidas en
  `Configuración de seguridad` (extensiones de archivo permitidas).

## 5. Configuración en Moodle

| # | Ajuste | Ruta | Valor |
|---|---|---|---|
| 1 | Tipos de archivo | Site admin → Servidor → Tipos de archivo | Permitir `mp3`, `pdf`, `docx`, `txt` |
| 2 | Extensiones permitidas | Seguridad → Configuración de seguridad → "Extensiones de archivos permitidas" | Lista blanca de las 4 extensiones |
| 3 | Límite subida | Seguridad → Configuración de seguridad → Límite de archivos para subir | 256 MB sugerido |
| 4 | Doc viewer | Plugins → Editor → Atlas/Tinymce (opcional) | No requerido; usar embebed=1 |
| 5 | Tamaño máximo por recurso | `maxbytes` en cada recurso | Configurable |

> El visor de PDF nativo de Moodle (`mod_resource`) renderiza PDF vía
> `embed` / iframe; para DOCX no hay previsualización estándar en v1 (se descarga).

## 6. Modelo de datos (nativo) y almacenamiento

```
mdl_resource : course, section (via mdl_course_modules), name, display
mdl_files    : contextid (recurso), component='mod_resource', filearea='content'
               filepath, filename, filesize, mimetype, timecreated
```
Almacenamiento físico: `$CFG->dataroot/filedir/...` (hash de contenido).

## 7. Criterios de aceptación (Test Harness)

| ID | Prueba | Pasos | Resultado esperado | Estado |
|---|---|---|---|---|
| TC-D-01 | Subir MP3 | Subir `u1_audio.mp3` | Se guarda; aparece como recurso en la unidad | ☐ |
| TC-D-02 | Reproducir MP3 | Pulsar play | Audio se reproduce sin salir | ☐ |
| TC-D-03 | Subir PDF | Subir `apuntes.pdf` | Se abre previsualización o se descarga | ☐ |
| TC-D-04 | Subir DOCX | Subir `tema.docx` | Descarga directa sin error | ☐ |
| TC-D-05 | Subir TXT | Subir `lectura.txt` | Previsualización en línea | ☐ |
| TC-D-06 | Rechazo extensión | Intentar subir `.exe` o `.zip` | Bloqueado por lista blanca | ☐ |
| TC-D-07 | Límite tamaño | MP3 > 256 MB | Mensaje de error de tamaño | ☐ |
| TC-D-08 | Descarga por rol | Usuario estándar | Solo descarga/ve, sin editar/subir | ☐ |

## 8. Riesgos y mitigaciones
| ID | Descripción | Prob. | Impacto | Mitigación |
|---|---|---|---|---|
| R-D-01 | Agotar disco del hosting con MP3 grandes | Media | Alto | Límite por archivo; revisar reporte de tamaño; mover audios históricos a YouTube/audios externos |
| R-D-02 | Formato DOCX con macros (.docm) | Baja | Medio | Bloquear `.docm`; solo permitir `.docx` sin macros |
| R-D-03 | Archivos mal nombrados | Media | Baja | Convención de nombres en runbook |
| R-D-04 | Desborde de `memory_limit` al previsualizar PDF | Baja | Baja | Previsualización vía iframe (no parseo local) |

## 9. Definition of Done (DoD)
- [ ] Subida, previsualización y descarga de MP3, PDF, DOCX, TXT verificadas.
- [ ] Lista blanca de extensiones activa y probada.
- [ ] Convención de nombres documentada.
- [ ] Reporte de uso de disco consultable (`admin/report/security` + informe de archivos).
- [ ] Límite por recurso definido (MP3 256 MB, docs 50 MB).