<?php
namespace theme_plataforma\output;

defined('MOODLE_INTERNAL') || die();

/**
 * En Moodle 4.5.13 el logo configurado vía Site admin (core_admin/logo,
 * core_admin/logocompact) se sirve con una URL rota: moodle_url::make_pluginfile_url()
 * concatena la revisión del tema con el nombre de archivo sin separador
 * ("{rev}{filename}"), y core_admin_pluginfile() (admin/lib.php) no puede volver a
 * separarlos al parsear los argumentos — siempre devuelve 404. No es viable
 * arreglarlo sin tocar el core, así que el logo se sirve directo desde pix/
 * del propio tema (mismo mecanismo, ya probado sin problemas, que usa el
 * fallback nativo de favicon()).
 */
class core_renderer extends \theme_boost\output\core_renderer {

    public function get_logo_url($maxwidth = null, $maxheight = 200) {
        return $this->image_url('logo', 'theme');
    }

    public function get_compact_logo_url($maxwidth = 300, $maxheight = 300) {
        return $this->image_url('logo', 'theme');
    }
}
