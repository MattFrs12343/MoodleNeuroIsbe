# ESD-01 — Infraestructura y Despliegue

> **Especificación de Ingeniería · Harness Engineer**
> Versión 1.0 · 2026-08-28 · Prioridad: **Alta**
> Relacionada con: `00-maestro.md` (ADR-5)

---

## 1. Propósito
Definir los requisitos de infraestructura, los pasos de instalación de Moodle en
**hosting compartido**, y los criterios de aceptación para que la plataforma opere
de forma estable y segura.

## 2. Alcance
- Verificación de requisitos del hosting (PHP, BD, extensiones).
- Instalación del open source de Moodle.
- Configuración mínima inicial (idioma, directorios, cron, SSL).
- Definición del "Definition of Done" de despliegue.

## 3. Requisitos de infraestructura (Requisitos Funcionales/No Funcionales)

### 3.1 RNF-I-01 — Requisitos del hosting
| Parámetro | Requisito mínimo | Recomendado |
|---|---|---|
| PHP | 8.1+ (Moodle 4.4+ requiere 8.1; Moodle 4.5 requiere 8.1; verificar versión instalada) | 8.2 / 8.3 |
| Base de datos | MySQL 8.0+ o MariaDB 10.11+ | MariaDB 10.6–11 |
| Disco | 5 GB libres | 10 GB |
| RAM | 512 MB (PHP memory_limit) | 1 GB |
| Subida de archivos (`upload_max_filesize`) | 256 MB | 512 MB |
| `post_max_size` | 256 MB | 512 MB |
| Ejecución PHP (`max_execution_time`) | 120 s | 300 s |
| Dominio/Subdominio | Certificado SSL | Wildcard/auto (Let's Encrypt) |

> **Nota**: en hosting compartido cPanel, verificar **"PHP Version", "PHP Extensions"**
> y la posibilidad de activar: `intl`, `zip`, `gd`, `mbstring`, `curl`, `xmlrpc`,
> `soap`, `iconv`, `opcache`, `openssl`.

### 3.2 RNF-I-02 — Requisitos funcionales de instalación
- RF-I-01: Desplegar Moodle (versión estable **4.4.x o 4.5.x LTS-friendly**, en español/portugués) en el `DocumentRoot` del subdominio.
- RF-I-02: Crear base de datos `DB: moodle_db` + usuario `moodle_user` con contraseña fuerte y privilegios SOLO sobre esa BD.
- RF-I-03: Configurar el archivo `config.php` con rutas absolutas (`$CFG->dataroot` fuera o dentro de `www`, protegida).
- RF-I-04: Ejecutar el instalador web (`install.php`) y completar el formulario.
- RF-I-05: Instalar el paquete de idioma **pt-BR** (sin activarlo aún hasta Spec-02).

### 3.3 RNF-I-03 — Configuración de cron
- Cron CLI (recomendado) o cron web con autenticación por token.
- Frecuencia: **cada 1 minuto**.
- En cPanel: `Cron Jobs` → `*/1 * * * * /usr/bin/php /home/USER/public_html/admin/cli/cron.php`.

### 3.4 RNF-I-04 — Seguridad base del despliegue
- SSL activo en todo el sitio (`$CFG->wwwroot` con `https://`).
- `$CFG->preventexecpath` opcional; deshabilitar edición de temas desde la UI al final.
- Proteger `config.php` (permisos 400/440).
- Deshabilitar los informes de errores visibles: `$CFG->debug = 0` en producción.

## 4. Arquitectura de infraestructura

```
Internet
   │ https://portal.midominio.com
   ▼
Hosting Compartido (Apache/nginx + PHP-FPM/CGI)
   ├── public_html/
   │     ├── config.php
   │     └── admin/, auth/, course/, lib/, theme/, local/, mod/ ...
   ├── moodledata/  (dataroot FUERA de public_html si el hosting lo permite)
   │     ├── filedir/   → archivos de recursos (PDF, MP3, DOCX, TXT)
   │     ├── cache/     → cachés
   │     └── sessions/  → sesiones
   └── MySQL/MariaDB (moodle_db)
```

## 5. Pasos de despliegue (secuencia verificable)

| # | Tarea | Verificación |
|---|---|---|
| 1 | Crear subdominio `portal` + apuntar DNS | URL responde `204/200` |
| 2 | Crear BD `moodle_db` y usuario con permisos | Test conexión desde phpMyAdmin |
| 3 | Descargar Moodle y subir a `public_html/portal` | `php -l config.php` sin errores |
| 4 | Configurar `config.php` | Ruta `wwwroot` https correcta |
| 5 | Ejecutar instalador | Completa sin warnings críticos |
| 6 | Activar SSL + forzar https | `https://` en navegación |
| 7 | Programar cron 1 min | Línea en cPanel activa |
| 8 | Instalar idioma pt-BR | Paquete aparece en idiomas |

## 6. Criterios de aceptación (Test Harness)

| ID | Prueba | Pasos minimales | Resultado esperado | Estado |
|---|---|---|---|---|
| TC-I-01 | Instalación completa | Navegar a `install.php` con requisitos OK | Instalador finaliza, site visible | ☐ |
| TC-I-02 | Conexión BD | `php -r 'new PDO("mysql:host=".$argv[1],$argv[2],$argv[3]);' host user pass` | Sin excepciones | ☐ |
| TC-I-03 | SSL forzado | Visitar `http://` | Redirige a `https://` | ☐ |
| TC-I-04 | Cron operativo | Revisar `admin/report/status` | "El cron se ha ejecutado..." menor a 1 min | ☐ |
| TC-I-05 | Sin errores log | Revisar `admin/report/performanceinfo` y `debug=0` | Sin errores PHP | ☐ |

## 7. Riesgos y mitigaciones
| ID Risgo | Descripción | Prob. | Impacto | Mitigación |
|---|---|---|---|---|
| R-I-01 | PHP < 8.1 en hosting | Media | Alto | Verificar antes del despliegue; cambiar plan o versión PHP vía cPanel |
| R-I-02 | `memory_limit` bajo causa errores de subida | Media | Medio | Subir a 1 GB; ajustar `upload_max_filesize` |
| R-I-03 | Dataroot dentro de `www` expuesto | Baja | Crítico | Colocar en directorio privado o proteger con `.htaccess` |
| R-I-04 | Certificado SSL caducado | Media | Medio | Habilitar auto-renovación (Let's Encrypt) |

## 8. Definition of Done (DoD)
- [ ] Subdominio con HTTPS y nombre definitivo funcionando.
- [ ] Moodle instalado y navegable con la portada por defecto.
- [ ] Cron activo y sin errores en el panel de estado.
- [ ] `config.php` con `$CFG->debug = 0`.
- [ ] Documento de credenciales almacenado en gestor externo (no en repo).