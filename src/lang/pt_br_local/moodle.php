<?php
// Personalizacion de idioma pt_br (mecanismo oficial de Moodle, NO toca core):
// se anula el saludo nativo "Olá, {nombre}! 👋" (lib/classes/user.php::welcome_message(),
// strings lang/en/moodle.php welcomeback/welcometosite) porque el desarrollador pidio
// sacarlo de la Pagina Principal y reemplazarlo por un saludo propio en Mis cursos.
// String vacio => Mustache trata la seccion {{#welcomemessage}} como falsy y no
// renderiza nada (ni siquiera un div vacio) — confirmado en lib/templates/welcome.mustache.
// Se instala en $CFG->dataroot/lang/pt_br_local/moodle.php (no dentro del docroot;
// esta copia en el repo es solo referencia versionada).
$string['welcomeback'] = '';
$string['welcometosite'] = '';
