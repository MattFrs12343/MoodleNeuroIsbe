# ESD-08 — Cumplimiento Legal y LGPD

> **Especificación de Ingeniería · Harness Engineer**
> Versión 1.0 · 2026-08-28 · Prioridad: **Media**
> Relacionada con: `00-maestro.md` (GO-5), `02-autenticacion.md`

---

## 1. Propósito
Asegurar que la plataforma cumpla con la **LGPD** (Ley General de Protección de Datos
brasileña, Lei 13.709/2018) y que el manejo del **contenido de terceros** (curso comprado
por el Dr.) respete derechos de autor y términos del contrato de compra.

## 2. Alcance
- Datos personales: registro, email, contraseña, curso/navegación.
- Textos legales: Aviso de Privacidad, Términos de Uso, consentimiento de cookies.
- **Gestión de contenido con copyright** (videos del curso comprado).
- Derechos del titular (acceso, corrección, eliminación).

## 3. Datos personales tratados

| Dato | Finalidad | Base legal (LGPD) | Retención |
|---|---|---|---|
| Nombre | Identificación de cuenta | Consentimiento (`art. 7º I`) | Mientras dure la cuenta |
| Email | Autenticación y recuperación | Consentimiento | Mientras dure la cuenta |
| Contraseña (hash) | Acceso seguro | Legítimo interés / seguridad | Mientras dure la cuenta |
| Registros de acceso | Seguridad y auditoría | Cumplimiento obligación legal | Según marca temporal (sugerido 6 meses) |
| Fecha/hora de login | Auditoría | Obligación legal | 6 meses |

## 4. Requisitos funcionales

### 4.1 Privacidad y consentimiento
- RF-L-01: Mostrar **Aviso de Privacidad** en el registro (enlace + checkbox obligatorio).
- RF-L-02: Mostrar **banner de cookies** con opción "Aceitar / Recusar" (y enlace al Aviso).
- RF-L-03: Publicar **Términos de Uso** accesibles desde el pie (footer) de la portada.
- RF-L-04: Permitir al usuario **solicitar** (vía email) la exportación/eliminación de su cuenta.
- RF-L-05: Configurar Moodle para el borrado de cuenta del propio usuario (o proceso manual del admin).
- RF-L-06: Registrar los consentimientos obtenidos (fecha del alta) como evidencia.

> En Moodle: *Users → Policies → User policies* (configurar banner/aviso) y activar
> "borrado de cuenta por el propio usuario". Las políticas legales se alojan en una
> **página estática** del sitio o recurso HTML del pie.

### 4.2 Contenido con copyright (videos del curso comprado)
- RF-L-07: **Restricción de distribución**: los videos NO se re-publican como públicos;
  solo usuarios autenticados y autorizados los ven/descargan.
- RF-L-08: **Licencia del curso RESUELTA (2026-08-30)**: es de **uso personal, un solo
  usuario**. La plataforma queda limitada al **Dr. como único usuario** — sin
  auto-registro, sin altas de terceros (ver `docs/02-autenticacion.md` §3.1, actualizado).
- RF-L-09: Mantener los archivos en el **Dropbox del Dr.** (guardar en privado, compartidos
  solo con quien corresponda); **no** subirlos a YouTube público ni a hosting con URL pública.
- RF-L-10: Documentar la trazabilidad: quién puede acceder a qué (registro de usuarios autorizados).
- RF-L-11: Establecer aviso interno: "Material de curso comercial — uso restringido".

## 5. Modelo de consentimiento (mínimo)
```
Registro:  ☐ Li e aceito o Aviso de Privacidade (obrigatório)
Cookies:   Banner → Aceitar / Recusar (decisión se persistirá según política)
```
Moodle nativo guarda la aceptación en `mdl_user_privacy` / `mdl_user_lastaccess` según
configuración (`userconsent`). Si no es suficiente, plugin `local` de registro de
consentimiento en `mdl_usermenu` (decisión de implementación futura).

## 6. Criterios de aceptación (Test Harness)

| ID | Prueba | Pasos | Resultado esperado | Estado |
|---|---|---|---|---|
| TC-L-01 | Checkbox aviso en registro | Ir a "Criar conta" | Checkbox obligatorio presente y funcional | ☐ |
| TC-L-02 | Banner cookies | Visitar portada anónimo | Banner visible con Aceptar/Recusar | ☐ |
| TC-L-03 | Fuente legal en footer | Portada | Aviso de Privacidad y Términos accesibles | ☐ |
| TC-L-04 | Solicitud de borrado de cuenta | Enviar solicitud al admin | Proceso documentado y accesible | ☐ |
| TC-L-05 | Sin acceso público a videos | Video sin login | No se reproduce ni se descarga | ☐ |
| TC-L-06 | Lista de autorizados | Revisar usuarios inscritos | Coincide con el registro autorizado (RF-L-10) | ☐ |

## 7. Riesgos y mitigaciones
| ID | Descripción | Prob. | Impacto | Mitigación |
|---|---|---|---|---|
| R-L-01 | Redistribución ilegal del curso comprado | Media | Alto (multas/sanciones del vendedor) | Restricción de acceso, registro de autorizados, verificar licencia ANTES de abrir a varios usuarios |
| R-L-02 | Incumplimiento LGPD (falta de aviso) | Media | Alto (multa ~2% facturación) | Aviso de privacidad y consentimientos implementados |
| R-L-03 | Datos de usuarios eliminados ambiguos | Baja | Medio | Proceso de borrado documentado |
| R-L-04 | Enlaces públicos de Dropbox | Media | Medio | Enlaces solo de visualización; rotación si se comparte accidentalmente |

## 8. Definition of Done (DoD)
- [ ] Aviso de Privacidad y Términos publicados en pt-BR y accesibles.
- [ ] Banner de cookies operativo.
- [ ] Proceso de solicitud/borrado de cuenta documentado.
- [x] Decisión de licencia del curso RESUELTA: **uso personal, un solo usuario** (2026-08-30).
- [x] Lista de usuarios autorizados al contenido del curso definida: **solo el Dr.**