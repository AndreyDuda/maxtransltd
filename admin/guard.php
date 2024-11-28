<?php
session_start();
if (!isset($_SESSION['admin'])){
    header('Location:/'.ADMIN_PANEL.'/auth.php');
}
?>