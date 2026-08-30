---
name: publicar-contenido
description: Procedimiento paso a paso para publicar un video, audio o documento nuevo (o corregir un lote ya publicado). Usar cada vez que se sube contenido a la plataforma.
---

# Publicar contenido (video / audio / documento)

> Resume `docs/12-runbook-publicacion.md`. Contrato de datos en `specs/04-fase-carga.md`
> y convención de nombres en `specs/03-fase-estructura.md` (SDD-EST-04).

## Video
1. Archivo en Dropbox del Dr.: `MÓDULO NN/Unidade N/<nombre>.mp4`.
2. Subir a YouTube con la cuenta del Dr., visibilidad **"Não listado"**, título
   `Vídeo da aula N — <tema>`. Copiar `VIDEO_ID`.
3. Embed: `https://www.youtube-nocookie.com/embed/<VIDEO_ID>`.
4. Compartir el mismo archivo en Dropbox con permiso **"Puede ver"** (no editar); copiar
   el enlace fijo (no "transfer").
5. Registrar **dos filas** en `lista.csv`: `tipo_recurso=video_embed` (con `url_embed`) y
   `tipo_recurso=video_dropbox` (con `url_dropbox`, para el botón "Baixar").
6. Ejecutar `php local/importcontenido/cli/importar.php --archivo=lista.csv --categoria=Módulos`.
7. Verificar en UI: video embebido reproduce, botón "Baixar" descarga desde Dropbox.

## Audio (MP3)
1. Copiar a la carpeta temporal del importador.
2. Fila CSV: `tipo_recurso=audio_mp3`, `archivo_relativo=<ruta>`.
3. Importar y verificar reproducción.

## Documento (PDF/DOCX/TXT)
1. Copiar a la carpeta temporal.
2. Fila CSV según tipo: `doc_pdf` (previsualiza), `doc_docx` (fuerza descarga), `doc_txt` (en línea).
3. Importar y verificar apertura/descarga.

## Checklist post-publicación (por lote)
- [ ] Video NO aparece en búsquedas públicas de YouTube (no listado).
- [ ] `youtube-nocookie.com/embed/<ID>` carga el reproductor.
- [ ] Enlace Dropbox pide login (privado) y descarga bien.
- [ ] Nombre del recurso en pt-BR y siguiendo la convención SDD-EST-04.
- [ ] Unidad y módulo correctos.
- [ ] Fila registrada en el CSV (reproducibilidad de la importación).

## Reprocesamiento
Corregir el CSV y re-ejecutar el importador (detecta cursos existentes por `shortname`).
Si un recurso quedó duplicado, eliminarlo manualmente en la UI antes de re-ejecutar.
