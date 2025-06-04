<?php
require_once("abifunktsioonid.php");
session_start();

// Разрешаем только вошедшим пользователям с ролью "kasutaja"
if (!isset($_SESSION["onadmin"]) || $_SESSION["onadmin"] != 1) {
    header("Location: login2.php");
    exit;
}

// Получаем список полицейских и их отделов
global $yhendus;
$kask = $yhendus->prepare("
    SELECT p.nimi, p.pnimi, p.auaste, p.isikukood, o.nimi AS osakond
    FROM politseinik p
    LEFT JOIN politseiosakond o ON p.osakond_id = o.id
");
$kask->execute();
$kask->bind_result($nimi, $pnimi, $auaste, $isikukood, $osakond);
?>

<!DOCTYPE html>
<html lang="et">
<head>
    <meta charset="UTF-8">
    <title>Politseinike vaade</title>
</head>
<body>
<button onclick="location.href='welcome.php'">← Avaleht</button>
<header class="topbar">
    <div class="header-left">
        <h1>Politsei Infosüsteem</h1>
    </div>
    <div class="header-right">
        <img src="policelogo.png" alt="Politsei logo">
    </div>
</header>
<h2>Politseinike nimekiri</h2>
<link rel="stylesheet" href="politsei.css">
<table>
    <tr>
        <th>Eesnimi</th>
        <th>Perekonnanimi</th>
        <th>Auaste</th>
        <th>Isikukood</th>
        <th>Osakond</th>
    </tr>

    <?php while ($kask->fetch()): ?>
        <tr>
            <td><?= htmlspecialchars($nimi) ?></td>
            <td><?= htmlspecialchars($pnimi) ?></td>
            <td><?= htmlspecialchars($auaste) ?></td>
            <td><?= htmlspecialchars($isikukood) ?></td>
            <td><?= htmlspecialchars($osakond) ?></td>
        </tr>
    <?php endwhile; ?>

</table>

<div class="page-bottom-spacer"></div>
<img src="politseifoto1.png" alt="Photo" class="bottom-image">

</body>
</html>
