# SDD — Spec-Driven Development (Tarjetas de Trabajo)

> Conjunto de **tarjetas de trabajo** que convierten las ESD de `docs/` en unidades
> ejecutables por un agente IA (Claude/open@) con pasos exactos y verificación.
>
> **Orden de ejecución**: las fases son secuenciales. No saltar fases.

| Fase | Archivo | Contenido |
|---|---|---|
| 0 | `00-variables-fijas.md` | Parámetros inmutables del build (leer y completar una vez) |
| 1 | `01-fase-infra.md` | Despliegue Moodle + servidor |
| 2 | `02-fase-config.md` | Configuración base: idioma, auth, roles, seguridad, legal |
| 3 | `03-fase-estructura.md` | Estructura categoría/cursos/secciones |
| 4 | `04-fase-carga.md` | Script de importación + carga de 200 videos/audios/docs |
| 5 | `05-fase-tema.md` | Tema hijo + portada con tarjetas |
| 6 | `06-fase-qa.md` | QA, seguridad, backups, puesta en producción |

## Formato de cada tarjeta
```
### SDD-<FASE>-<NN> — <Título>
- Prereq      : tarjetas o condiciones que deben estar listas antes.
- Entradas    : parámetros exactos (de 00-variables-fijas.md).
- Pasos       : comandos/CUI a ejecutar en orden.
- Salidas     : archivos/config creados.
- Verificación: comando/SQL/UI + resultado esperado.
- DoD         : casillas ☐ a marcar al terminar.
```

## Reglas para el agente
1. Ejecutar **una tarjeta a la vez**, en orden numérico.
2. Usar **siempre** los valores de `00-variables-fijas.md` (no inventar).
3. No marcar DoD sin haber pasado la verificación.
4. Si la verificación falla, detenerse: no improvisar. Reportar.
5. Registrar el avance en `docs/CHANGELOG.md` al cerrar cada fase.