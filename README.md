# Índice de Documentación — Plataforma Educativa

> Documentación modular estilo **Harness Engineer** (ESD — Engineering Specification
> Document). Cada spec es autónoma y tiene criterios de aceptación verificables.

## Código fuente de referencia (copiar al Moodle al desplegar)

| Ruta | Contenido |
|---|---|
| [src/local/importcontenido](src/local/importcontenido) | Plugin de importación: `cli/importar.php`, `lib.php`, `version.php`, lang pt-BR |
| [src/theme/plataforma](src/theme/plataforma) | Tema hijo de Boost: `config.php`, SCSS, lang, logo |
| [specs/plantillas/lista_ejemplo.csv](specs/plantillas/lista_ejemplo.csv) | Plantilla CSV con las 6 filas tipo (video/audio/doc) |

## Tasks — seguimiento de trabajo

Sistema de tareas/subtareas por fase para saber dónde continuar:

| Archivo | Contenido |
|---|---|
| [tasks/README.md](tasks/README.md) | Cómo usar el sistema + estados |
| [tasks/00-estado-general.md](tasks/00-estado-general.md) | Tablero maestro + **punto de continuidad** |
| [tasks/01-infra.md](tasks/01-infra.md) · [02-config.md](tasks/02-config.md) | Fases 1–2 |
| [tasks/03-estructura.md](tasks/03-estructura.md) · [04-carga.md](tasks/04-carga.md) | Fases 3–4 |
| [tasks/05-tema.md](tasks/05-tema.md) · [06-qa.md](tasks/06-qa.md) | Fases 5–6 |

## Spec-Driven Development (ejecución por IA)

| Fase | Spec | Contenido |
|---|---|---|
| 0 | [Variables fijas](specs/00-variables-fijas.md) | Parámetros inmutables del build |
| 1 | [Infraestructura](specs/01-fase-infra.md) | Despliegue Moodle + servidor (`SDD-INF-01..07`) |
| 2 | [Configuración base](specs/02-fase-config.md) | Idiomas, auth, roles, seguridad, legal (`SDD-CFG`) |
| 3 | [Estructura](specs/03-fase-estructura.md) | Categorías/cursos/secciones (`SDD-EST`) |
| 4 | [Carga de contenido](specs/04-fase-carga.md) | Script + 200 videos + audios/docs (`SDD-CAR`) |
| 5 | [Tema y portada](specs/05-fase-tema.md) | Child theme boost + tarjetas (`SDD-TEM`) |
| 6 | [QA y producción](specs/06-fase-qa.md) | Tests, seguridad, backups, cierre (`SDD-QA`) |

Reglas de implementación en [`CLAUDE.md`](CLAUDE.md). Avance en [docs/CHANGELOG.md](docs/CHANGELOG.md).

## Documentos

| # | Documento | Descripción |
|---|---|---|
| 00 | [Estado/Maestro](docs/00-maestro.md) | Visión general, objetivos, ADR y arquitectura |
| 01 | [Infraestructura](docs/01-infraestructura.md) | Hosting compartido, PHP, MySQL, instalación Moodle |
| 02 | [Autenticación y usuarios](docs/02-autenticacion.md) | Login, auto-registro, roles, idioma pt-BR |
| 03 | [Módulos y cursos](docs/03-modulos-cursos.md) | Categoría → Módulo(Curso) → Unidad(Sección) → Recurso |
| 04 | [Videos embed](docs/04-videos-embed.md) | Reproductor YouTube no listado + descarga por Dropbox (>200 videos) |
| 05 | [Audio y documentos](docs/05-audio-documentos.md) | MP3, PDF, DOCX, TXT por módulo |
| 06 | [Portada y tema hijo](docs/06-portada-tema.md) | Portada con tarjetas por módulo, boost child theme |
| 07 | [Seguridad y backups](docs/07-seguridad-backups.md) | Ops: seguridad, RTO, actualizaciones |
| 08 | [Legal y LGPD](docs/08-legal-lgpd.md) | Privacidad, cookies, copyright del curso comprado |
| 09 | [Migración y runbook](docs/09-migracion-runbook.md) | Cambio de hosting/dominio + operación diaria |
| 10 | [Manual de usuario (pt-BR)](docs/manual-de-uso-ptbr.md) | Guía final para el Dr./usuarios |
| 11 | [Presupuesto](docs/10-presupuesto.md) | Cotización: horas, tarifa Bs, conversión BRL, pagos |
| 12 | [Justificación del presupuesto](docs/11-justificacion-presupuesto.md) | Desglose partida por partida hasta Bs 6,000 |
| 13 | [Runbook de publicación](docs/12-runbook-publicacion.md) | Cómo publicar video/audio/doc (paso a paso) |
| 14 | [Información pendiente](docs/13-info-necesaria.md) | Datos a reunir antes de programar |
| 15 | [MCPs y Skills](docs/14-mcps-skills.md) | Playwright + MySQL MCP y 5 skills del proyecto |
| 16 | [Prevención de regresiones](docs/15-prevencion-regresiones.md) | Reglas para corregir sin romper lo que funcionaba |

## Cómo leer la documentación
1. Empieza con el **maestro** (`00`) para la visión y decisiones.
2. Despliega con `01` (infraestructura) y luego `02`→`06` (funcionalidad).
3. Termina con `07` para el régimen operativo.
4. Cada spec se verifica con su **test harness** (matriz `TC-`).

## Estado de implementación
_Pendiente de marcado durante el desarrollo (usa las casillas ☐ de cada spec)._

## Próximos pasos sugeridos (roadmap)
- [ ] P1: Infraestructura desplegada (Spec-01).
- [ ] P2: Autenticación + idioma (Spec-02).
- [ ] P3: Primer módulo con una unidad (Spec-03 y 04/05).
- [ ] P4: Portada con tarjetas (Spec-06).
- [ ] P5: Backups y seguridad (Spec-07).