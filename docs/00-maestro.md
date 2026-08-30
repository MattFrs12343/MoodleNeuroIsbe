# PLATAFORMA EDUCATIVA — Documento Maestro (v1.0)

> **Estilo**: IDD (Integrated Design & Development) / Spec modular tipo Harness Engineer.
> Cada módulo es una especificación independiente (ESD — Engineering Specification Document)
> con criterios de aceptación verificables (test harness) y matriz de riesgos.

---

## 1. Identificación del proyecto

| Campo | Valor |
|---|---|
| **Nombre** | Plataforma Educativa (nombre clave: *plataforma-portal*) |
| **Versión documento** | 1.0 |
| **Fecha** | 2026-08-28 |
| **Product Owner / Cliente** | Usuario final (desarrollador único) |
| **Rol documental** | Harness Engineer — especificaciones modulares |

## 2. Descripción de negocio

La plataforma permite a **usuarios autenticados** ubicar de forma **estructurada y ordenada**
los materiales de sus clases: **videos** (embed YouTube + descarga vía Dropbox), **audios MP3**
y **documentos** (PDF, Word, TXT), agrupados y separados por **módulos**.

**Caso de uso principal**: un Dr. adquirió un curso cuyos videos le fueron entregados por
**Dropbox**. Necesita **verlos** (embed) y **descargarlos** (enlace Dropbox), todo organizado
por módulos/unidades en un solo lugar.

No existe la figura de "profesor". El **desarrollador (rol: site admin)** es quien crea,
gestione y publica todo el contenido. Los usuarios solo **ven y navegan** el material.

## 3. Objetivos y no-objetivos

### 3.1 Objetivos
- GO-1: Visualizar videos de clase embebidos (YouTube, modo no listado).
- GO-1b: **Descargar** los videos desde la plataforma (enlace a Dropbox, sin duplicarlos en hosting).
- GO-2: Organizar el contenido por módulos con una jerarquía clara (módulo → unidad → recurso).
- GO-3: Adjuntar y reproducir audios MP3 por módulo.
- GO-4: Adjuntar, descargar y visualizar documentos (PDF, Word/`docx`, `txt`) por módulo.
- GO-5: Autenticar usuarios; el desarrollador administra todo.
- GO-6: Funcionar en **hosting compartido** con PHP 8.x y **MySQL/MariaDB**.
- GO-7: Interfaz en **portugués de Brasil (pt-BR)**.
- GO-8: Portada de bienvenida con **tarjetas por módulo**.

### 3.2 No-objetivos (fuera de alcance v1)
- NO-1: Transmisión de video en vivo / streaming propio.
- NO-2: Almacenamiento masivo de video en el hosting compartido (los videos permanecen en Dropbox).
- NO-3: Edición de video o audio dentro de la plataforma.
- NO-4: Pagos, ventas o e-commerce.
- NO-5: Foros, chats ni comunicación entre usuarios.
- NO-6: Integración con sistemas externos (LTI, SIS, SSO enterprise).
- NO-7: Aplicación móvil nativa (la UI es responsive).

## 4. Decisiones de diseño clave (ADR)

| ID | Decisión | Justificación |
|---|---|---|
| ADR-1 | **Moodle no se modifica en el core** | Forkear el core rompe actualizaciones de seguridad. Se personaliza la capa de presentación (tema hijo) y se añaden plugins `local` / `mod` si se requieren. |
| ADR-2 | **Módulo = Curso con secciones** | Estructura nativa de Moodle, sin desarrollo adicional: `SCHOOL`=Categoría, `MODULE`=Curso, `UNIT`=Sección. |
| ADR-3 | **Videos: ver = embed (YouTube no listado), descargar = Dropbox** | Con +200 videos, evita 50–150 GB de disco y ancho de banda en hosting compartido (aunque sea propio). YouTube para reproducir; Dropbox (origen de los archivos del Dr.) para descargar. |
| ADR-4 | **Autenticación nativa Moodle** (login + auto-registro simplificado) | Los usuarios necesitan login; no requiere SSO ni plugins de pago. |
| ADR-5 | **MySQL/MariaDB como única BD** | Soporte nativo de Moodle, disponible en 100% de hostings compartidos. |
| ADR-6 | **Idioma pt-BR como idioma base** | Se instala `pt_br` y se marca como único idioma activo. |
| ADR-7 | **Solo un rol administrador + usuarios estándar** | No existe profesor; se minimiza la superficie de permisos. |
| ADR-8 | **Descargas de video vía enlaces privados de Dropbox** | Los videos son de un curso comprado entregado por Dropbox. El hosting solo guarda audio y documentos (GBs, no decenas de GBs). |

## 5. Arquitectura de alto nivel

```
+------------------------------------------------------------+
|                        NAVEGADOR                           |
|                (desktop / tablet / móvil)                  |
+------------------------------------------------------------+
                           |
                           v
+------------------------------------------------------------+
|                     MOODLE (LMS)                           |
|   PHP 8.x  +  MySQL/MariaDB  —  instalación enDocumentRoot  |
|                                                             |
|   Capa presentación : Tema hijo personalizado (pt-BR)      |
|   Capa funcional    : Cursos/Secciones + Actividades/Recursos|
|   Capa datos        : esquema Moodle (mdl_*)                |
|   Plugins propios   : local_confirmacceso / mod_* (si aplica) |
+------------------------------------------------------------+
                           |
                           v
+------------------------------------------------------------+
|              SERVICIOS EXTERNOS (solo lectura/embed)       |
|   • YouTube  : <iframe> embed, videos "não listados"       |
|   • Archivos : alojados en el mismo hosting (mimedir)       |
+------------------------------------------------------------+
```

### 5.1 Flujo de usuario principal
1. Usuario entra a la portada (página inicial).
2. Lista de **tarjetas por módulo** (cursos) con imagen, título y descripción.
3. Usuario hace login (si registrado) o se registra (auto-registro).
4. Usuario abre un **módulo** (curso) → ve las **unidades** (secciones).
5. Cada unidad muestra **recursos**: video (embed), audio (MP3), documentos.
6. El usuario reproduce/descarga sin poder editar.

## 6. Mapa de módulos y especificaciones

| Spec | Módulo | Estado | Prioridad |
|---|---|---|---|
| `01-infraestructura.md` | Infraestructura y despliegue | Definido | Alta |
| `02-autenticacion.md` | Autenticación y usuarios | Definido | Alta |
| `03-modulos-cursos.md` | Estructura de módulos/cursos | Definido | Alta |
| `04-videos-embed.md` | Reproductor y descarga de videos | Definido | Alta |
| `05-audio-documentos.md` | Audios y documentos | Definido | Alta |
| `06-portada-tema.md` | Portada y tema hijo | Definido | Alta |
| `07-seguridad-backups.md` | Seguridad, backups, actualización | Definido | Media |
| `08-legal-lgpd.md` | Cumplimiento legal y LGPD | Definido | Media |
| `09-migracion-runbook.md` | Migración y runbook | Definido | Media |
| `manual-de-uso-ptbr.md` | Manual de usuario (pt-BR) | Definido | Baja |

## 7. Capacidad y dimensionamiento inicial

| Ítem | Valor estimado |
|---|---|---|
| Videos (embed) | 200+ (sin peso en disco propio; origen: Dropbox del Dr.) |
| Audios MP3 | ~10 MB promedio (linha de base) |
| Documentos | 1–10 MB promedio (PDF/DOCX/TXT) |
| Usuarios | Log in por cuenta propia (escala baja, decenas a cientos) |
| Disco consumido por archivos | Estimado 1–3 GB inicial (solo audio/documentos) |
| Ancho de banda | Bajo (el video pesa en YouTube y la descarga sale de Dropbox) |

## 8. Criterios de calidad globales (cross-module)
- **CQ-1**: Toda la interfaz pública debe cargar < 3 s en conexión de 5 Mbps.
- **CQ-2**: Todo texto visible para el usuario debe estar en pt-BR (excepto nombres propios de recursos).
- **CQ-3**: La plataforma no debe tener errores PHP visibles en producción (`debug=0`).
- **CQ-4**: Cualquier recurso (video/audio/documento) debe ser alcanzable en ≤ 2 clics desde la portada.
- **CQ-5**: Los backups deben ser restaurables en < 30 min.

## 9. Glosario técnico resumido
| Término | Definición |
|---|---|
| Módulo | Curso de Moodle que agrupa un tema completo de estudio |
| Unidad | Sección/tema dentro de un curso (agrupa recursos) |
| Recurso | Video, audio o documento individual |
| Embed | Video incrustado de YouTube vía `<iframe>` |
| Auto-registro | Alta de usuario por el propio usuario, configurable en Moodle |
| Site Admin | Rol con control total (desarrollador) |