# ESD-04 — Reproductor y Descarga de Videos

> **Especificación de Ingeniería · Harness Engineer**
> Versión 1.0 · 2026-08-28 · Prioridad: **Alta**
> Relacionada con: `00-maestro.md` (ADR-3, ADR-8)

---

## 1. Propósito
Permitir que cada **unidad** de un módulo tenga su(s) **video(s) de clase** incrustado(s)
desde YouTube (modo "não listado"/no listado), reproducible en la propia página, y que el
usuario pueda **descargarlos** desde el **Dropbox del Dr.** (origen legal de los archivos),
sin almacenar el video en el hosting.

## 2. Alcance
- Definición del mecanismo de embed (recurso URL de Moodle o HTML embebido).
- Política de visibilidad del video en YouTube (no listado).
- **Descarga** mediante enlace privado de Dropbox por cada video.
- Configuración del reproductor (diseño, proporción, restricciones).
- Capacidad: **200+ videos** con crecimiento abierto.

## 3. Decisiones y reglas de negocio

- RN-V-01: El video **nunca se sube a Moodle**; se referencia por **URL de YouTube**.
- RN-V-02: En YouTube, subir el video con visibilidad **"Não listado"** (No listado)
  para que solo quienes tengan el enlace puedan verlo.
- RN-V-03: Cada video se agrega al módulo como **recurso "URL"** apuntando al enlace de
  embed (`https://www.youtube.com/embed/<VIDEO_ID>`).
- RN-V-04: El embed debe ser **responsive** (relación 16:9) y reproducción interna
  (sin salir de la página) con `autoplay=0`.
- RN-V-05: Título en la unidad = título del video (pt-BR) para no depender del de YouTube.
- RN-V-06: **Descarga**: junto al embed se muestra un botón "Baixar vídeo" cuyo enlace es
  un **enlace compartido de Dropbox** (carpeta o archivo del Dr.). El enlace es **privado**
  (compartido solo con la plataforma), no público.
- RN-V-07: Si el hueco en el curso no tiene link de Dropbox, el botón de descarga NO se muestra.
- RN-V-08: Dropbox se usa **solo para descarga**; nunca para embed (el embed siempre es YouTube).

> Ejemplo de enlace de embed:
> `https://www.youtube-nocookie.com/embed/VIDEO_ID?rel=0&cc_load_policy=1`
> Usar dominio `youtube-nocookie.com` para **reducir cookies de terceros** (privacidad).

## 4. Requisitos funcionales

- RF-V-01: Agregar el recurso desde la sección: *Agregar actividad o recurso* → **URL**
  (nombre en pt-BR como "Vídeo da aula 3").
- RF-V-02: En "Ajustes del recurso URL":
  - Tipo de ventana: **Incrustar** (para embebido) o "Igual" (si se abre dentro del iframe).
  - Mostrar descripción en la página del curso: Sí.
- RF-V-03: El reproductor debe renderizarse con `iframe` y proporción 16:9 vía CSS del tema hijo.
- RF-V-04: Restricciones opcionales por unidad: restringir por grupo si el contenido es
  específico de un grupo (postergado para v1.1).
- RF-V-05: Debe funcionar en móvil (touch, pantalla completa).

## 5. Descarga de videos (Dropbox)

| # | Requisito | Detalle |
|---|---|---|
| 1 | Origen del archivo | Carpeta Dropbox del Dr., estructurada igual que la plataforma (`MÓDULO 01/Unidade 1/video.mp4`) |
| 2 | Enlace | Compartido por Dropbox con permiso "solo puede ver/descargar" (no editar) |
| 3 | Ubicación en Moodle | Recurso **URL** adicional junto al embed (mismo contenido `mod_url`, o botón en la descripción del recurso) |
| 4 | Visualización | Ícono/etiqueta "Baixar" con `target=_blank` (abre Dropbox) |
| 5 | Reporte de enlaces rotos | Revisión periódica de enlaces comprobando `HEAD` (código 200) o inspección manual |

> ⚠️ Los enlaces de Dropbox pueden caducar si se cambia el permiso de la carpeta.
> Mantener el enlace **fijo** (carpeta permanente, no "transfer").

## 6. Configuración en Moodle
| # | Ajuste | Ruta | Valor |
|---|---|---|---|
| 1 | Habilitar recurso URL | Plugins → Módulos de actividades → URL | Habilitado |
| 2 | Media embed (filtro) | Filtros → Administrar filtros | `Multimedia plugins` activo y multi-lang ok |
| 3 | Formato del embed | Editor HTML → Recurso URL → Display | `Incrustar` |
| 4 | Lazy load iframes | Administración del sitio → Configuración de página → "Ajustes generales" | Opcional |
| 5 | Dominio de cookies | URL del embed | `youtube-nocookie.com` |
| 6 | Ventana de descarga | Recurso URL → Display | "Abrir en ventana nueva" (`popup`) |

## 7. Modelo de datos (nativo)
```
mdl_url : course, name, externalurl (https://www.youtube-nocookie.com/embed/...,
          o enlace Dropbox), display (embed/1 | popup), popupwidth, popupheight
mdl_course_modules : instancia url dentro de la sección del curso
```

## 7. Criterios de aceptación (Test Harness)

| ID | Prueba | Pasos minimales | Resultado esperado | Estado |
|---|---|---|---|---|
| TC-V-01 | Embed correcto | Abrir unidad con recurso URL | Video visible e incrustado en la página | ☐ |
| TC-V-02 | Responsive | Ver en móvil (380 px) y desktop | Iframe 16:9 sin desbordes | ☐ |
| TC-V-03 | No listado | Abrir el mismo video en YouTube sin enlace | No aparece en público/búsquedas | ☐ |
| TC-V-04 | 200+ videos | Crear 200 recursos de prueba o comprobación en BD | Plataforma sin degradación notable | ☐ |
| TC-V-05 | Sin cookies de terceros | Inspeccionar red con DevTools | Las llamadas van a `youtube-nocookie.com` | ☐ |
| TC-V-06 | Play interno | Clic en play | Se reproduce dentro de la página | ☐ |
| TC-V-07 | Pantalla completa | Botón fullscreen | Funciona en desktop y móvil | ☐ |
| TC-V-08 | Descarga Dropbox | Clic "Baixar" con enlace Dropbox válido | Abre Dropbox y inicia descarga del archivo | ☐ |
| TC-V-09 | Sin enlace de descarga | Recurso sin link Dropbox | No aparece botón "Baixar" | ☐ |
| TC-V-10 | Enlace caducado | Enlace Dropbox revocado | Muestra aviso/fallo controlado, se reporta | ☐ |

## 9. Riesgos y mitigaciones
| ID | Descripción | Prob. | Impacto | Mitigación |
|---|---|---|---|---|
| R-V-01 | Video marcado como "oculto" o borrado en YouTube | Media | Medio | Checklist de publicación al cargar; título descriptivo |
| R-V-02 | Youtube bloquea embed por configuración del canal | Baja | Alto | Revisar "Permitir incorporación" en YouTube Studio por video |
| R-V-03 | Degradación por 200 iframes | Baja | Baja | La página lista recursos, no carga todos los iframes simultáneamente |
| R-V-04 | Usuario no distinguir si el video funciona | Media | Baja | Mostrar miniaturas/imágenes de vista previa y estado "não listado" |
| R-V-05 | Enlace Dropbox caducado/roto | Media | Media | Enlace a carpeta permanente (no "transfer"); validación periódica |
| R-V-06 | Fuga del enlace Dropbox a terceros | Baja | Alto | Enlace privado (solo visualización), se restringe acceso a usuarios logueados; revisar permisos de enlace |

## 10. Definition of Done (DoD)
- [ ] Recurso URL con embed funcionando en una unidad de prueba.
- [ ] Proporción 16:9 responsive verificada en 3 breakpoints.
- [ ] Dominio `youtube-nocookie.com` implementado.
- [ ] Botón "Baixar" con enlace Dropbox funcional y oculto cuando no aplica.
- [ ] Documento operativo "Cómo publicar un video no listado" (para el admin).
- [ ] Prueba de 5 videos consecutivos sin errores.