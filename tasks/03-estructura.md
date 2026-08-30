# Fase 3 — Estructura de Contenido

> Spec: `specs/03-fase-estructura.md` · ESD: `docs/03-modulos-cursos.md`
> Bloqueante: Fase 2 completa.

**Progreso:** ⬜ 0% (0/4) · **Estado:** PENDIENTE

## ▶ Próxima tarea
T3.1 (crear categorías) — requiere Fase 2.

---

## T3.1 · Crear categorías (SDD-EST-01)
Estado: ⬜
- [ ] Categoría raíz `Plataforma`
- [ ] Categoría hijo `Módulos`
- Verificación: SELECT mdl_course_categories → 2 filas

## T3.2 · Plantilla de curso (SDD-EST-02)
Estado: ⬜
- [ ] Curso `Módulo 00 — Modelo` (shortname `mod00`), formato Temas, 6 secciones
- [ ] Secciones nombradas: `Avisos e Introdução`, `Unidade 1..5`
- [ ] Enrolamiento manual con rol `user`
- Verificación: SELECT mdl_course_sections

## T3.3 · Crear cursos Módulo NN (SDD-EST-03)
Estado: ⬜
- [ ] Un curso por módulo con shortname `modNN`
- [ ] Summary en pt-BR + imagen de curso
- Verificación: SELECT mdl_course format='topics'

## T3.4 · Convención de nombres aceptada (SDD-EST-04)
Estado: ⬜
- [ ] Convención Módulo/Unidad/Recurso activa para carga
- Verificación: muestra del CSV de Fase 4