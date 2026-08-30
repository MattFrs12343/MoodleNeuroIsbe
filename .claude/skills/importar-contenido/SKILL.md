---
name: importar-contenido
description: Contrato del plugin local_importcontenido y su script CLI (formato del CSV, tipos de recurso, --dryrun, verificación SQL). Usar al ejecutar o modificar src/local/importcontenido.
---

# Contrato de `local_importcontenido`

> Fuente: `src/local/importcontenido/`. Referencia: `specs/04-fase-carga.md` (SDD-CAR-01).

## Formato del CSV (`lista.csv`)
Separador `;`, UTF-8 con BOM. Cabecera obligatoria:
```
modulo_shortname;modulo_fullname;modulo_summary;unidad_n;unidad_nombre;
tipo_recurso;nombre_recurso;descripcion;url_embed;url_dropbox;archivo_relativo
```
Plantilla de ejemplo (las 6 filas tipo): `specs/plantillas/lista_ejemplo.csv`.

`tipo_recurso` ∈ `video_embed | video_dropbox | audio_mp3 | doc_pdf | doc_docx | doc_txt`.
Para `audio_mp3`/`doc_*` es obligatorio `archivo_relativo` (ruta existente en disco).

## Convención `display` del `mod_url` / `mod_resource`
- `video_embed` → `mod_url`, `display=1` (embebido en la página).
- `video_dropbox` → `mod_url`, `display=3` (nueva ventana/popup).
- `doc_docx` → `mod_resource`, `display=1` fuerza descarga; el resto (`pdf`, `txt`) usa
  el valor por defecto (previsualización/en línea).

## Uso del script
```
php local/importcontenido/cli/importar.php --archivo=lista.csv --categoria=Módulos [--dryrun]
```
- `--dryrun`: valida y muestra qué se crearía, sin tocar la base de datos. **Usar siempre
  primero** con un CSV nuevo o modificado.
- El script es idempotente por curso: detecta `modulo_shortname` existente y no lo duplica;
  los recursos SÍ se vuelven a crear si se re-ejecuta (evitar re-importar filas ya cargadas).
- Requiere `course/lib.php` y `course/modlib.php` (este último provee `create_module()`).

## Verificación SQL post-import
```sql
SELECT COUNT(*) FROM mdl_course_modules cm JOIN mdl_course c ON cm.course=c.id
  WHERE c.shortname='mod01';
SELECT externalurl FROM mdl_url WHERE course=(SELECT id FROM mdl_course WHERE shortname='mod01') LIMIT 3;
SELECT COUNT(*) FROM mdl_files WHERE component='mod_resource';
```

## Estado conocido / pendiente
- `lib.php` expone `local_importcontenido_render_modulos_grid()` para pintar la grilla de
  tarjetas, pero **no está conectada a ningún filtro/bloque/página todavía**. La decisión
  de v1 en `docs/06-portada-tema.md` §6 es usar **bloques nativos + HTML/CSS** en la
  portada; si se decide usar esta función en su lugar, falta implementar el mecanismo de
  invocación (p. ej. un filtro de texto) — no asumir que ya funciona en producción.
- No modificar el contrato del CSV sin actualizar esta skill y `specs/04-fase-carga.md` a la vez.
