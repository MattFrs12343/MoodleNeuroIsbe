# Tasks — Sistema de Seguimiento

> Archivos de tareas (tareas + subtareas) para **saber por dónde continuar** en cada sesión.
> ESD de referencia: `docs/` · Specs de implementación: `specs/` · Progreso: `docs/CHANGELOG.md`.

## Cómo leer
1. Abrir **`00-estado-general.md`** → ver la línea **"PUNTO DE CONTINUIDAD"**.
2. Ir al archivo de la fase indicada → encontrar la tarea **⏳ EN CURSO**.
3. Ejecutar la tarea y marcar subtareas `[x]`.
4. Mover la tarea a ✅ y actualizar el contador de la fase.

## Estados
| Ícono | Estado | Significado |
|---|---|---|
| ⬜ | PENDIENTE | No iniciada |
| ⏳ | EN CURSO | En ejecución (aquí sigues) |
| ✅ | HECHO | Completada y verificada (TC ✔) |
| 🚫 | BLOQUEADO | Necesita dato/decisión del cliente antes de avanzar |

## Estructura de archivos

| Archivo | Fase |
|---|---|
| [00-estado-general.md](00-estado-general.md) | Tablero maestro + punto de continuidad |
| [01-infra.md](01-infra.md) | Fase 1 — Infraestructura |
| [02-config.md](02-config.md) | Fase 2 — Configuración base |
| [03-estructura.md](03-estructura.md) | Fase 3 — Estructura de contenido |
| [04-carga.md](04-carga.md) | Fase 4 — Carga de contenido |
| [05-tema.md](05-tema.md) | Fase 5 — Tema y portada |
| [06-qa.md](06-qa.md) | Fase 6 — QA, seguridad, producción |

## Reglas de actualización
- **Una tarea a la vez**; nunca dos "EN CURSO" en la misma fase.
- Marcar una tarea ✅ **solo** tras pasar su verificación especificada.
- Al terminar una fase, registrar en `docs/CHANGELOG.md` la línea correspondiente.
- Si una tarea depende de un dato del cliente, muévela a 🚫 y anota QUÉ falta y para QUIÉN.