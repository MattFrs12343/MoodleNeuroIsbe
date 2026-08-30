# ESD-03 — Estructura de Módulos y Cursos

> **Especificación de Ingeniería · Harness Engineer**
> Versión 1.0 · 2026-08-28 · Prioridad: **Alta**
> Relacionada con: `00-maestro.md` (ADR-2)

---

## 1. Propósito
Definir la taxonomía de contenido de la plataforma: **Categoria → Módulo(Curso) → Unidad(Sección) → Recurso**,
de modo que el usuario entienda exactamente dónde está cada material.

## 2. Alcance
- Jerarquía de categorías y cursos.
- Formato de curso y disposición de secciones.
- Política de visibilidad y matriculación de usuarios.
- Nomenclatura de módulos/unidades (convención de nombres pt-BR).

## 3. Jerarquía de contenido

```
Categoria raiz  📁 "Plataforma" (o nombre del proyecto)
   └── Categoria "Módulos"
        ├── Curso  : "Módulo 1 — <Tema>"          (escuela/MOODLE course)
        │    ├── Sección 0 : Avisos / Introdução
        │    ├── Sección 1 : Unidade 1 — <Subtema>
        │    ├── Sección 2 : Unidade 2 — <Subtema>
        │    └── Sección N : Unidade N — <Subtema>
        ├── Curso  : "Módulo 2 — <Tema>"
        └── ...
```

### 3.1 Reglas de negocio
- RN-M-01: Cada **módulo** es un **curso** de Moodle (course).
- RN-M-02: Cada **unidad** es una **sección** del curso (formato "Temas" / topics).
- RN-M-03: La sección 0 se reserva para avisos e introducción general del módulo.
- RN-M-04: Los recursos se colocan SOLO dentro de su sección/unidad correspondiente.
- RN-M-05: El orden de las secciones define la secuencia de estudio (lineal).
- RN-M-06: Nombres únicos y numerados: `Módulo 01`, `Módulo 02`, ... y `Unidade 1`, `Unidade 2`, ...

## 4. Requisitos funcionales

- RF-M-01: Crear la categoría raíz y la categoría "Módulos" vía Site admin → Cursos → Categorías.
- RF-M-02: Crear cursos con formato **Temas** (topics) y con número definido de secciones.
- RF-M-03: Como default, el curso está **visible** para usuarios autenticados.
- RF-M-04: Activar el **modo de edición** solo para el rol admin.
- RF-M-05: Configurar el **summary/descripción** de cada módulo (aparece en la tarjeta de la portada — Spec-06).
- RF-M-06: Los usuarios estándar no se matriculan manualmente; se usa auto-enrol con el rol por defecto
  (ver Spec-02, RF-A-07).

## 5. Configuración en Moodle (checklist)

| # | Ajuste | Ruta | Valor |
|---|---|---|---|
| 1 | Crear categorías | Administración del sitio → Cursos → Gestión de cursos y categorías | `Módulos` |
| 2 | Formato de curso | Al crear curso → Formato | `Temas` |
| 3 | Nº secciones | Al crear curso → Nº de secciones | Ej. 5+ |
| 4 | Visibilidad | Curso → Configuración → Visible | Sí |
| 5 | Enrolamiento | Curso → Participantes → Métodos de matriculación | `Enrolamiento manual` + rol `Authenticated user` |
| 6 | Acceso tras login | /course/index.php (o portada custom) | Solo login, no guest |

## 6. Modelo de datos (solo lectura de esquema nativo)
```
mdl_course            : fullname, shortname, category, format='topics'
mdl_course_sections   : course, section, name, summary, visible
mdl_course_modules    : course, section, module, instance
mdl_context           : permisos por contexto de curso
```
Nota: no se extiende el esquema en v1 (se aprovechan campos nativos: `summary`,
`course_sections.name`, `course_sections.summary`).

## 7. Criterios de aceptación (Test Harness)

| ID | Prueba | Pasos minimales | Resultado esperado | Estado |
|---|---|---|---|---|
| TC-M-01 | Crear módulo | Crear curso Módulo 01 con 5 secciones | Aparece en categoría Módulos | ☐ |
| TC-M-02 | Numeración | Nombrar `Módulo 01` y `Unidade 1..N` | Filtrar por nombre da orden correcto | ☐ |
| TC-M-03 | Validación de recursos | Crear recurso en sección 5 | Aparece solo en sección 5 | ☐ |
| TC-M-04 | Visibilidad para login | Deslogueado + entrar curso | Redirige a login (no guest) | ☐ |
| TC-M-05 | Visibilidad logado | Con cuenta user, abrir curso | Ve secciones y recursos; sin editar | ☐ |
| TC-M-06 | Summary en tarjeta | Portada (Spec-06) | El summary del curso se muestra en la tarjeta | ☐ |

## 8. Riesgos y mitigaciones
| ID | Descripción | Prob. | Impacto | Mitigación |
|---|---|---|---|---|
| R-M-01 | Cursos desordenados (mal categorizados) | Media | Medio | Usar convención `Módulo NN`; backup y plantilla de curso |
| R-M-02 | Usuario ve menú "Mis cursos" vacío | Media | Baja | Configurar página inicial para mostrar categorías/módulos |
| R-M-03 | Formatos inconsistentes entre módulos | Media | Baja | Plantilla de curso de referencia clonable |

## 9. Definition of Done (DoD)
- [ ] Categorías creadas (`Módulos`) con al menos 1 curso plantilla.
- [ ] Formato Temas y secciones numeradas verificados.
- [ ] Enrolamiento por defecto funcionando para cualquier usuario autenticado.
- [ ] Summary/descripción de cada curso completado para la portada.
- [ ] Curso plantilla clonable documentado.