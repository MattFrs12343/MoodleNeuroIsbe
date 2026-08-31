# Fase 3 — Estructura de Contenido

> Spec: `specs/03-fase-estructura.md` · ESD: `docs/03-modulos-cursos.md`
> Ejecutada parcialmente 2026-08-30 sobre `portal.examenes-neuro.com`.

**Progreso:** 🟡 63% (2.5/4) · **Estado:** BLOQUEADA en T3.3 por datos de contenido del Dr.

## ▶ Próxima tarea
T3.3 (crear cursos reales) — requiere que el Dr. entregue el número de módulos y su orden
(`docs/13-info-necesaria.md` §3). El mecanismo ya está probado (T3.2).

---

## T3.1 · Crear categorías (SDD-EST-01)
Estado: ✅
- [x] Categoría raíz `Plataforma` (id 2)
- [x] Categoría hijo `Módulos` (id 3)
- Verificación: `SELECT id,name,parent FROM mdl_course_categories` → 2 filas correctas

## T3.2 · Plantilla de curso (SDD-EST-02)
Estado: ✅
- [x] Curso `Módulo 00 — Modelo` (`mod00`), formato Temas, 6 secciones, `visible=0`
- [x] Secciones nombradas: `Avisos e Introdução`, `Unidade 1..5`
- [x] Método de inscripción manual activo
- **Bugs reales encontrados y corregidos** (también en `src/local/importcontenido/cli/importar.php`,
  que usa el mismo patrón para Fase 4): `core_course_category::create_course()` no existe en
  Moodle 4.5 (es `create_course()` global de `course/lib.php`); el valor de retorno de
  `course_create_sections_if_missing()` no sirve para actualizar `name` directamente, hay
  que releer el registro de `mdl_course_sections` después. Ver detalle en la spec.
- Verificación: `SELECT s.section,s.name FROM mdl_course_sections ...` → 6 filas correctas

## T3.3 · Crear cursos Módulo NN (SDD-EST-03)
Estado: ⏸️ BLOQUEADA
- [ ] Un curso por módulo con shortname `modNN` — **falta el número/orden real de módulos
      del Dr.** (`docs/13-info-necesaria.md` §3)
- [ ] Summary en pt-BR + imagen de curso
- [ ] Inscribir al Dr. (único usuario) en cada curso real vía script (RF-M-06 actualizado
      en `docs/03-modulos-cursos.md`)

## T3.4 · Convención de nombres aceptada (SDD-EST-04)
Estado: 🟡 fijada, uso real pendiente
- [x] Convención Módulo/Unidad/Recurso ya aplicada en la plantilla `mod00`
- [ ] Verificación contra el CSV real de Fase 4 — pendiente (bloqueada junto con T3.3/Fase 4)
