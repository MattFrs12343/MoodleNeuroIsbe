---
name: textos-ptbr-lgpd
description: Reglas de traducción pt-BR y textos legales LGPD para cualquier string visible al usuario final (UI, avisos, banners). Usar al escribir o revisar cualquier texto de cara al Dr./usuarios.
---

# Textos pt-BR y legales (LGPD)

> Referencia: `docs/08-legal-lgpd.md`, `CLAUDE.md` §3.3 (todo string visible al cliente en pt-BR).

## Regla de oro
Todo texto visible al **usuario final** (Dr./usuarios) va en **pt-BR**, sin excepción.
El desarrollador se comunica en español con el agente; el agente responde en español,
pero **nunca** escribe strings de UI en español o inglés.

## Glosario aprobado
| Concepto | pt-BR |
|---|---|
| Login (acción) | Entrar |
| Mi cuenta | Minha conta |
| Logout | Sair |
| Descargar | Baixar |
| Módulos | Módulos |
| Acceder (a un curso) | Acessar |
| Aviso de privacidad | Aviso de Privacidade |
| Términos de uso | Termos de Uso |

## Textos legales obligatorios (RF-L de `docs/08-legal-lgpd.md`)
- Checkbox obligatorio en registro: `☐ Li e aceito o Aviso de Privacidade`.
- Banner de cookies con opciones **Aceitar / Recusar**.
- Footer con enlaces a Aviso de Privacidade y Termos de Uso.
- Aviso de contenido con copyright: "Material de curso comercial — uso restringido"
  (no reproducir el curso ni compartirlo fuera de la plataforma).

## Antes de dar por cerrado un texto
1. ¿Está 100% en pt-BR (sin mezclar términos en inglés/español)?
2. ¿Usa el glosario aprobado (no inventar sinónimos sueltos)?
3. Si es un texto legal: ¿está alineado con `docs/08-legal-lgpd.md` y no contradice la
   licencia del curso (personal vs. terceros — ver `docs/13-info-necesaria.md` §4, aún
   pendiente de confirmación del Dr.)?
4. ¿Los errores/trazas de Moodle siguen ocultos en producción (`debug=0`)? Un texto legal
   no reemplaza esa regla técnica.
