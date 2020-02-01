<?php
ini_set('date.timezone', 'Europe/Volgograd');

const PAGE = 'http://academicol.ru/students/schedule/';
const MAIN_BLOCK = '<h1>Расписание занятий</h1>';
const AFTER_BLOCK = 'Информационные ресурсы';

$ctx = stream_context_create(['http' => ['timeout' => 15]]);
$content = file_get_contents(PAGE, 0, $ctx);
$main = explode(MAIN_BLOCK, $content);
$main = explode(AFTER_BLOCK, $main[1]);
$content = strip_tags($main[0], '<a><sup><tbody><td><tr><th><table>');
if (!empty($content)) {
    $patterns = ['/\$\(\"ul\"\)\.remove\(\"\.fmenu\"\)\;/', '/<([b-z][a-z0-9]*)[^>]*?(\/?)>/i', '/\/upload((.)+\.doc)/', '/\/upload((.)+\.xls)/', '/<.{1,2}>\s+<\/.{1,2}>/u', '/<table>/', '/\s+/', '/(Очная форма обучения:|Заочная форма обучения:)/', '~([0-9]+)\.([0-9]+)\<sup~u', '/<td> ([0-9]+) (пара) <\/td>/u'];
    $replacements = ['', '<$1$2>', 'https://docs.google.com/viewer?url=http://academicol.ru/upload$1', 'http://academicol.ru/upload$1', '', '<table class="table table-dark">', ' ', '<h3>$1</h3>', '$1<sup', ''];

    $content = preg_replace($patterns, $replacements, $content);


    $grulisty = '';
    $grulisty2 = '';

    if (preg_match_all('/http\:\/\/academicol\.ru\/upload\/price\/postoynnoe_spo\/para\/(.){40,150}\.doc/', $content, $urls)) {
        $urls = array_unique($urls[0]);
        $only = false;
        $zameny = [];
        foreach ($urls as $url) {
            $url = urldecode($url);
            $moth = [1=>'января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря'];
            if (preg_match('%([0-9][0-9])_(' . implode('|', $moth) . ')_([0-9]{2,4})%ui', $url, $matches)) {
                $one = explode('_', $matches[0]);
                $time = strtotime($one[0] . '.' . array_search($one[1], $moth) . '.' . $one[2] . ' 23:59:00');
                $zameny[] = ['name' => strtr($matches[0],'_',' '), 'url' => $url, 'date' => $time];
            }
        }
        echo json_encode($zameny, JSON_UNESCAPED_UNICODE);
    }
}