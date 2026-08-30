# Presupuesto de Proyecto — Plataforma Educativa (v1.2 — rango acordado)

> Documento de cotización **v1.2 · 2026-08-28**
> Desarrollador: **Jr. (Bolivia, con asistencia de IA)** · Cliente: **Brasil (Dr.)**
> Moneda de pago: **Bolivianos (Bs)**
> Rango objetivo acordado: **Bs 5,500 – 7,000**
>
> ## ✅ ACUERDO CERRADO — 2026-08-28 · **Total: Bs 6,000**
> *Aceptado por el cliente. Detalle partida por partida en `11-justificacion-presupuesto.md`.*

---

## 1. Resumen ejecutivo

| Concepto | Valor |
|---|---|
| **Carga total estimada** | **≈ 64 horas** |
| **Tarifa desarrollo** | **Bs 120 / hora** |
| **Tarifa carga de contenido** (ingreso de datos) | **Bs 50 / hora** |
| **Subtotal por horas** | **≈ Bs 5,650** |
| **Cifra a cotizar (fija)** | **Bs 6,000** |
| **Rango de cotización** | **Bs 5,500 – 6,500** |

> La carga de contenido (subir 200 videos, crear recursos) es **ingreso de datos**, por eso
> lleva tarifa menor. El total de 6,000 incluye un **margen de contingencia (~6%)** válido
> para imprevistos y revisiones del cliente.

## 2. Estimación de horas y costo por fase

| # | Fase | Horas | Tarifa | Costo |
|---|---|---|---|---|
| 1 | Levantamiento y plan de carga | 3 h | 120 | Bs 360 |
| 2 | Infraestructura y despliegue (instalación Moodle, BD, SSL, cron, seguridad base) | 8 h | 120 | Bs 960 |
| 3 | Configuración base (pt-BR, login, auto-registro, roles, LGPD, cookies, textos) | 6 h | 120 | Bs 720 |
| 4 | **Carga de contenido** (200 videos + audio + docs) | **29 h** | **50** | **Bs 1,450** |
| 4a | └ Estructura, nomenclatura, carpeta Dropbox + script de importación | 6 h | 120 | Bs 720 |
| 4b | └ Subir videos a YouTube no listado (200 × ~3 min) | 10 h | 50 | Bs 500 |
| 4c | └ Crear recursos (embeds + links Dropbox) vía script y verificar | 8 h | 50 | Bs 400 |
| 4d | └ Audios y documentos (~60 recursos) | 5 h | 50 | Bs 250 |
| 5 | Tema hijo + portada con tarjetas | 10 h | 120 | Bs 1,200 |
| 6 | QA, manual pt-BR, documentación, puesta en producción | 8 h | 120 | Bs 960 |
| | **SUBTOTAL** | **≈ 64 h** | | **Bs 5,650** |
| | **Margen de contingencia (~6%)** | — | | **Bs 350** |
| | **TOTAL A COTIZAR** | | | **≈ Bs 6,000** |

## 3. Tarifa — referencia de mercado

- Programador jr. Bolivia local: Bs 45 – 80/h.
- Freelance internacional (Brasil) + IA: **Bs 120/h** de desarrollo es razonable.
- Carga de contenido (repetitivo, sin complejidad): **Bs 50/h**.

| Rango de negociación | Total (Bs) |
|---|---|
| **Cifra recomendada** | **Bs 6,000** |
| Rango a presentar | Bs 5,500 – 6,500 |
| Límite inferior (solo si presiona) | Bs 5,200 |

### 3.1 Impuestos (si se emite factura en Bolivia)
| Régimen | Recargo |
|---|---|
| IVA (13%) | + Bs 780 → Precio factura ≈ **Bs 6,780** |
| Pago informal/internacional | **Bs 6,000** netos |

## 4. Conversión a Reales (referencia)

> ⚠️ A mercado **1 real ≈ 1.2 – 1.3 Bs** (no "1 Bs = 2 reales"). Cotizar y cobrar en **Bs**.

| Monto (Bs) | Con "1 Bs = 2 R$" | Con mercado (1 Bs ≈ 0.80 R$) |
|---|---|---|
| 6,000 | R$ 12,000 | **R$ 4,800** |
| 5,500 | R$ 11,000 | R$ 4,400 |
| 6,500 | R$ 13,000 | R$ 5,200 |

## 5. Costos recurrentes (los paga el cliente, no son tuyos)
- Hosting compartido (propiedad del desarrollador): reembolso o incluido en servicio.
- Dominio propio: ~Bs 200 – 400/año.
- SSL (Let's Encrypt): gratis. · Dropbox y YouTube del Dr.: ya existentes.

## 6. Formas de pago (propuesta)

| Hito | % | Entrega |
|---|---|---|
| Arranque (infra desplegada + Moodle instalado) | **50%** | Bs 3,000 |
| Entrega final (QA OK, manual pt-BR, docs) | **50%** | Bs 3,000 |

## 7. Exclusiones y condiciones
- El cliente entrega: videos (Dropbox), audios MP3, documentos y la licencia del curso.
- Confirmar **licencia de uso** del curso (personal vs. terceros) antes de abrir a varios usuarios.
- 30 días de soporte bonificado tras la entrega; mantenimiento posterior se cotiza aparte.
- Nuevo alcance = cotización nueva (work order).
- Entrega estimada: **2 – 4 semanas**. Medios de pago: transferencia internacional en Bs/USD.