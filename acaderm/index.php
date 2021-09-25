<?php
ini_set('session.gc_maxlifetime', 2592000);
ini_set('session.cookie_lifetime', 0);
session_set_cookie_params(0);
session_start();
if (isset($_GET['reload'])) {
    include 'cron.php';
    header('location:index.php');
}

if (isset($_GET['p'])) {
    if ($_GET['p'] == 'volbi') {
        $_SESSION['p'] = 'volbi';
        header('location:index.php');
    } else {
        $_SESSION['p'] = 'academicol';
        header('location:index.php');
    }
}
if (isset($_SESSION['p'])) {
    if ($_SESSION['p'] == 'volbi') {
        include_once 'volbi.php';
    } else {
        include_once 'academicol.php';
    }
} else {
    include_once 'academicol.php';
}
