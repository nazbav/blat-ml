<?php

function heeader($page = 'academicol') {
    if($page == 'academicol'){
        $styles = 'style.css';
        $abbriv = 'СПО';
        $link = 'http://academicol.ru/students/schedule/';
    } else {
        $styles = 'style2.css';
        $abbriv = 'ВО';
        $link = 'http://volbi.ru/glavnaya/raspisanie-zanyatiy/';
    }
    echo '<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://use.fontawesome.com/00136352b6.js"></script>
    <script src="main.js"></script>
	<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
    <meta name="viewport" content="width=device-width, initial-scale=0.9">
        <link type="text/css" rel="stylesheet" href="http://' . $_SERVER['SERVER_NAME'] . '/'.$styles.'">
</head>
<div class="header"><a href="index.php?reload" class="reload col col-sm-1"><i class="fa fa-refresh" aria-hidden="true"></i></a>
    <a href="'.$link.'" class="logotype col col-sm-11">РАСПИ <span class="brand">САНИЕ</span> '.$abbriv.'</a>
</div>
<div class="body"><br/>';
}
function footer($page = 'academicol') {
    if($page == 'academicol'){
        echo "</div><a class=\"header\" href=\"index.php?p=volbi\">Расписание ВИБ</a>
<script language='JavaScript'>
$(document).ready(function() {
    $('table:eq(-1)').prepend($('<tr>').append($('<th>').html(\"РАСПИСАНИЕ ЗВОНКОВ\")));
});
</script>";
    } else {
        echo "</div><a class=\"header\" href=\"index.php?p=academicol\">Расписание АК</a>";
    }
}

const PAGE = 'http://academicol.ru/students/schedule/';
const MAIN_BLOCK = '<h1>Расписание занятий</h1>';
const AFTER_BLOCK = 'Информационные ресурсы';
$content = file_get_contents(PAGE);
$main = explode(MAIN_BLOCK, $content);
$main = explode(AFTER_BLOCK, $main[1]);
$content = strip_tags($main[0], '<a><sup><tbody><td><tr><th><table>');
$patterns = [
 '/<([b-z][a-z0-9]*)[^>]*?(\/?)>/i',
 '/\/upload/',
 '/<.{1,2}>\s+<\/.{1,2}>/u',
 '/<table>/',
 '/\s+/',
 '/(Очная форма обучения:|Заочная форма обучения:)/',
 '~([0-9]+)\.([0-9]+)\<sup~u',
 '/<td> ([0-9]+) (пара) <\/td>/u'
 ];
$replacements = [
 '<$1$2>',
 'https://docs.google.com/viewer?url=http://academicol.ru/upload',
 '',
 '<table class="table table-dark">',
 ' ',
 '<h3>$1</h3>',
 '$1<sup',
 ''
 ];
$content = preg_replace($patterns, $replacements, $content);

ob_start();
heeader();
echo strtr($content,['&nbsp;'=>'']);
footer();
$end = ob_get_clean();
file_put_contents('academicol.php',$end);

/////////////////////////////////////////////////
/////////////////////////////////////////////////
/////////////////////////////////////////////////
/////////////////////////////////////////////////
/////////////////////////////////////////////////
/////////////////////////////////////////////////
/////////////////////////////////////////////////

const PAGE2 = 'http://volbi.ru/glavnaya/raspisanie-zanyatiy/';
const MAIN_BLOCK2 = '<h1>Расписание занятий</h1>';
const AFTER_BLOCK2 = '<div id="footer">';

$content = file_get_contents(PAGE2);
$main = explode(MAIN_BLOCK2, $content);
$main = explode(AFTER_BLOCK2, $main[1]);
$content = strip_tags($main[0], '<a><sup><tbody><td><tr><th><table>');
$patterns = [
    '/<([b-z][a-z0-9]*)[^>]*?(\/?)>/i',
    '/\/files/',
    '/(bgcolor=|width=|nowrap)|style=\"(.*)\"|(<td>\s+<\/td>)|УРОВЕНЬ ВО/ui',
    '/<table>/',
    '/\s+/',
    '/(Очная форма обучения:|Очно - заочная форма обучения:|Заочная форма обучения:)/',
    '/([0-9]+)\.([0-9]+)\<sup/',
    '/<td> ([0-9]+) (пара) <\/td>/u'
];
$replacements = [
    '<$1$2>',
    'https://docs.google.com/viewer?url=http://volbi.ru/files',
    '',
    '<table class="table table-dark">',
    ' ',
    '<h3>$1</h3>',
    '$1<sup',
    ''
];
$content = preg_replace($patterns, $replacements, $content);

ob_start();
heeader('volbi');
echo strtr($content,['&nbsp;'=>'']);
footer('volbi');
$end = ob_get_clean();
file_put_contents('volbi.php',$end);
