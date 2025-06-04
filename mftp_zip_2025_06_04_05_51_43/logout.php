<?php
session_start();
if (!isset($_SESSION['tuvastamine'])) {
    header('Location: login2.php');
    exit();
}
if(isset($_POST['logout'])){
    session_destroy();
    header('Location: admin.php');
    exit();
}
?>