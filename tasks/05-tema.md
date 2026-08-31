# Fase 5 — Tema y Portada

> Spec: `specs/05-fase-tema.md` · ESD: `docs/06-portada-tema.md`
> Ejecutada 3/4 al 2026-08-31, con identidad visual completa (NeuroIsbe).

**Progreso:** 🟡 75% (3/4) · **Estado:** T5.3 bloqueada por Fase 3/4 (sin cursos reales)

## ▶ Próxima tarea
Solo queda T5.3 (portada con tarjetas), que espera a que exista al menos un módulo real
(SDD-EST-03). Pendiente transversal: confirmación **visual** (Playwright) cuando la
extensión de Chrome esté disponible — todo lo demás se verificó por `curl`/CSS.

---

## T5.1 · Copiar y activar tema hijo (SDD-TEM-01)
Estado: ✅ — ver detalle en `specs/05-fase-tema.md`

## T5.2 · Estilos SCSS (SDD-TEM-02)
Estado: ✅ — identidad visual completa NeuroIsbe (paleta, tipografía Space
Grotesk/IBM Plex, hero con imagen real, firma "sinapse"). Ver el hallazgo crítico sobre
errores SCSS silenciosos en `specs/05-fase-tema.md` y en la skill `boost-theme-custom-global`.

## T5.3 · Portada con tarjetas (SDD-TEM-03)
Estado: ⏸️ BLOQUEADA — sin cambios, espera módulos reales (Fase 3/4)

## T5.4 · Logo, favicon, footer (SDD-TEM-04)
Estado: ✅
- [x] Logo/favicon reales (con 2 bugs de Moodle 4.5.13 resueltos, ver spec)
- [x] Footer con enlaces a Aviso de Privacidade/Termos de Uso
- [ ] **Banner de cookies**: sigue pendiente (Moodle 4.5 no trae uno nativo) — única
      sub-tarea que queda abierta en T5.4
