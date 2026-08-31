# SDD-03 — Fase 3: Estructura de Contenido

> Prereq: Fases 1 y 2. ESD ref: `docs/03-modulos-cursos.md`.

---

### SDD-EST-01 — Crear categorías ✅ (2026-08-30)
- **Pasos ejecutados**: en vez de UI, script temporal con `core_course_category::create()`
  (API oficial de Moodle), borrado justo después de usarlo.
- **Verificación**
  - `SELECT id, name, parent FROM mdl_course_categories WHERE name IN ('Plataforma','Módulos');`
    → `Plataforma` (id 2, parent 0), `Módulos` (id 3, parent 2).
- **DoD**
  - [x] Categorías creadas.

---

### SDD-EST-02 — Plantilla de curso (formato Temas) ✅ (2026-08-30)
- **Entradas**: `FORMAT_CURSO=topics`, `NUM_SECCIONES_BASE=6`, `SEC_0`, `SEC_UNIDAD`.
- **Pasos ejecutados** (script temporal con la API de Moodle, `create_course()` de
  `course/lib.php` + `course_create_sections_if_missing()`):
  1. Curso `Módulo 00 — Modelo` (`mod00`) creado en categoría `Módulos`, `visible=0`
     (es plantilla, no debe aparecer en la portada).
  2. Formato `topics`, 6 secciones (0 a 5).
  3. Secciones renombradas: `Avisos e Introdução`, `Unidade 1`..`Unidade 5`.
  4. Método de inscripción `manual` confirmado activo (`ENROL_INSTANCE_ENABLED`).
  ⚠️ **Bug real encontrado y corregido, también en `src/local/importcontenido/cli/importar.php`**
  (que usa el mismo patrón para Fase 4): `core_course_category::create_course()` **no existe**
  en Moodle 4.5 — la función correcta es la global `create_course($data)` de `course/lib.php`.
  Además, el valor de retorno de `course_create_sections_if_missing()` en esta versión
  (basado en la nueva API `formatactions`) **no sirve para actualizar el nombre
  directamente**: hay que re-leer la sección con `$DB->get_record('course_sections', ...)`
  después de asegurarla, y recién ahí actualizar `name`. Ambos fixes aplicados en el
  plugin del repo (`get_or_create_course()` y `ensure_section()`).
- **Verificación**
  - `SELECT course.id, s.section, s.name FROM mdl_course course JOIN mdl_course_sections s ON s.course=course.id WHERE course.shortname='mod00';`
    → 6 filas con los nombres correctos.
- **DoD**
  - [x] Plantilla con 6 secciones nombradas.
  - [x] Enrolamiento por defecto OK (método manual activo).

---

### SDD-EST-03 — Crear cursos Módulo NN (a demanda) ⏸️ BLOQUEADA
> Bloqueada por datos reales del Dr.: número de módulos, orden y temas
> (`docs/13-info-necesaria.md` §3, sin responder). No se improvisan nombres de módulos
> inventados. Se retoma en cuanto llegue esa info; el mecanismo (plantilla + `create_course()`)
> ya está probado y funcionando (ver SDD-EST-02).
- **Entradas**: `NOMBRE_CURSO`, `SHORTNAME_CURSO`.
- **Pasos**
  1. Para cada módulo real: duplicar la plantilla o crear con: fullname `Módulo NN — <Tema>`, shortname `modNN`, categoría `Módulos`, formato `topics`.
  2. En `summary` del curso: texto de presentación en pt-BR (aparece en la tarjeta de la portada).
  3. Guardar imagen de curso (opcional) en `course_overviewfiles`.
- **Verificación**
  - `SELECT id, fullname FROM mdl_course WHERE format='topics';` → tantos como módulos.
  - La portada (Fase 5) los lista.
- **DoD**
  - [ ] Uno por módulo, con summary e imagen.

---

### SDD-EST-04 — Convención de nombres (adopción por fase 4) 🟡 fijada, uso real pendiente
- **Regla** (documentada para el script de importación):
  - Curso: `modNN`
  - Sección 0: `Avisos e Introdução` · Secciones: `Unidade N — <Subtema>`
  - Recurso video: `Vídeo da aula N [+ texto]` · audio: `Áudio da aula N`
  - Documento: `<Tipo> — <descripción>` (ej. `PDF — Apuntes Unidade 1`)
- **Verificación**
  - Ya aplicada en la plantilla `mod00` (SDD-EST-02: secciones `Unidade 1`..`5`).
  - Falta verificar contra el CSV real de la Fase 4 (bloqueada, ver SDD-EST-03/SDD-CAR-02).
- **DoD**
  - [x] Convención fijada.
  - [ ] Usada en carga real — pendiente de Fase 4.