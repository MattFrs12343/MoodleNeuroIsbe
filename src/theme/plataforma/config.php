<?php
// theme/plataforma — tema hijo de boost. NO modifica theme/boost.
defined('MOODLE_INTERNAL') || die();

$THEME->name   = 'plataforma';
$THEME->parents = ['boost'];

$THEME->sheets = [];
// $THEME->scss debe ser un callback (no un array de nombres de archivo): Moodle lo
// invoca para obtener el SCSS completo a compilar. Ver lib.php.
$THEME->scss   = function($theme) {
    return theme_plataforma_get_main_scss_content($theme);
};

$THEME->enable_dock = false;

// Pedido del desarrollador: el Dr. (rol "user") no necesita el enlace "Painel"
// (Dashboard, /my/) en el menú superior — el flujo de la plataforma es "Página
// Principal" -> curso, no un panel personal. Mecanismo oficial del tema (NO toca
// core): lib/classes/navigation/views/primary.php respeta
// $PAGE->theme->removedprimarynavitems para decidir si agrega el nodo 'myhome'.
$THEME->removedprimarynavitems = ['myhome'];

$THEME->rendererfactory = 'theme_overridden_renderer_factory';

// Hereda el resto (layouts, block support, etc.) del padre boost.
$THEME->layouts = [];