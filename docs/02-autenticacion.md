# ESD-02 — Autenticación y Usuarios

> **Especificación de Ingeniería · Harness Engineer**
> Versión 1.0 · 2026-08-28 · Prioridad: **Alta**
> Relacionada con: `00-maestro.md` (ADR-4, ADR-6, ADR-7)

---

## 1. Propósito
Garantizar que solo usuarios autenticados accedan al contenido, con roles
mínimos (admin = desarrollador; usuario = consumidor de contenido), y que toda la
interfaz se presente en **portugués de Brasil (pt-BR)**.

## 2. Alcance
- Configuración del método de autenticación nativo de Moodle.
- Auto-registro simplificado (self-registration por email).
- Roles y permisos (dos perfiles operativos).
- Configuración de idioma pt-BR como único idioma activo.

## 3. Requisitos funcionales

### 3.1 Autenticación
- RF-A-01: **Autenticación manual** con usuario/contraseña + email (método `manual`).
- RF-A-02: Permitir **auto-registro** por email: Site admin → *Plugins → Autenticación →
  *Manejo manual de cuentas* → Activarlo y habilitar "Self registration".
- RF-A-03: Verificación por email opcional en v1 (decidir unívocamente al desplegar).
- RF-A-04: El login debe ser la puerta única al contenido (no accesible como invitado).
- RF-A-05: Tras login, redirigir a la **portada de módulos** (ver Spec-06).

### 3.2 Roles
| Rol | Capacidad | Notas |
|---|---|---|
| **Site admin** (desarrollador) | Total: crea cursos, secciones, sube recursos, gestiona usuarios | Único |
| **Usuario (`user` / rol por defecto Authenticated user)** | Ve y navega; NO edita ni sube | Auto-altas |

- RF-A-06: Deshabilitar el rol **Invitado** (guest) a nivel de sitio.
- RF-A-07: En los cursos, no matricular usuarios manualmente: basta la visibilidad
  pública del curso para usuarios autenticados (config de enrol).
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

## 5. Flujo de registro/login

```
[Portada] ──(clica "Entrar")──▶ [Login Moodle]
     │
     ├── tiene cuenta? ──▶ [Dashboard/Portada de módulos]
     │
     └── no ──▶ [Criar conta] (self registration)
                  └─ email + senha ──▶ (verificação) ──▶ [Portada de módulos]
```

## 6. Configuración en Moodle (checklist de ajustes)

| # | Ajuste | Ruta | Valor |
|---|---|---|---|
| 1 | Método autenticación | Plugins → Autenticación → Administrar | Habilitar `manual` + `email` (self) |
| 2 | Self registration | Plugins → Autenticación → Email-based self registration | ON |
| 3 | Guest access | Plugins → Enrolment | Desactivar `guest` |
| 4 | Rol por defecto | Users → Permissions → Define roles | Solo `Authenticated user` |
| 5 | Idioma sitio | Idioma → Configuración | `pt_BR` forzado |
| 6 | Ocultar selector idioma | Tema hijo → settings | Eliminar/drop zone del selector |

## 7. Criterios de aceptación (Test Harness)

| ID | Prueba | Pasos minimales | Resultado esperado | Estado |
|---|---|---|---|---|
| TC-A-01 | Registro self | Ir a "Criar conta", completar datos | Cuenta creada y activa | ☐ |
| TC-A-02 | Login correcto | Acceso con credenciales | Entra y ve portada | ☐ |
| TC-A-03 | Login incorrecto | Password erróneo | Mensaje de error, sin acceso | ☐ |
| TC-A-04 | Sin guest | Visitar curso sin sesión | Redirige a login (no "Entrar como invitado") | ☐ |
| TC-A-05 | Usuario sin permisos editar | Con cuenta usuario, abrir curso | No muestra opciones de edición | ☐ |
| TC-A-06 | Idioma pt-BR | Entrar como usuario anónimo/logado | Toda la UI en portugués | ☐ |
| TC-A-07 | Redirección post-login | Login desde portada | Retorna a portada de módulos | ☐ |

## 8. Riesgos y mitigaciones
| ID | Descripción | Prob. | Impacto | Mitigación |
|---|---|---|---|---|
| R-A-01 | Spam de cuentas auto-registro | Media | Medio | Captcha reCAPTCHA en registro + verificación por email obligatoria |
| R-A-02 | El usuario "ve el menú de administración" | Baja | Medio | Forzar tema limpio; revisar capacidades del rol |
| R-A-03 | El inglés del paquete por defecto aparece | Media | Baja | Abrir sitio en `pt_BR` forzado; desactivar resto de idiomas |

## 9. Definition of Done (DoD)
- [ ] Auto-registro y login funcionales.
- [ ] Sin acceso guest a cursos.
- [ ] Idioma del sitio 100% pt-BR (verificado en 4 páginas clave).
- [ ] Usuario estándar sin capacidades de edición verificado.
- [ ] Adjunta captcha/verificación anti-spam decidida.