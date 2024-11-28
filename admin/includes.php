<?php
session_start();
error_reporting(0);

date_default_timezone_set($GLOBALS['timezone']);

include($_SERVER["DOCUMENT_ROOT"] . "/" . ADMIN_PANEL . "/engine/db.php");
include($_SERVER["DOCUMENT_ROOT"] . "/" . ADMIN_PANEL . "/engine/CMain.php");
include($_SERVER["DOCUMENT_ROOT"] . "/" . ADMIN_PANEL . "/engine/CRouter.php");
include($_SERVER["DOCUMENT_ROOT"] . "/" . ADMIN_PANEL . "/engine/CDb.php");
include($_SERVER["DOCUMENT_ROOT"] . "/" . ADMIN_PANEL . "/engine/functions.php");
include($_SERVER["DOCUMENT_ROOT"] . "/" . ADMIN_PANEL . "/engine/User.php");

$Db = new CDb();
$Router = new CRouter();
$Main = new CMain();

$User = new User();

$GLOBALS['site_settings'] = $Main->GetDefineSettings();
$GLOBALS['auth_fields'] = array('email');

if (substr_count($_SERVER['REQUEST_URI'], "/" . ADMIN_PANEL . "/") > 0) {
    include($_SERVER["DOCUMENT_ROOT"] . "/" . ADMIN_PANEL . "/engine/CAdmin.php");
    $Admin = new CAdmin();
    $Router->lang = $Admin->lang;
}
?>