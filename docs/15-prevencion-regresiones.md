# Prevención de Regresiones — Reglas del Proyecto

> Cuando "corregí algo leve y se rompió lo que funcionaba" ocurre, la causa casi siempre es
> la misma: **cambios NO aislados, sin prueba de regresión y sobre el mismo entorno de
> producción**. Este documento fija las reglas para que no vuelva a pasar en este proyecto.

---

## 1. Regla de oro

> **"Si algo se rompe y antes funcionaba, es prioridad máxima. Se REVIERTE primero
> y se analiza la causa ANTES de seguir tocando."** No se parchea sobre parche.

## 2. Entornos (no tocar producción a tontas)

| Entorno | Para qué | Regla |
|---|---|---|
| **Producción** | Uso real del Dr./usuarios | SOLO recibe cambios aprobados y verificados |
| **Staging** (copia funcional) | Probar cambios antes de mandarlos a producción | Es EL lugar para correcciones |
| **Local** | Desarrollo experimental | Libre para probar, se pierde sin pena |

- Cualquier corrección se hace **primero en staging**. Solo al pasar la verificación se
  despliega a producción.
- Backups (BD + dataroot) ANTES de cada cambio significativo (Spec-07).

## 3. Cambios atómicos

- **Un cambio a la vez**: tocar el tema/SCSS ≠ mover lógica de fixtures ≠ cambiar textos.
- Cada corrección es **pequeña y con propósito único**.
- Al terminar cada cambio: **verificar de inmediato** que lo tocado sigue bien **y que lo
  vecino no se rompió** (regla de "prueba por cercanía").

## 4. Prueba de regresión (la clave)

Cada spec tiene una **matriz TC**. Esos TC son nuestro "suite de regresión" guardado:

- Al corregir X: ejecutar los TC de X **y los de los módulos vecinos** (ej. toqué el tema
  → re-correr TC-F (portada) + TC-V (video) + un TC de login).
- **Frecuencia ligera**: tras cada fase o lote, correr toda la matriz de las fases cerradas.
- **Checklist vivo**: `docs/CHANGELOG.md` guarda qué se tocó, para saber qué re-verificar.

## 5. Git: la red de seguridad

- Rama por tarea: `fix/portada-hero`, `feat/import-200videos`; nunca commits directos a la
  rama principal.
- **Commits atómicos**: 1 corrección = 1 commit con mensaje claro.
- Antes de mandarlo a producción: `git diff` revisado y ausencia de "código muerto".
- **Tag por fase** (`v1-infra`, `v1-config`, ...) → siempre puedes volver a un estado sano.
- Nunca commit de una compilación/cache (`moodledata`, `*.scss.php` compilados).

## 6. Antes de tocar código que funciona

1. Guardar estado: `git status` limpio y tag si aplica.
2. (Opcional) Backup rápido de BD si el cambio toca datos.
3. Anotar en `CHANGELOG` qué vas a cambiar y por qué.
4. Hacer el cambio en staging.
5. Correr TC afectados + vecinos.
6. Solo entonces: despliegue y cierre.

## 7. Frontend específico (tema Boost) — protección extra

- El SCSS se compila vía `purge_caches`. Tras cada cambio visual:
  - Guardar **captura de referencia** (Playwright) ANTES del cambio y DESPUÉS.
  - Comparar 380 / 768 / 1280 px. Si cambia algo que no era el objetivo → se revisa.
- Nunca mezclar en el mismo cambio: **apariencia** y **lógica** (rutas, permisos, textos).

## 8. Regla para el agente IA (se añade a CLAUDE.md)
- El agente NO modifica código "que no se le pidió" aunque lo vea "sucio".
- Si detecta una regresión: **para**, informa el TC fallido y espera instrucción.
- Al terminar su cambio, el agente **siempre** ejecuta los TC de la zona tocada y reporta
  el resultado por escrito (no "lo arreglé y listo").

## 9. Checklist de corrección sin drama

- [ ] Cambio hecho en **staging**, no en producción.
- [ ] Cambio **atómico** (un solo propósito).
- [ ] TC de la zona tocada: ☑
- [ ] TC de zonas vecinas: ☑
- [ ] Captura de referencia si es frontend: ✓ comparada.
- [ ] Commit en rama + `git diff` revisado.
- [ ] `CHANGELOG` actualizado.
- [ ] Despliegue a producción solo tras verificación en staging.