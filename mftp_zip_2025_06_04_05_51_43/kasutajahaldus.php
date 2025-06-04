<?php
require_once("abifunktsioonid.php");
session_start();

if (!isset($_SESSION["kasutaja"])) {
    header("Location: login2.php");
    exit;
}

$teade = "";

// Ainult adminile lubatud tegevused
if (isset($_SESSION["onadmin"]) && $_SESSION["onadmin"] == "1") {

    // Määrame admini rolli
    if (isset($_GET["teeadmin"])) {
        $id = intval($_GET["teeadmin"]);
        $stmt = $yhendus->prepare("UPDATE kasutajad1 SET onadmin='1' WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
        $teade = "Kasutajale määrati admini roll.";
    }

    // Eemaldame admini rolli
    if (isset($_GET["eemaldaadmin"])) {
        $id = intval($_GET["eemaldaadmin"]);
        $stmt = $yhendus->prepare("UPDATE kasutajad1 SET onadmin='0' WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
        $teade = "Admini roll eemaldati.";
    }
}

// Kuvame kõik kasutajad
$kasutajad1 = $yhendus->query("SELECT id, kasutaja, onadmin FROM kasutajad1");
?>

<!DOCTYPE html>
<html lang="et">
<head>
    <meta charset="UTF-8">
    <title>Kasutajate haldus</title>
    <link rel="stylesheet" href="politsei.css">
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

<h2>Kasutajate haldus</h2>

<?php if (!empty($teade)): ?>
    <p style="color:green;"><?= htmlspecialchars($teade) ?></p>
<?php endif; ?>

<table>
    <tr>
        <th>Kasutajanimi</th>
        <th>Roll</th>
        <th>Muuda rolli</th>
    </tr>

    <?php while ($r = $kasutajad1->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($r["kasutaja"]) ?></td>
            <td><?= $r["onadmin"] == "1" ? "Admin" : "Kasutaja" ?></td>
            <td>
                <?php if ($_SESSION["onadmin"] == "1"): ?>
                    <?php if ($r["onadmin"] === "0"): ?>
                        <a href="?teeadmin=<?= $r["id"] ?>">Määra admin</a>
                    <?php elseif ($r["onadmin"] === "1" && $r["kasutaja"] !== $_SESSION["kasutaja"]): ?>
                        <a href="?eemaldaadmin=<?= $r["id"] ?>">Eemalda admin</a>
                    <?php else: ?>
                        -
                    <?php endif; ?>
                <?php else: ?>
                    -
                <?php endif; ?>
            </td>
        </tr>
    <?php endwhile; ?>
</table>

<div class="page-bottom-spacer"></div>
<img src="politseifoto1.png" alt="Photo" class="bottom-image">

</body>
</html>
