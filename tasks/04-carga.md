# Fase 4 — Carga de Contenido

> Spec: `specs/04-fase-carga.md` · ESD: `docs/04-videos-embed.md`, `docs/05-audio-documentos.md`,
> `docs/12-runbook-publicacion.md` (flujo por video).
> Bloqueante: Fase 3 (plantilla/cursos) + licencia del curso confirmada (Fase 0).

**Progreso:** ⬜ 0% (0/7) · **Estado:** PENDIENTE

## ▶ Próxima tarea
T4.1 (plugin de importación) — verificar skeleton de `src/local/importcontenido`.

---

## T4.1 · Plugin local de importación (SDD-CAR-01)
Estado: ⬜
- [ ] Revisar skeleto `src/local/importcontenido` sobre Moodle instalado
- [ ] Probar `--dryrun` con 3 filas piloto
- Verificación: SELECT mdl_course_modules (piloto)

## T4.2 · Inventario / lista CSV (SDD-CAR-02)
Estado: ⬜
- [ ] Hoja de control con los 200 videos (ID maestro, estados)
- [ ] `lista.csv` completo (embed + dropbox + audios/docs)
- Verificación: validación CSV (dry-run)

## T4.3 · Subir videos a YouTube "não listado" (SDD-CAR-03)
Estado: ⬜
- [ ] Lotes de 10–20/sesión con VIDEO_ID en la hoja
- [ ] Títulos pt-BR y playlists por módulo
- Verificación: embed público no aparece en búsqueda

## T4.4 · Enlaces Dropbox de descarga (SDD-CAR-04)
Estado: ⬜
- [ ] Enlace privado (solo ver) por video
- Verificación: enlace pide login

## T4.5 · Audios y documentos (SDD-CAR-05)
Estado: ⬜
- [ ] MP3/PDF/DOCX/TXT importados
- Verificación: SELECT mdl_files mod_resource > 0

## T4.6 · Lista blanca de extensiones (SDD-CAR-06)
Estado: ⬜
- [ ] Solo `mp3, pdf, docx, txt`
- Verificación: subir `.exe` → rechazado

## T4.7 · Carga completa + QA contenido (SDD-CAR-07)
Estado: ⬜
- [ ] Importación íntegra sin errores
- [ ] Muestreo de 10 cursos: player + botón Baixar + docs
- [ ] TC-V-01..10 y TC-D-01..08 ejecutados
- Verificación: log de importación 0 errores