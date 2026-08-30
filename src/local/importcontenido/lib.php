<?php
// local/importcontenido — helpers del plugin. NO toca core.
defined('MOODLE_INTERNAL') || die();

/**
 * Devuelve el HTML de la grilla de tarjetas de módulos (para la portada).
 * Busca cursos de la categoría indicada y genera <article class="modulo-card">.
 *
 * @param string $categoryname Nombre de la categoría (default 'Módulos').
 * @return string HTML
 */
function local_importcontenido_render_modulos_grid(string $categoryname = 'Módulos'): string {
    global $DB;

    $category = $DB->get_field('course_categories', 'id', ['name' => $categoryname, 'parent' => 0]);
    if (empty($category)) {
        return '';
    }

    $courses = $DB->get_records('course', ['category' => $category, 'visible' => 1], 'sortorder ASC');
    if (empty($courses)) {
        return '';
    }

    $html = '<div class="modulos-grid">';
    foreach ($courses as $course) {
        $courseid  = (int)$course->id;
        $fullname  = format_string($course->fullname);
        $summary   = format_string($course->summary);
        $imageurl  = local_importcontenido_course_image($course);
        $url       = new moodle_url('/course/view.php', ['id' => $courseid]);

        $html .= '<article class="modulo-card card">';
        $html .= '<img class="modulo-card-img" src="' . s($imageurl) . '" alt="' . s($fullname) . '">';
        $html .= '<div class="modulo-card-body">';
        $html .= '<h3 class="modulo-card-title">' . $fullname . '</h3>';
        $html .= '<p class="modulo-card-summary">' . $summary . '</p>';
        $html .= '<a class="btn btn-primary" href="' . $url->out() . '">' . get_string('acessar', 'local_importcontenido') . '</a>';
        $html .= '</div></article>';
    }
    $html .= '</div>';
    return $html;
}

/**
 * Devuelve la URL de la imagen destacada del curso o un placeholder.
 */
function local_importcontenido_course_image(stdClass $course): string {
    global $OUTPUT;

    $fs = get_file_storage();
    $context = context_course::instance($course->id);
    $files = $fs->get_area_files($context->id, 'course', 'overviewfiles', 0, 'sortorder DESC, id ASC', false);
    foreach ($files as $file) {
        return moodle_url::make_pluginfile_url(
            $file->get_contextid(),
            $file->get_component(),
            $file->get_filearea(),
            $file->get_itemid(),
            $file->get_filepath(),
            $file->get_filename()
        )->out();
    }
    return $OUTPUT->image_url('default_module')->out();
}