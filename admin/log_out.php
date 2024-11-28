<?php
include $_SERVER['DOCUMENT_ROOT'] . '/config.php';
session_start();
unset($_SESSION['admin']);
header('Location:/'.ADMIN_PANEL.'/auth.php');
?>