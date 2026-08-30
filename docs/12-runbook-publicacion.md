# Runbook de Publicación de Contenido (Admin)

> Procedimiento **repetible** para publicar cada video/audio/documento en la plataforma.
> Uso interno del desarrollador. Complementa `specs/04-fase-carga.md`.

---

## 1. Publicar un VIDEO (paso a paso)

1. **Tener el archivo**
   - El Dr. entrega el video (mp4) en su Dropbox, carpeta:
     `MÓDULO NN/Unidade N/<nombre>.mp4`

2. **Subirlo a YouTube (no listado)**
   - Entrar a `youtube.com/upload` con la cuenta del Dr.
   - Seleccionar el archivo.
   - Título (pt-BR): `Vídeo da aula N — <tema>`.
   - **Visibilidad = "Não listado"** (No listado). NO público, NO privado.
   - Esperar a que termine la subida/procesamiento.
   - Copiar el `VIDEO_ID` de la URL (`youtube.com/watch?v=<VIDEO_ID>`).

3. **Generar el enlace de embed**
   - Formar: `https://www.youtube-nocookie.com/embed/<VIDEO_ID>`

4. **Compartir el archivo en Dropbox (para descarga)**
   - En Dropbox: clic derecho sobre el archivo → **Compartir**.
   - Permiso: **"Puede ver"** (solo visualización; NO "editar").
   - Enlace: copiar el enlace compartido (fijo, no "transfer").

5. **Registrar en `lista.csv`** (una fila por video)
   - `tipo_recurso = video_embed` → con `url_embed`.
   - `tipo_recurso = video_dropbox` (fila extra) → con `url_dropbox`, para el botón "Baixar".
   - Módulo, unidad y nombres según `specs/03-fase-estructura.md` (SDD-EST-04).

6. **Ejecutar la importación**
   ```
   php local/importcontenido/cli/importar.php --archivo=/home/USER/lista.csv --categoria=Módulos
   ```
   - Revisar el resultado en consola (0 errores esperados).

7. **Verificar en la UI**
   - Abrir el curso → unidad → el video se reproduce embebido.
   - Probar el botón "Baixar" con el enlace de Dropbox.

## 2. Publicar un AUDIO (MP3)

1. Archivo en `MÓDULO NN/Unidade N/<nombre>.mp3`.
2. Mover/copiar a `/home/USER/import/` (carpeta temporal del importador).
3. Fila en el CSV:
   - `tipo_recurso = audio_mp3`, `archivo_relativo = /home/USER/import/<nombre>.mp3`.
4. Ejecutar importación y verificar reproducción en la unidad.

## 3. Publicar un DOCUMENTO (PDF/DOCX/TXT)

1. Archivo en la carpeta de importación.
2. Fila en el CSV según tipo:
   - `doc_pdf` (previsualización), `doc_docx` (descarga forzada), `doc_txt` (texto en línea).
3. Ejecutar importación y verificar apertura/descarga.

## 4. Checklist post-publicación (por lote de videos)

- [ ] ¿El video NO aparece en búsquedas públicas? (no listado)
- [ ] ¿`youtube-nocookie.com/embed/<ID>` carga el reproductor?
- [ ] ¿El enlace Dropbox pide login (privado) y descarga bien?
- [ ] ¿El nombre del recurso está en pt-BR?
- [ ] ¿Está en la **unidad correcta** del **módulo correcto**?
- [ ] ¿Registrado en el CSV para reproducir la importación? (reproducibilidad)

## 5. Reprocesamiento (corrección de un lote)
- Corregir el CSV (nombres/enlaces).
- Re-ejecutar el importador (detecta cursos existentes por `shortname`; los recursos se agregan de nuevo).
- Si se duplicó un recurso, eliminarlo manualmente en la UI antes de re-ejecutar.