# CLAUDE.md — Manual de Implementación para el Agente IA

> Este archivo guía al agente de IA (Claude Code) que **construye** la plataforma.
> Debe leerse **antes de empezar** y respetarse siempre.

---

## 1. Qué es este proyecto
Plataforma educativa sobre **Moodle 4.5.x** en hosting compartido (PHP 8.2 + MariaDB),
en **pt-BR**, para un Dr. de Brasil. Los videos se **embeben** desde YouTube (no listado)
y se **descargan** por Dropbox. El proyecto está **contratado a Bs 6,000** (3 fases de pago).

## 2. Fuentes de verdad (leer en este orden)
1. `docs/00-maestro.md` — visión y decisiones (ADR).
2. `specs/00-variables-fijas.md` — **parámetros inmutables** del build (completar una vez).
3. `specs/*.md` — tarjetas de trabajo SDD por fase.
4. `docs/01..09-*.md` — ESD de referencia (detalle y test harness).

## 3. REGLAS OBLIGATORIAS

### 3.1 Prohibido tocar el core de Moodle
- NO editar/parchear: `theme/boost/*`, `lib/base`, `admin/*`, `course/format/topics`,
  `mod/resource`, `mod/url`, archivos oficiales `*.php` del core.
- Personalizar SOLO mediante:
  - **Tema propio**: `theme/plataforma/` (child de boost: `$THEME->parents = ['boost']`).
  - **Plugin local propio**: `local/importcontenido/`.
  - **Configuración** vía CLI (`admin/cli/cfg.php`, `install.php`) o UI.

### 3.2 Flujo de trabajo por tarjeta
1. Leer la tarjeta SDD actual (una a la vez).
2. Ejecutar los pasos con los **parámetros exactos** de `00-variables-fijas.md`.
3. Correr la **verificación** indicada (comando/consulta SQL/chequeo UI).
4. Marcar el **DoD** de la tarjeta y seguir.
5. Si algo no cuadra con una tarjeta: **no improvisar** → reportar y volver a la spec.

### 3.3 Comunicación
- Responder al desarrollador (usuario) en **español**.
- Todo string visible al **cliente (Dr./usuarios)** debe ir en **pt-BR**.
- Los **errores y trazas** de Moodle NO son visibles en producción (`debug=0`).

### 3.4 Verificación obligatoria
- Toda tarjeta termina con su matriz `TC` ejecutada (☑ en DoD).
- No declarar “hecho” sin la verificación correspondiente.

### 3.5 Git
- No hacer `git commit`/`push` sin pedir permiso explícito.
- Registrar en `docs/CHANGELOG.md` cada fase terminada.

### 3.6 Prevención de regresiones (obligatorio — leer `docs/15-prevencion-regresiones.md`)
- CAMBIO ATÓMICO: tocar solo lo pedido; nunca "arreglar de paso" lo que no se pidió.
- Al terminar un cambio, ejecutar SIEMPRE los TC de la zona tocada Y de los módulos vecinos;
  reportar el resultado por escrito.
- Si algo que antes funcionaba se rompe: PARAR, revertir primero, analizar causa; no parchear
  encima.
- Cambios a producción SOLO después de verificación en staging (nunca probar correcciones en vivo).
- Frontend (tema Boost): tras cada cambio visual, comparar captura de referencia (Playwright,
  380/768/1280 px) antes/después.

## 4. Stack fijado
| Capa | Elección |
|---|---|
| LMS | Moodle **4.5.x** (stable 405, última revision) |
| Backend | PHP **8.2** + CLI |
| BD | **MariaDB** (Db `moodle_db`, usuario `moodle_user`) |
| Idioma | `pt_br` único activo |
| Tema | `theme_plataforma` (padre `boost`) |
| Estructura | Categoría `Módulos` → Curso `Módulo NN` → Sección `Unidade N` → Recurso |
| Servicios | YouTube (embed `youtube-nocookie.com`) · Dropbox (descarga) |
| Automatización | Script `local/importcontenido` (CSV → estructura) |

## 5. Recordatorios operativos
- Cron cada **1 min** (`admin/cli/cron.php`).
- Backup: BD diaria + dataroot semanal (spec `07-seguridad-backups.md`).
- LGPD: aviso de privacidad + banner cookies + acceso restringido (spec `08-legal-lgpd.md`).