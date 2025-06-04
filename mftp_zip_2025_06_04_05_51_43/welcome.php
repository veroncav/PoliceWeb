<?php
session_start();

if (!isset($_SESSION["kasutaja"])) {
    header("Location: login2.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="et">
<head>
    <meta charset="UTF-8">
    <title>Tere tulemast</title>
    <link rel="stylesheet" href="politsei.css">
</head>
<body>
<header class="topbar">
    <div class="header-left">
        <h1>Politsei Infosüsteem</h1>
    </div>
    <div class="header-right">
        <img src="policelogo.png" alt="Politsei logo">
    </div>
</header>

<h2>Tere tulemast, <?= htmlspecialchars($_SESSION["kasutaja"]) ?>!</h2>


<!-- Показать все кнопки независимо от прав -->
<button onclick="location.href='admin.php'">Politseinike haldus</button>
<button onclick="location.href='kuritegevusHaldus.php'">Kuritegevuse andmed</button>
<button onclick="location.href='kasutajahaldus.php'">Kasutajate haldus</button>
<button onclick="location.href='logout.php'">Logi välja</button>

<div class="page-bottom-spacer"></div>
<img src="politseifoto1.png" alt="Photo" class="bottom-image">

</body>
</html>

