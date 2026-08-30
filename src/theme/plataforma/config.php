<?php
// theme/plataforma — tema hijo de boost. NO modifica theme/boost.
defined('MOODLE_INTERNAL') || die();

$THEME->name   = 'plataforma';
$THEME->parents = ['boost'];

$THEME->sheets = [];
$THEME->scss   = ['presets', '_extra'];

$THEME->enable_dock = false;

$THEME->rendererfactory = 'theme_overridden_renderer_factory';

// Hereda el resto (layouts, block support, etc.) del padre boost.
$THEME->layouts = [];