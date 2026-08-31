# Fase 2 — Configuración Base

> Spec: `specs/02-fase-config.md` · ESD: `docs/02-autenticacion.md`, `docs/08-legal-lgpd.md`
> Ejecutada 2026-08-30 sobre `portal.examenes-neuro.com`. Detalle completo (comandos reales,
> hardening extra encontrado) en `specs/02-fase-config.md`.

**Progreso:** 🟡 83% (5/6) · **Estado:** CASI HECHO — solo falta T2.5 (textos legales)

## ▶ Próxima tarea
T2.5 (textos legales LGPD) — requiere: (a) dato de contacto real para el aviso de
privacidad (`docs/13-info-necesaria.md` §4; se puede usar `jmcabello_merida@hotmail.com`
si el Dr. confirma que ese es el contacto correcto para privacidad), (b) revisión del
texto con el desarrollador antes de publicarlo. No bloquea el arranque de Fase 3.

---

## T2.1 · Forzar idioma pt_BR único (SDD-CFG-01)
Estado: ✅
- [x] `cfg.php --name=X --set=Y` (la spec traía `--value=`, incorrecto en Moodle 4.5)
- [x] `lang=pt_br`, `langlist=pt_br`, `langmenu=0`
- Verificación: confirmado en `mdl_config` y en `<title>` de portada/login

## T2.2 · Autenticación sin auto-registro (SDD-CFG-02)
Estado: ✅
- [x] `auth=manual` (se quitó `email`, activo por defecto tras el instalador)
- [x] Guest deshabilitado (`guestloginbutton=0`, `enrol_plugins_enabled=manual`)
- [x] `registerauth` vacío, `/login/signup.php` → 404
- [x] Cuenta única del Dr. creada: `dr_jcabello` (Dr. Juan Marcelo Cabello Mérida,
      jmcabello_merida@hotmail.com), clave temporal + cambio forzado en primer login
- Verificación: `mdl_user` tiene `guest`, `admin`, `dr_jcabello`; login probado por curl

## T2.3 · Roles mínimos (SDD-CFG-03)
Estado: ✅ (sin cambios necesarios, ya venía correcto)
- [x] Rol por defecto = `user` (Authenticated user)
- [x] `siteadmins=2` (solo admin), sin asignaciones de teacher/manager

## T2.4 · Seguridad base (SDD-CFG-04)
Estado: ✅
- [x] `protectusernames=1`, `passwordpolicy=1`
- [x] `preventexecpath=true` en config.php
- [x] **Hallazgo extra no previsto**: 12 tipos de archivo interno de Moodle (composer.json,
      *.dist, install.xml, READMEs, tests/, behat/, fixtures/) eran públicamente
      descargables. Corregido con reglas `.htaccess` — ver detalle en la spec.
- [~] Informe de seguridad UI sigue mostrando 1 "Erro" pese a que cada ruta ya verifica
      403 por `curl` (posible artefacto de caché/render, no una vulnerabilidad real —
      no se investigó más para evitar un rabbit hole)

## T2.5 · Textos legales LGPD (SDD-CFG-05)
Estado: ⏸️ PENDIENTE (no iniciada, decisión consciente de no improvisar contenido legal)
- [ ] Páginas Aviso de Privacidad y Términos en pt-BR
- [ ] Cargar como Site Policy (`admin/tool/policy`) — reemplaza el mecanismo de "checkbox
      en registro" original, ya que no hay auto-registro
- [ ] Banner de cookies

## T2.6 · Verificación transversal de idioma (SDD-CFG-06)
Estado: ✅
- [x] Portada, login e informe de seguridad (admin) 100% pt-BR, sin strings visibles en inglés
