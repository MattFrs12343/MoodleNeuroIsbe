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

    /**
     * El saludo nativo de Moodle (\core\user::welcome_message(), "Olá, {nombre}! 👋")
     * se anuló por completo (lang override en $CFG->dataroot/lang/pt_br_local/moodle.php
     * — pedido del desarrollador: sacarlo de la Página Principal). En su lugar hay DOS
     * saludos propios: uno permanente en "Meus cursos", y uno a pantalla completa en la
     * Página Principal pero SOLO para usuarios logueados (la vista "interna" del perfil;
     * el visitante sin sesión sigue viendo el hero público con el botón "Entrar"). No se
     * renderiza el HTML final acá: este método "standard_top_of_body_html" es el único
     * punto de extensión oficial que corre en TODAS las páginas sin tocar plantillas de
     * core, así que solo deja el dato (nombre de pila del usuario) en un elemento oculto
     * — disponible en CUALQUIER página donde haya sesión real (no solo "Meus cursos"),
     * para que el script de additionalhtmlfooter (mismo patrón ya usado para
     * constelaciones/splash) decida en JS, según la página, dónde armar cada widget.
     */
    public function standard_top_of_body_html() {
        $html = parent::standard_top_of_body_html();

        if (isloggedin() && !isguestuser()) {
            global $USER;
            $html .= \html_writer::tag('span', format_string($USER->firstname), [
                'id' => 'platform-greeting-data',
                'hidden' => 'hidden',
            ]);
        }

        return $html;
    }
}
