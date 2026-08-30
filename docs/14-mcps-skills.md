# MCPs y Skills recomendadas — Plataforma Educativa

> Guía de herramientas para el agente IA (opencode / Claude Code) en este proyecto.
> Fecha: 2026-08-28. Uso interno del desarrollador.

---

## 1. Resumen

Para construir esta plataforma (Moodle 4.5 + tema Boost + plugin de importación) el
agente solo necesita **2 MCP** y **5 skills de proyecto**. El resto ya lo cubren las
capacidades nativas (shell, file read/write/edit, webfetch, websearch).

| Tipo | Cantidad | Propósito |
|---|---|---|
| MCP server | 2 | Playwright (verificación frontend en navegador) + MySQL (verificación SQL de las TC) |
| Skills del proyecto | 5 | Convenciones de Moodle/Boost, publicación de videos, importador, legal pt-BR, verificación |
| Skills del sistema | 0 | No necesarias |

---

## 2. MCP servers a instalar

### 2.1 `playwright` — verificación de frontend en navegador (alta prioridad)
- **Por qué**: la customización del frontend (tema hijo, tarjetas, responsive, embed de
  video, login) **debe verificarse visualmente**. El agente abre la URL real, navega
  portada → módulo → recurso, mide responsive en 3 breakpoints (TC-F-03, TC-V-02, TC-V-06).
- **Config (opencode.json)**:
  ```json
  "mcp": {
    "playwright": {
      "type": "local",
      "command": ["npx", "-y", "@playwright/mcp@latest"],
      "enabled": true
    }
  }
  ```
- **Para Claude Code**: misma definición en `.mcp.json`.

### 2.2 `mysql` — verificación SQL de las matrices TC (alta prioridad)
- **Por qué**: casi todas las tarjetas SDD terminan con consultas SQL
  (`SELECT ... FROM mdl_course, mdl_config, mdl_course_sections`). El agente ejecuta las
  mismas consultas de verificación en vez de pedirlas.
- **Opciones** (elegir la que exista en tu ecosistema):
  - `designcomputer/mysql_mcp` (Python, `uvx mcp-server-mysql`) — muy usada.
  - Cualquier MCP MySQL/MariaDB que soporte `MYSQL_HOST, MYSQL_PORT, MYSQL_USER,
    MYSQL_PASS, MYSQL_DB` vía entorno.
- **Config (opencode.json)**:
  ```json
  "mcp": {
    "mysql": {
      "type": "local",
      "command": ["uvx", "mcp-server-mysql"],
      "enabled": true,
      "environment": {
        "MYSQL_HOST": "localhost",
        "MYSQL_PORT": "3306",
        "MYSQL_USER": "moodle_user",
        "MYSQL_DB": "moodle_db"
      }
    }
  }
  ```
  > ⚠️ **No escribir `MYSQL_PASS` en el archivo**; usar variable de entorno del sistema
  > (`{env:MYSQL_PASS}`) o el gestor de secretos. Nunca versionar la clave de BD.

### 2.3 MCP opcionales (solo si quieres)
| MCP | Uso | Recomendación |
|---|---|---|
| `git`/GitHub | Empujar el repo a GitHub | Opcional; el hosting no requiere GitHub |
| `sftp` | Desplegar archivos al hosting vía SFTP | Opcional; cPanel File Manager basta |
| `dropbox` | Gestionar enlaces de descarga | No hay MCP oficial fiable; hacerlo manual |

---

## 3. Skills de la plataforma (custom, del proyecto)

> **Ubicación**: para opencode → `.opencode/skills/<name>/SKILL.md`.
> Para Claude Code → `.claude/skills/<name>/SKILL.md`.
> Si usas ambos, duplica el archivo en las dos carpetas.

### 3.1 `moodle-verificacion`
- **Dispara cuando**: se marca el DoD de cualquier tarjeta SDD, o ante un error de Moodle.
- **Contenido**: comandos canónicos — `php -l` (lint), `php admin/cli/purge_caches.php`,
  `php admin/cli/cron.php`, consultas de estado (`mdl_config` versión), leer el informe de
  seguridad, y la regla "si `debug=0` no mostrar trazas".
- **Por qué**: unifica "cómo sabemos que está bien hecho".

### 3.2 `boost-theme-custom-global`
- **Dispara cuando**: cualquier tarea de frontend (tarjetas, hero, iframe, responsive, SCSS).
- **Contenido**: dónde vive `theme/boost` (NO tocarlo), carpeta del child
  `src/theme/plataforma`, cómo se compila SCSS (`presets.scss` + `_extra.scss` →
  `purge_caches`), overrides de layout/mustache seguros, y el test de beneficios: revisar
  en browser con Playwright en 380/768/1280 px.
- **Por qué**: previene tocar el core y estandariza el flujo de edición visual.

### 3.3 `publicar-contenido`
- **Dispara cuando**: se publica un video/audio/documento (subir a YouTube, enlazar, importar).
- **Contenido**: resume `docs/12-runbook-publicacion.md` — ID maestro `MOD01-U02-N01`,
  embargo "Não listado", link embed `youtube-nocookie.com/embed/<ID>`, enlace Dropbox
  privado, filas del CSV, estados de la hoja de control.
- **Por qué**: 200 videos → el agente nunca "adivina" el flujo.

### 3.4 `importar-contenido` (plugin CLI)
- **Dispara cuando**: se ejecuta o modifica `local/importcontenido`.
- **Contenido**: formato exacto del CSV, columnas `tipo_recurso`, uso de `--dryrun`,
  contrato del script (`create_module`, display 1=embed / 3=Dropbox / docx=descarga forzada),
  verificación SQL post-import.
- **Por qué**: el plugin es código propio; la skill fija su contrato para no romperlo.

### 3.5 `textos-ptbr-lgpd`
- **Dispara cuando**: se escribe cualquier string visible al cliente o textos legales.
- **Contenido**: reglas de traducción (pt-BR), glosario aprobado (login → "Entrar",
  login → "Minha conta", logout → "Sair", download → "Baixar"), textos de Aviso de
  Privacidad/Términos adaptados de `docs/08-legal-lgpd.md`, y el aviso de uso restringido
  del curso (copyright).
- **Por qué**: cero textos en otro idioma o legales mal redactados.

---

## 4. Lo que NO necesitas instalar

| Herramienta | Por qué no |
|---|---|
| Filesystem MCP | opencode/Claude Code ya leen/editan archivos nativamente |
| WebFetch MCP | Ya hay `webfetch`/`websearch` nativos (manuales de Moodle, docs de API) |
| LSP/solvers | El loop "editar → `purge_caches` → Playwright" es más efectivo aquí |
| MCP de YouTube | La subida es web manual (no listado); no se automatiza de forma fiable |
| MCP de Dropbox | Enlaces se comparten manualmente desde la cuenta del Dr. |

---

## 5. Checklist de instalación

- [ ] `npx @playwright/mcp` funciona (abrir la URL de la portada).
- [ ] MCP MySQL conecta a `moodle_db` con la clave por variable de entorno.
- [ ] Carpeta `.opencode/skills/` (o `.claude/skills/`) con las 5 skills.
- [ ] `CLAUDE.md` y `specs/` legibles por el agente (lectura al inicio).
- [ ] Probar la skill `moodle-verificacion` en una tarjeta simple (SDD-INF-01).
- [ ] Registrar en `docs/CHANGELOG.md` el cierre de esta fase de setup.