# ESD-06 — Portada y Tema Hijo (Front End)

> **Especificación de Ingeniería · Harness Engineer**
> Versión 1.0 · 2026-08-28 · Prioridad: **Alta**
> Relacionada con: `00-maestro.md` (ADR-1)

---

## 1. Propósito
Implementar la **portada de bienvenida con tarjetas por módulo** (curso) y un **tema
hijo** ligero que dé identidad visual a la plataforma **sin modificar el core** de Moodle.

## 2. Alcance
- Tema hijo (`theme_<nombre>` basado en `boost`, tema oficial).
- Portada: hero + grilla de tarjetas por módulo (cursos de la categoría `Módulos`).
- Responsive (móvil, tablet, desktop).
- pt-BR en todos los textos visibles.

## 3. Decisiones técnicas

- RN-F-01: Tema base **`boost`** (estándar, mantenido por Moodle). Debemos crear
  `theme_nombre` que "extienda" boost (child theme).
- RN-F-02: Todo el CSS adicional se incluye como `additional CSS` del tema o en el
  SCSS build del child theme; **no se toca** `theme/boost/*` original.
- RN-F-03: La portada se construye con **bloques estándar + HTML en la página inicial**
  (frontpage), mostrando cursos vía la actividad/recurso estándar (sin backend custom).
- RN-F-04: La interacción "tarjeta → abre curso" usa enlaces nativos
  `/course/view.php?id=<ID>`.
- RN-F-05: Imágenes de tarjeta = `course_overviewfiles` (imagen destacada) o imagen
  por defecto.

## 4. Reglas funcionales de la portada

- RF-F-01: **Hero**: título breve + subtítulo motivador + botón CTA "Entrar" (si no logueado).
- RF-F-02: **Grilla de módulos**: tarjetas ordenadas por número de módulo; cada tarjeta:
  imagen, `Módulo NN`, título, resumen (summary), botón "Acessar".
- RF-F-03: Solo mostrar **cursos visibles para el usuario** (el propio Moodle resuelve el permiso).
- RF-F-04: Si el usuario no está logueado y la plataforma exige login: mostrar login/registro y
  aún así la portada pública permite visitar el listado (según política de Spec-02).
- RF-F-05: Menú superior mínimo: logo, "Módulos", "Minha conta", "Sair".
- RF-F-06: Footer: nombre del proyecto, año, privacidad (si aplica), versión.

## 5. Estructura del tema hijo

```
theme_nombre/
├── config.php          (parents = ['boost'], blocks, SCSS)
├── lang/pt_br/
│   └── theme_nombre.php  (strings UI propia)
├── scss/
│   ├── presets.scss    (paleta de color, fuentes)
│   └── _extra.scss     (estilos de tarjetas, hero, iframe 16:9)
├── templates/
│   ├── frontpage.mustache     (portada custom)
│   └── core/…                (overrides mínimos si aplica)
└── pix/               (logo, favicon, iconos)
```

## 6. Configuración de la portada (frontpage)

| # | Ajuste | Ruta | Valor |
|---|---|---|---|
| 1 | Qué mostrar en la página inicial | Site admin → Página inicial | "Página HTML/CSS personalizada" |
| 2 | Contenido de la página inicial | HTML agent | Hero + <div id="modulos-grid">… |
| 3 | Tema activo | Site admin → Apariencia → Tema selector | `theme_nombre` |
| 4 | Logo | Tema → settings | Logo desde `pix/logo.svg` |
| 5 | Ícono Favicon | Apariencia | Favicon del tema |
| 6 | CSS adicional en curso | Curso → edición | Mantener consistencia 16:9 de iframe |

> La **grilla de tarjetas** puede generarse con un pequeño plugin `local_modulos`
> (recomendado más adelante) que consulte `mdl_course` para listar los cursos de la
> categoría `Módulos`, o con bloques nativos `cursos` en la página inicial. En v1:
> **bloques nativos + personalización de tarjetas vía CSS/HTML**.

## 7. Criterios de aceptación (Test Harness)

| ID | Prueba | Pasos | Resultado esperado | Estado |
|---|---|---|---|---|
| TC-F-01 | Portada muestra cursos | Login y abrir raíz `/` | Grilla con tarjetas de la categoría Módulos | ☐ |
| TC-F-02 | Tarjeta → curso | Clic en tarjeta | Abre `/course/view.php?id=..` | ☐ |
| TC-F-03 | Responsive | 380 px / 768 px / 1280 px | Grilla 1/2/3+ columnas sin scroll horizontal | ☐ |
| TC-F-04 | Textos pt-BR | Inspeccionar UI | Sin strings en inglés en portada/menú | ☐ |
| TC-F-05 | Core intacto | `git status theme/boost` | Sin modificaciones en core | ☐ |
| TC-F-06 | Login desde portada | Clic "Entrar" | Redirige a `/login/index.php` | ☐ |
| TC-F-07 | Hero visible | Cargar portada | Título + CTA presentes | ☐ |

## 8. Riesgos y mitigaciones
| ID | Descripción | Prob. | Impacto | Mitigación |
|---|---|---|---|---|
| R-F-01 | Actualizar Moodle rompe el tema hijo | Media | Medio | Tema hijo versionado; test tras cada upgrade |
| R-F-02 | Tarjetas sin imagen (feo) | Media | Baja | Imagen por defecto configurada |
| R-F-03 | Textos no traducidos | Media | Media | `lang/pt_br` y revisión manual 4 páginas |
| R-F-04 | Portada con mucha lógica propia no mantenible | Media | Media | Preferir bloques nativos; no JS complejo |

## 9. Definition of Done (DoD)
- [ ] Tema hijo activo basado en boost, con `parents=['boost']`.
- [ ] Portada con hero + grilla de tarjetas funcional y responsive.
- [ ] Logo, favicon y footer configurados.
- [ ] Textos de la interfaz propios en pt-BR.
- [ ] Verificado: `theme/boost` intacto en control de versiones.