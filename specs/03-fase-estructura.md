# SDD-03 — Fase 3: Estructura de Contenido

> Prereq: Fases 1 y 2. ESD ref: `docs/03-modulos-cursos.md`.

---

### SDD-EST-01 — Crear categorías
- **Pasos** (UI: *Site admin → Courses → Add a category*)
  1. Crear categoría raíz `Plataforma`.
  2. Crear hijo `Módulos`.
- **Verificación**
  - `SELECT id, name FROM mdl_course_categories WHERE name IN ('Plataforma','Módulos');` → 2 filas.
- **DoD**
  - [ ] Categorías creadas.

---

### SDD-EST-02 — Plantilla de curso (formato Temas)
- **Entradas**: `FORMAT_CURSO`, `NUM_SECCIONES_BASE`, `SEC_0`, `SEC_UNIDAD`.
- **Pasos** (UI o CLI)
  1. Crear curso plantilla `Módulo 00 — Modelo` (shortname `mod00`) en categoría `Módulos`.
  2. Formato = **Temas**; nº secciones = `NUM_SECCIONES_BASE`.
  3. Renombrar cada sección:
     - Sección 0 → `Avisos e Introdução`
     - Secciones 1..5 → `Unidade 1`, `Unidade 2`, …
  4. Configurar enrolamiento: método `manual` con rol `Authenticated user` (permite auto-matrícula al ver).
- **Verificación**
  - `SELECT course.id, s.section, s.name FROM mdl_course course JOIN mdl_course_sections s ON s.course=course.id WHERE course.shortname='mod00';`
- **DoD**
  - [ ] Plantilla con 6 secciones nombradas.
  - [ ] Enrolamiento por defecto OK.

---

### SDD-EST-03 — Crear cursos Módulo NN (a demanda)
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

### SDD-EST-04 — Convención de nombres (adopción por fase 4)
- **Regla** (documentada para el script de importación):
  - Curso: `modNN`
  - Sección 0: `Avisos e Introdução` · Secciones: `Unidade N — <Subtema>`
  - Recurso video: `Vídeo da aula N [+ texto]` · audio: `Áudio da aula N`
  - Documento: `<Tipo> — <descripción>` (ej. `PDF — Apuntes Unidade 1`)
- **Verificación**
  - Muestras del CSV de la Fase 4 cumplen la convención.
- **DoD**
  - [ ] Convención fijada y usada en carga.