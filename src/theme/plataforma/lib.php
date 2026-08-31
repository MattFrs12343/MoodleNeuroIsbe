<?php
// theme/plataforma/lib.php — NO toca core.
defined('MOODLE_INTERNAL') || die();

/**
 * Arma el SCSS completo: variables propias (presets) + contenido principal de
 * boost (bootstrap + estilos core) + componentes propios (_extra).
 */
function theme_plataforma_get_main_scss_content($theme) {
    global $CFG;

    $scss = '';
    $scss .= file_get_contents($CFG->dirroot . '/theme/plataforma/scss/presets.scss');
    $scss .= file_get_contents($CFG->dirroot . '/theme/boost/scss/preset/default.scss');
    $scss .= file_get_contents($CFG->dirroot . '/theme/plataforma/scss/_extra.scss');

    return $scss;
}
