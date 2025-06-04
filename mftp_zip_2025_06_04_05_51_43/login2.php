<?php
ob_start();
session_start();
require_once('zoneconf.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $kasutaja = $_POST["kasutaja"];
    $parool = $_POST["parool"];

    $stmt = $conn->prepare("SELECT id, parool, onadmin FROM kasutajad1 WHERE kasutaja = ?");
    $stmt->bind_param("s", $kasutaja);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 1) {
        $stmt->bind_result($id, $hashedPassword, $onadmin);
        $stmt->fetch();

    if (password_verify($parool, $hashedPassword)) {
        $_SESSION["id"] = $id;
        $_SESSION["kasutaja"] = $kasutaja;
        $_SESSION["onadmin"] = $onadmin;

        header("Location: welcome.php");
        exit;
        } else {
            $teade = "Vale parool.";
        }
    } else {
        $teade = "Kasutajat ei leitud.";
    }

    $stmt->close();
    $conn->close();
}
?>


<!DOCTYPE html>
<html lang="et">
<head>
    <meta charset="UTF-8">
    <title>Logi sisse</title>
    <link rel="stylesheet" href="politsei.css">
</head>
<body>
<button onclick="location.href='welcome.php'">← Avaleht</button>
<header class="topbar">
    
    <div class="header-left">
        <h1>Politsei Infosüsteem</h1>
    </div>
    <div class="header-right">
        <img src="policelogo.png" alt="Politsei logo" style="height:50px;">
    </div>

</header>

<h2>Logi sisse</h2>
<?php if (!empty($teade)): ?>
    <p style="color:red;"><?= $teade ?></p>
<?php endif; ?>

<form method="post">
    Kasutaja: <input type="text" name="kasutaja" required><br>
    Parool: <input type="password" name="parool" required><br>
    <input type="submit" value="Logi sisse">
    <button type="button" onclick="location.href='registration.php'">Registreeru</button>
    <button type="button" onclick="window.location.href='https://github.com/veroncav/PoliceWeb'">GitHub</button>

</a>
</form>
<div class="page-bottom-spacer"></div>
<img src="politseifoto1.png" alt="Photo" class="bottom-image">

</body>
</html>
