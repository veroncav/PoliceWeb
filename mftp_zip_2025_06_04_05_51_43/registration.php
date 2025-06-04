<?php
session_start();
require('zoneconf.php');


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $kasutaja = $_POST['kasutajanimi'];  // здесь исправлено с 'kasutaja' на 'kasutajanimi'
    $parool = $_POST['parool'];
    $onadmin = '0'; // фиксировано

    // Проверка пользователя
    $stmt = $conn->prepare("SELECT id FROM kasutajad1 WHERE kasutaja = ?");
    $stmt->bind_param("s", $kasutaja);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo "Kasutaja on juba võetud.";
    } else {
        $hash = password_hash($parool, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO kasutajad1 (kasutaja, parool, onadmin) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $kasutaja, $hash, $onadmin);
        if ($stmt->execute()) {
            echo "Registreerimine õnnestus! <a href='login2.php'>Logi sisse</a>";
        } else {
            echo "Viga registreerimisel: " . $stmt->error;
        }
    }

    $stmt->close();
    $conn->close();
}

?>
<header class="topbar">
    <div class="header-left">
        <h1>Politsei Infosüsteem</h1>
    </div>
    <div class="header-right">
        <img src="policelogo.png" alt="Politsei logo">
    </div>
</header>
<h2>Registreerimine</h2>
<link rel="stylesheet" href="politsei.css">
<form method="post">
    Kasutajanimi: <input type="text" name="kasutajanimi" required><br>
    Parool: <input type="password" name="parool" required><br>
    <input type="submit" value="Registreeri">
    <button onclick="location.href='login2.php'">← Tagasi</button>

    <div class="page-bottom-spacer"></div>
<img src="politseifoto1.png" alt="Photo" class="bottom-image">

</form>
