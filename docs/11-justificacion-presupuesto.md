# Justificación del Presupuesto — Bs 6,000

> Complemento a `10-presupuesto.md`.
> Cada entregable lista **qué se hace, por qué** y **cuánto cuesta**. La suma de las 9 partidas
> llega a **Bs 6,000**.

---

## Desglose (total = Bs 6,000)

| # | Entregable | Qué se hace | Por qué se justifica (beneficio para el Dr.) | Costo |
|---|---|---|---|---|
| 1 | **Levantamiento y plan de carga** | Inventario de los 200+ videos, audios y documentos del Dropbox; mapa `Módulo → Unidad → Recurso`; nomenclatura y estructura de carpetas. | Sin un plan, cargar 200 videos es caos. Garantiza que **todo quede en el lugar correcto** y con nombre consistente desde el día 1. | **Bs 300** |
| 2 | **Instalación y configuración del servidor** | Instalación de Moodle en el hosting, creación de base de datos MySQL, certificado SSL, cron, permisos, seguridad base. | Es la **columna vertebral** de la plataforma: si esto falla, nada funciona. Se hace una sola vez y bien (compatible con hosting compartido). | **Bs 900** |
| 3 | **Configuración de usuarios y privacidad** | Login, auto-registro, roles (admin = tú, usuarios solo ven), invitado deshabilitado, idioma **pt-BR**, avisos LGPD y banner de cookies. | **Bloquea el acceso a los videos del curso comprado** (solo usuarios autorizados) y cumple la ley brasileña. Sin esto el contenido estaría expuesto. | **Bs 800** |
| 4 | **Estructura de módulos y unidades** | Creación de categorías, cursos `Módulo 01`, `Módulo 02`..., plantilla de curso con secciones/unidades. | Es la promesa principal de la plataforma: **"ordenar dónde está cada cosa"**. El usuario entra y ve los módulos claros y numerados. | **Bs 700** |
| 5 | **Carga de los 200 videos** | Subir cada video a YouTube (no listado), crear el recurso embebido en su unidad y enlazar el **botón de descarga por Dropbox**. | Es el **contenido principal del proyecto** (200 videos). Es la partida más grande porque son 200 unidades individuales; sin este paso la plataforma no sirve para su propósito. | **Bs 1,400** |
| 6 | **Carga de audios y documentos** | Subir MP3 (reproducción), PDF (previsualización), DOCX/TXT (descarga), con lista blanca de extensiones y límites. | Los materiales complementarios por módulo quedan disponibles y **bien protegidos** (solo tipos permitidos). | **Bs 500** |
| 7 | **Tema e identidad visual (portada con tarjetas)** | Tema hijo de Boost, portada con **tarjetas por módulo** (imagen, título, resumen, botón "Acessar"), logo, responsive. | La "cara" de la plataforma. Permite llegar al contenido **en ≤ 2 clics** desde la portada y se ve profesional (imagen del Dr.). | **Bs 1,000** |
| 8 | **Manual de usuario y documentación** | Manual de uso en **pt-BR**, documentación técnica y runbook en la carpeta `docs/`. | El Dr. y sus usuarios **se ayudan solos** sin depender de ti; tu proyecto queda documentado y escalable. | **Bs 200** |
| 9 | **Pruebas (QA), seguridad y puesta en producción** | Ejecutar la matriz de pruebas de cada spec, revisar seguridad, pulir, publicar y validar en producción. | **Entrega sin errores**: menos reclamos y menos soporte pos-venta para ti. Cierra el proyecto "con llave". | **Bs 200** |

---

## Suma verificada

| Partidas | Costo |
|---|---|
| 1 Levantamiento | Bs 300 |
| 2 Infraestructura | Bs 900 |
| 3 Usuarios + privacidad | Bs 800 |
| 4 Estructura de módulos | Bs 700 |
| 5 Carga de 200 videos | Bs 1,400 |
| 6 Audios y documentos | Bs 500 |
| 7 Tema y portada | Bs 1,000 |
| 8 Manual + documentación | Bs 200 |
| 9 QA y puesta en producción | Bs 200 |
| **TOTAL** | **Bs 6,000** |

## Notas para presentarlo al Dr.
- El **40%** del presupuesto (partidas 1, 4, 5, 6 ≈ Bs 2,900) va a **estructura y contenido**,
  que es exactamente lo que el cliente dijo necesitar: "ordenar dónde queda cada cosa".
- El **tema y la portada** (partida 7, Bs 1,000) es lo "vistoso" que percibe el usuario;
  el resto es invisible pero indispensable.
- Si el Dr. quiere bajar precio, las partidas 5 y 6 (carga de contenido) son las únicas
  reducibles, entregando los videos listos en YouTube por tu parte o en más días.