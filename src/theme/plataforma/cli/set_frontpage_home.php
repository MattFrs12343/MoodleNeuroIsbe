<?php
// Script de una sola ejecución (no forma parte del runtime del tema): arma la
// Página Principal con un hero + una sección "Como funciona" con íconos SVG
// (ver theme/plataforma/scss/_extra.scss, clases .hero/.features-*). Usa la
// API pública de Moodle (course_update_section) sobre la sección 1 del curso
// sitio — el mismo lugar donde "Activar edición" en la portada permite agregar
// contenido a mano — no toca core ni la base de datos directo.
//
// IMPORTANTE: el HTML de más abajo usa <div> y NO <section>/<svg> porque
// format_text()/HTMLPurifier de esta instalación descarta por completo
// cualquier <svg>, <path>, <circle> o <section> (verificado con un script de
// prueba antes de escribir esto) — de ahí que los íconos vivan como fondo CSS
// en el tema, no como HTML embebido en el contenido de la portada.
//
// Uso: php theme/plataforma/cli/set_frontpage_home.php
define('CLI_SCRIPT', true);
require(__DIR__ . '/../../../config.php');
require_once($CFG->dirroot . '/course/lib.php');
require_once($CFG->libdir . '/clilib.php');

$loginurl = (new moodle_url('/login/index.php'))->out(false);

$html = <<<HTML
<div class="hero">
    <div class="hero-inner">
        <span class="hero-eyebrow">Portal de Estudos</span>
        <h1 class="hero-title">NeuroIsbe</h1>
        <p class="hero-subtitle">Videoaulas, materiais e acompanhamento do seu progresso, tudo em um só lugar, no seu ritmo.</p>
        <a class="btn btn-primary btn-lg" href="{$loginurl}">Entrar</a>
    </div>
</div>
<div class="features-section">
    <div class="features-header">
        <h2>Como funciona</h2>
        <span class="synapse-rule"></span>
    </div>
    <div class="features-grid">
        <div class="feature-item">
            <span class="feature-icon feature-icon--video"></span>
            <h3>Videoaulas</h3>
            <p>Aulas organizadas por módulo, disponíveis sempre que você precisar rever um conteúdo.</p>
        </div>
        <div class="feature-item">
            <span class="feature-icon feature-icon--download"></span>
            <h3>Materiais para baixar</h3>
            <p>Baixe apostilas e áudios de apoio para estudar também offline, no seu tempo.</p>
        </div>
        <div class="feature-item">
            <span class="feature-icon feature-icon--progress"></span>
            <h3>Progresso no seu ritmo</h3>
            <p>Avance módulo a módulo e acompanhe sua evolução, sem prazos nem pressa.</p>
        </div>
        <div class="feature-item">
            <span class="feature-icon feature-icon--lock"></span>
            <h3>Acesso seguro e pessoal</h3>
            <p>Conteúdo protegido: acesso individual e intransferível, só seu.</p>
        </div>
    </div>
</div>
HTML;

$modinfo = get_fast_modinfo($SITE);
$sectioninfo = $modinfo->get_section_info(1);
if (!$sectioninfo) {
    cli_error('No existe la sección 1 del curso sitio (id=1) — revisar course_create_sections_if_missing().');
}

$sectionrecord = new stdClass();
$sectionrecord->id = $sectioninfo->id;
$sectionrecord->course = $SITE->id;
$sectionrecord->section = $sectioninfo->section;

course_update_section($SITE->id, $sectionrecord, [
    'summary' => $html,
    'summaryformat' => FORMAT_HTML,
]);

cli_writeln('Listo: sección 1 (Página Principal) actualizada con hero + "Como funciona".');
