<?php
// local/importcontenido/cli/importar.php
// Importa un CSV con la estructura Módulo -> Unidad -> Recursos usando la API de Moodle.
// Uso: php local/importcontenido/cli/importar.php --archivo=lista.csv [--categoria=Módulos] [--dryrun]
define('CLI_SCRIPT', true);
require(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/clilib.php');
require_once($CFG->dirroot . '/course/lib.php');
require_once($CFG->dirroot . '/course/modlib.php');

list($options, $unrecognized) = cli_get_params([
    'archivo'   => '',
    'categoria' => 'Módulos',
    'dryrun'    => false,
    'help'      => false,
], ['h' => 'help']);

if ($options['help'] || empty($options['archivo']) || !file_exists($options['archivo'])) {
    cli_writeln("Uso:");
    cli_writeln("  php local/importcontenido/cli/importar.php --archivo=lista.csv [--categoria=Módulos] [--dryrun]");
    exit(($options['help'] ? 0 : 1));
}

$rows = read_csv($options['archivo']);
if (empty($rows)) {
    cli_error('CSV vacío o sin filas de datos.');
}

cli_writeln('Filas a procesar: ' . count($rows));
$created = ['course' => 0, 'resource' => 0];

foreach ($rows as $row) {
    try {
        $courseid = get_or_create_course($row, $options['categoria']);
        $sectionnum = (int)$row['unidad_n'];
        ensure_section($courseid, $sectionnum, $row['unidad_nombre']);

        if ($options['dryrun']) {
            cli_writeln("[DRYRUN] $row[modulo_shortname] / Unidade $sectionnum / $row[tipo_recurso] / $row[nombre_recurso]");
            continue;
        }

        add_resource($row, $courseid, $sectionnum);
        $created['resource']++;
    } catch (Exception $e) {
        cli_writeln("ERROR en fila [$row[modulo_shortname] / $row[nombre_recurso]]: " . $e->getMessage());
    }
}

if (!$options['dryrun']) {
    purge_all_caches();
}
cli_writeln('Proceso completado. Cursos: ' . $created['course'] . ', recursos: ' . $created['resource']);

/** Lee el CSV separado por ';' con cabecera obligatoria. */
function read_csv(string $path): array {
    $rows = [];
    $handle = fopen($path, 'r');
    if ($handle === false) {
        cli_error('No se pudo abrir: ' . $path);
    }
    $header = null;
    while (($data = fgetcsv($handle, 0, ';')) !== false) {
        if (is_null($data[0]) || (count($data) === 1 && $data[0] === '')) {
            continue;
        }
        if ($header === null) {
            $header = array_map(fn($h) => str_replace("\xEF\xBB\xBF", '', trim($h)), $data);
            continue;
        }
        $row = [];
        foreach ($header as $i => $col) {
            $row[$col] = $data[$i] ?? '';
        }
        $rows[] = $row;
    }
    fclose($handle);
    return $rows;
}

/** Devuelve el id de la categoría por nombre (la crea si falta). */
function get_category_id(string $name): int {
    global $DB;
    if ($cat = $DB->get_record('course_categories', ['name' => $name, 'parent' => 0])) {
        return (int)$cat->id;
    }
    $cat = \core_course_category::create((object)[
        'name' => $name,
        'parent' => 0,
        'visible' => 1,
    ]);
    return (int)$cat->id;
}

/** Crea (o recupera) el curso por shortname. */
function get_or_create_course(array $row, string $categoryname): int {
    global $DB;
    $shortname = $row['modulo_shortname'];
    if ($course = $DB->get_record('course', ['shortname' => $shortname])) {
        return (int)$course->id;
    }

    $categoryid = get_category_id($categoryname);
    $data = (object)[
        'category'      => $categoryid,
        'fullname'      => $row['modulo_fullname'],
        'shortname'     => $shortname,
        'summary'       => $row['modulo_summary'],
        'summaryformat' => FORMAT_HTML,
        'format'        => 'topics',
    ];
    $course = create_course($data);
    return (int)$course->id;
}

/** Garantiza que la sección numérica exista y tenga el nombre de la unidad. */
function ensure_section(int $courseid, int $sectionnum, string $sectionname): void {
    global $DB;
    $course = $DB->get_record('course', ['id' => $courseid]);
    course_create_sections_if_missing($course, $sectionnum);
    $section = $DB->get_record('course_sections', ['course' => $courseid, 'section' => $sectionnum]);
    if (!empty($sectionname) && $section && empty($section->name)) {
        $section->name = $sectionname;
        $DB->update_record('course_sections', $section);
    }
}

/** Inserta el recurso correspondiente a tipo_recurso. */
function add_resource(array $row, int $courseid, int $sectionnum): void {
    global $CFG;
    $type = $row['tipo_recurso'];

    if ($type === 'video_embed' || $type === 'video_dropbox') {
        add_url_resource($row, $courseid, $sectionnum, $type === 'video_embed');
        return;
    }

    if (in_array($type, ['audio_mp3', 'doc_pdf', 'doc_docx', 'doc_txt'], true)) {
        add_file_resource($row, $courseid, $sectionnum);
        return;
    }

    throw new Exception('tipo_recurso no válido: ' . $type);
}

/** Recursos URL: video embed (YouTube) o video descarga (Dropbox). */
function add_url_resource(array $row, int $courseid, int $sectionnum, bool $embed): void {
    $mod = new stdClass();
    $mod->modulename    = 'url';
    $mod->course        = $courseid;
    $mod->section       = $sectionnum;
    $mod->name          = $row['nombre_recurso'];
    $mod->externalurl   = $embed ? $row['url_embed'] : $row['url_dropbox'];
    $mod->display       = $embed ? 1 : 3; // embed | nueva ventana
    $mod->showdescription = 1;
    $mod->intro         = $row['descripcion'];
    $mod->introformat   = FORMAT_HTML;
    create_module($mod);
}

/** Recursos Archivo: audio MP3 y documentos (PDF/DOCX/TXT). */
function add_file_resource(array $row, int $courseid, int $sectionnum): void {
    global $DB;
    $source = $row['archivo_relativo'];
    if (!file_exists($source)) {
        throw new Exception('Archivo no encontrado: ' . $source);
    }

    $mod = new stdClass();
    $mod->modulename    = 'resource';
    $mod->course        = $courseid;
    $mod->section       = $sectionnum;
    $mod->name          = $row['nombre_recurso'];
    $mod->showdescription = 1;
    $mod->intro         = $row['descripcion'];
    $mod->introformat   = FORMAT_HTML;
    $mod->display       = $row['tipo_recurso'] === 'doc_docx' ? 1 : 0; // docx: fuerzo descarga
    $cm = create_module($mod);

    $context = context_module::instance($cm->id);
    $fs = get_file_storage();
    $filerecord = (object)[
        'contextid'    => $context->id,
        'component'    => 'mod_resource',
        'filearea'     => 'content',
        'itemid'       => 0,
        'filepath'     => '/',
        'filename'     => basename($source),
        'timecreated'  => time(),
        'timemodified' => time(),
    ];
    $fs->create_file_from_pathname($filerecord, $source);
}