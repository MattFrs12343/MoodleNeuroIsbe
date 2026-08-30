# Fase 2 — Configuración Base

> Spec: `specs/02-fase-config.md` · ESD: `docs/02-autenticacion.md`, `docs/08-legal-lgpd.md`
> Bloqueante: Fase 1 completa (Moodle instalado y logueado como admin).

**Progreso:** ⬜ 0% (0/6) · **Estado:** PENDIENTE

## ▶ Próxima tarea
T2.1 (forzar pt_BR) — requiere Fase 1.

---

## T2.1 · Forzar idioma pt_BR único (SDD-CFG-01)
Estado: ⬜
- [ ] `cfg.php lang=pt_br`, `langlist=pt_br`, selector oculto
- Verificación: SELECT mdl_config; portada en portugués

## T2.2 · Autenticación + auto-registro (SDD-CFG-02)
Estado: ⬜
- [ ] Manual + email registration con captcha
- [ ] Guest deshabilitado
- Verificación: `/login/index.php` muestra "Criar conta"

## T2.3 · Roles mínimos (SDD-CFG-03)
Estado: ⬜
- [ ] Solo roles `admin` y `user` en uso
- [ ] Usuario estándar sin botón "Editar"
- Verificación: rol por defecto = Authenticated user

## T2.4 · Seguridad base (SDD-CFG-04)
Estado: ⬜
- [ ] `protectusernames=1`, políticas de contraseña
- [ ] Informe de seguridad sin alertas críticas
- Verificación: Site admin → Reports → Security overview

## T2.5 · Textos legales LGPD (SDD-CFG-05)
Estado: ⬜
- [ ] Páginas Aviso de Privacidad y Términos en pt-BR
- [ ] Banner de cookies (Aceitar / Recusar)
- [ ] Checkbox obligatorio en registro
- Verificación: registro y portada (manual)

## T2.6 · Verificación transversal de idioma (SDD-CFG-06)
Estado: ⬜
- [ ] 4 páginas clave 100% pt-BR (sin inglés/español)
- Verificación: captura Playwright