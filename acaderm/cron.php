<?php
date_default_timezone_set('Europe/Volgograd');
//setlocale (LC_ALL, "RU_ru");

function heeader($page = 'academicol')
{
    if ($page == 'academicol') {
        $styles = 'style.css';
        $abbriv = 'АК';
        $link = 'http://academicol.ru/students/schedule/';
    } else {
        $styles = 'style2.css';
        $abbriv = 'ВИБ';
        $link = 'http://lk.volbi.ru/glavnaya/raspisanie-zanyatiy/';
    }
    echo '<!DOCTYPE html>
	<html lang="ru">
	<head>
    <meta charset="utf-8">
	<title>Расписание занятий</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" integrity="sha384-B0vP5xmATw1+K9KRQjQERJvTumQW0nPEzvF6L/Z6nronJ3oUOFUFpCjEUQouq2+l" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://use.fontawesome.com/00136352b6.js"></script>
	<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
    <meta name="viewport" content="width=device-width, initial-scale=0.9">
        <link type="text/css" rel="stylesheet" href="' . $styles . '">
</head>
<div class="header"><a href="index.php?reload" class="icons col col-sm-1"><i class="fa fa-refresh" aria-hidden="true"></i></a>		
    <a href="' . $link . '" class="logotype col col-sm-10 d-flex justify-content-center">РАСПИ&nbsp;<span class="brand">САНИЕ</span>&nbsp;' . $abbriv . '</a>
	<a href="' . $link . '" class="icons col col-sm-1"><i class="fa fa-external-link" aria-hidden="true"></i></a>
</div>
<div class="body"><br/>';
}

function footer($page = 'academicol')
{
    $cpr = "© <?='2019-'.date('Y').''?> <a href='https://nazbav.github.io/NAZBAV/'><b><u>NAZBAV</u></b></a>";
    if ($page == 'academicol') {
        echo "</div><br/><br/><br/>
<div>
 <p style='color: white;text-align: center'>Обновлено: <?=(is_file('last_update.txt')) ? file_get_contents('last_update.txt') : '' ?><br>{$cpr}</p>
<a class=\"footer\" href=\"index.php?p=volbi\">Расписание ВИБ</a>
</div>
<script src=\"main.js\"></script>";

    } else {
        echo "</div><br/><br/><br/>
<p style='color: white;text-align: center'>Обновлено: <?=(is_file('last_update.txt')) ? file_get_contents('last_update.txt') : '' ?><br>{$cpr}</p>
<a class=\"footer\" href=\"index.php?p=academicol\">Расписание АК</a></script>
		<script src=\"main.js\"></script></html>";
    }
}

const PAGE = 'http://academicol.ru/students/schedule/';
const MAIN_BLOCK = '<h1>Расписание занятий</h1>';
const AFTER_BLOCK = 'Информационные ресурсы';

$ctx = stream_context_create(['http' => ['timeout' => 15]]);
$content = file_get_contents(PAGE, 0, $ctx);
$main = explode(MAIN_BLOCK, $content);
$main = explode(AFTER_BLOCK, $main[1]);
$content = strip_tags($main[0], '<a><sup><tbody><td><tr><th><table>');
if (!empty($content)) {

    $content = strtr($content, ['https://docs.google.com/viewer?url=' => '']);


    $patterns = ['/\$\(\"ul\"\)\.remove\(\"\.fmenu\"\)\;/', '/<([b-z][a-z0-9]*)[^>]*?(\/?)>/i', '/http\:\/\/academicol.ru\/upload((.)+\.docx)/', '/\/upload((.)+\.xls)/', '/<.{1,2}>\s+<\/.{1,2}>/u', '/<table>/', '/\s+/', '/(Очная форма обучения:|Заочная форма обучения:)/', '~([0-9]+)\.([0-9]+)\<sup~u', '/<td> ([0-9]+) (пара) <\/td>/u', '/id=("|)black("|)/u', '~<tr> </tr>~u'];
    $replacements = ['', '<$1$2>', 'https://docs.google.com/viewer?url=http://academicol.ru/upload$1', 'https://docs.google.com/viewer?url=http://academicol.ru/upload$1', '', '<table class="table table-dark">', ' ', '<h3>$1</h3>', '$1<sup', '', '', ''];

//https://docs.google.com/viewer?url=http://academicol.ru/upload


    $content = preg_replace($patterns, $replacements, $content);


    $content = strtr($content,
        ['.zvonki__content { text-align: left!important; } .zvonki__container, .zvonki__content { width: 100%; } .tdschedule { height: 3rem; } ' => '']);

    $grulisty = '';
    $grulisty2 = '';


    if (preg_match_all('/http\:\/\/academicol\.ru\/upload\/price\/postoynnoe_spo\/para\/(.){40,150}\.docx/', $content, $urls)) {

        $urls = array_unique($urls[0]);
        include_once 'perser.php';
        $only = false;
        foreach ($urls as $url) {
            $perser = (read_doc($url));
            //  $perser = mb_convert_encoding($perser, 'utf-8', mb_detect_encoding($perser));
            $perser = strtr($perser, ["\r\n" => '', "\r" => '', "\n" => '', "	" => '', ' ' => '']);
            $groups = 'ПСО|ПД|ИСП|ТОП|БД|ГД|ПСО|ПКС|ЗИО|Б|К';
            if (preg_match_all('%((([1-5]{1})(' . $groups . ')(\-[0-9]{1,2})(\-[0-9]{1})|([1-5]{1})(' . $groups . ')(\-(11|9)))(,)(([1-5]{1})(' . $groups . ')(\-[0-9]{1,2})(\-[0-9]{1})|([1-5]{1})(' . $groups . ')(\-(11|9))))|(([1-5]{1})(' . $groups . ')(\-[0-9]{1,2})(\-[0-9]{1})|([1-5]{1})(' . $groups . ')(\-(11|9)))%', $perser, $matches)) {

                $matches[0] = array_unique($matches[0]);

                foreach ($matches[0] as $item => $group) {
                    if ($group != '') $grulisty2 .= '<span class="group">' . $group . '</span> ';
                }
                $grulisty .= '<script>
		$(document).ready(function () {
		$(\'a[href="https://docs.google.com/viewer?url=' . $url . '"]\').parent().parent().after(\'<tr class="grouplist"><td colspan="4" >' . $grulisty2 . '</td></tr>\');
		});
</script>';
                $grulisty2 = '';
            }
        }
    }
} else {
    $content = "Ошибка сервера";
}


ob_start();
heeader();
echo $grulisty;
echo strtr($content, ['&nbsp;' => '']);
footer();
$end = ob_get_clean();
file_put_contents('academicol.php', $end);

/////////////////////////////////////////////////
/////////////////////////////////////////////////
///// ///// //      //      /////////////////////
/////  //  ///// ///// //// /////////////////////
//////   ////// ////// //// /////////////////////
////// /////      ////      /////////////////////
/////////////////////////////////////////////////

const PAGE2 = 'http://lk.volbi.ru/glavnaya/raspisanie-zanyatiy/';
const MAIN_BLOCK2 = '<h1>Расписание занятий</h1>';
const AFTER_BLOCK2 = '<div id="footer">';


$ctx = stream_context_create(['http' => ['timeout' => 15]]);
$content = file_get_contents(PAGE2, 0, $ctx);

$main = explode(MAIN_BLOCK2, $content);
$main = explode(AFTER_BLOCK2, $main[1]);
$content = strip_tags($main[0], '<a><sup><tbody><td><tr><th><table>');
if (!empty($content)) {
    $patterns = ['/(УРОВЕНЬ ВО)/', '/\$\(\"ul\"\)\.remove\(\"\.fmenu\"\)\;/', '/<([b-z][a-z0-9]*)[^>]*?(\/?)>/i', '/\/files((.)+\.doc)/', '/\/files((.)+\.xls)/', '/<.{1,2}>\s+<\/.{1,2}>/u', '/<table>/', '/\s+/', '/(Очная форма обучения:|Заочная форма обучения:)/', '~([0-9]+)\.([0-9]+)\<sup~u', '/<td> ([0-9]+) (пара) <\/td>/u'];
    $replacements = ['', '', '<$1$2>', 'https://docs.google.com/viewer?url=http://lk.volbi.ru/files$1', 'http://lk.volbi.ru/files$1', '', '<table class="table table-dark">', ' ', '<h3>$1</h3>', '$1<sup', ''];
    $content = preg_replace($patterns, $replacements, $content);
} else {
    $content = "Ошибка сервера";
}
ob_start();
heeader('volbi');
echo strtr($content, ['&nbsp;' => '']);
footer('volbi');
$end = ob_get_clean();
file_put_contents('volbi.php', $end);

if (filesize('volbi.php') < 1000 || filesize('academicol.php') < 1000) {
    $text = "\n\r" . '<a href="index.php?reload" class="icons col col-sm-1"><i class="fa fa-refresh" aria-hidden="true"></i></a>';
    file_put_contents('volbi.php', $text);
    file_put_contents('academicol.php', $text);
}

file_put_contents('last_update.txt', date('d-m-Y H:i:s'));
