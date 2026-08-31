# ESD-02 — Autenticación y Usuarios

> **Especificación de Ingeniería · Harness Engineer**
> Versión 1.0 · 2026-08-28 · Prioridad: **Alta**
> Relacionada con: `00-maestro.md` (ADR-4, ADR-6, ADR-7)

---

> ⚠️ **Actualizado 2026-08-30**: la licencia del curso comprado por el Dr. es de
> **uso personal, un solo usuario** (confirmado con el desarrollador). Por lo tanto
> **NO hay auto-registro público**: la única cuenta de consumo de contenido es la del
> Dr., creada manualmente por el admin. Ver `docs/08-legal-lgpd.md` RF-L-08.

## 1. Propósito
Garantizar que solo el usuario autorizado (el Dr., titular de la licencia del curso)
acceda al contenido, con roles mínimos (admin = desarrollador; usuario = el Dr.), y que
toda la interfaz se presente en **portugués de Brasil (pt-BR)**.

## 2. Alcance
- Configuración del método de autenticación nativo de Moodle.
- **Auto-registro DESHABILITADO**: cuenta única creada manualmente por el admin (sin
  self-registration por email).
- Roles y permisos (dos perfiles operativos).
- Configuración de idioma pt-BR como único idioma activo.

## 3. Requisitos funcionales

### 3.1 Autenticación
- RF-A-01: **Autenticación manual** con usuario/contraseña + email (método `manual`).
- RF-A-02: **Auto-registro DESHABILITADO** (self-registration = OFF). La única cuenta de
  consumo (el Dr.) la crea el admin manualmente: Site admin → *Users → Add a new user*.
- RF-A-03: Sin verificación por email (no aplica: no hay alta pública).
- RF-A-04: El login debe ser la puerta única al contenido (no accesible como invitado,
  ni existe pantalla de "Criar conta").
- RF-A-05: Tras login, redirigir a la **portada de módulos** (ver Spec-06).
- RF-A-05b: Si en el futuro la licencia cambia y se autoriza a más usuarios, revisar
  primero `docs/08-legal-lgpd.md` (RF-L-08/RF-L-10) antes de reactivar cualquier alta.

### 3.2 Roles
| Rol | Capacidad | Notas |
|---|---|---|
| **Site admin** (desarrollador) | Total: crea cursos, secciones, sube recursos, gestiona usuarios | Único |
| **Usuario (`user` / rol por defecto Authenticated user)** | Ve y navega; NO edita ni sube | **Una sola cuenta** (el Dr.), creada por el admin — sin auto-alta |

- RF-A-06: Deshabilitar el rol **Invitado** (guest) a nivel de sitio.
- RF-A-06b: Deshabilitar **self registration** a nivel de sitio (evita altas públicas).
- RF-A-07: En los cursos, no matricular usuarios manualmente uno a uno: basta la
  visibilidad del curso para el usuario autenticado (config de enrol) dado que hay un
  único usuario de consumo.
- RF-A-08: El botón "Administration/Admin" solo visible para el rol admin.

### 3.3 Idioma pt-BR
- RF-A-09: Instalar paquete `pt_br` (Spec-01).
- RF-A-10: Fijar **idioma por defecto del sitio = pt_BR**; ocultar selector de idiomas
  (así el usuario de la plataforma solo ve portugués).
- RF-A-11: Configurar "idioma forzado": Site admin → *Idioma* → *Configuración de idioma*
  → "Idioma del sitio por defecto" = `Português - Brasil`.

> Cadena clave de idioma a revisar: login (`login`), registro (`signup`),
> "Sair" (logout), "Painel" (dashboard), "Meus cursos".

## 4. Modelo de datos (esquema nativo `mdl_*`)
No hay esquema adicional en v1. Tablas relevantes que asegurar en backup:
```
mdl_user, mdl_user_enrolments, mdl_enrol, mdl_role_assignments,
mdl_config (contiene la config de idioma y auth), mdl_sessions
```

## 5. Flujo de login (sin registro público)

```
[Portada] ──(clica "Entrar")──▶ [Login Moodle]
     │
     ├── tiene cuenta (el Dr.)? ──▶ [Dashboard/Portada de módulos]
     │
     └── no tiene cuenta ──▶ (no hay "Criar conta"; solo el admin crea la cuenta
                              manualmente vía Site admin → Users → Add a new user)
```

## 6. Configuración en Moodle (checklist de ajustes)

| # | Ajuste | Ruta | Valor |
|---|---|---|---|
| 1 | Método autenticación | Plugins → Autenticación → Administrar | Habilitar solo `manual` |
| 2 | Self registration | Plugins → Autenticación → Email-based self registration | **OFF** (deshabilitado) |
| 3 | Guest access | Plugins → Enrolment | Desactivar `guest` |
| 4 | Rol por defecto | Users → Permissions → Define roles | Solo `Authenticated user` |
| 5 | Cuenta del Dr. | Users → Add a new user | Creada manualmente por el admin |
| 6 | Idioma sitio | Idioma → Configuración | `pt_BR` forzado |
| 7 | Ocultar selector idioma | Tema hijo → settings | Eliminar/drop zone del selector |

## 7. Criterios de aceptación (Test Harness)

| ID | Prueba | Pasos minimales | Resultado esperado | Estado |
|---|---|---|---|---|
| TC-A-01 | Sin registro público | Ir a `/login/signup.php` | No disponible / redirige (self-registration OFF) | ☐ |
| TC-A-02 | Login correcto | Acceso con credenciales del Dr. | Entra y ve portada | ☐ |
| TC-A-03 | Login incorrecto | Password erróneo | Mensaje de error, sin acceso | ☐ |
| TC-A-04 | Sin guest | Visitar curso sin sesión | Redirige a login (no "Entrar como invitado") | ☐ |
| TC-A-05 | Usuario sin permisos editar | Con cuenta del Dr., abrir curso | No muestra opciones de edición | ☐ |
| TC-A-06 | Idioma pt-BR | Entrar como usuario anónimo/logado | Toda la UI en portugués | ☐ |
| TC-A-07 | Redirección post-login | Login desde portada | Retorna a portada de módulos | ☐ |

## 8. Riesgos y mitigaciones
| ID | Descripción | Prob. | Impacto | Mitigación |
|---|---|---|---|---|
| R-A-01 | Alguien reactiva self-registration por error en una actualización | Baja | Alto (viola licencia de uso personal) | TC-A-01 en la matriz de regresión de cada fase; revisar `mdl_config.registerauth` tras cada cambio de auth |
| R-A-02 | El usuario "ve el menú de administración" | Baja | Medio | Forzar tema limpio; revisar capacidades del rol |
| R-A-03 | El inglés del paquete por defecto aparece | Media | Baja | Abrir sitio en `pt_BR` forzado; desactivar resto de idiomas |

## 9. Definition of Done (DoD)
- [ ] Self-registration deshabilitado y verificado (TC-A-01).
- [ ] Cuenta única del Dr. creada manualmente y login funcional.
- [ ] Sin acceso guest a cursos.
- [ ] Idioma del sitio 100% pt-BR (verificado en 4 páginas clave).
- [ ] Usuario estándar sin capacidades de edición verificado.