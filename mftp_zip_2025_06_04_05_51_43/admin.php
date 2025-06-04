<?php
session_start();

if (!isset($_SESSION["kasutaja"])) {
    header("Location: login2.php");
    exit;
}

$onAdmin = isset($_SESSION["onadmin"]) && $_SESSION["onadmin"] == "1";

require_once('zoneconf.php');
$teade = "";

// Только админ может удалять
if ($onAdmin && isset($_GET["delete"])) {
    $id = intval($_GET["delete"]);
    if ($conn->query("DELETE FROM politseinik WHERE id = $id")) {
        $teade = "<p style='color:green;'>Politseinik kustutatud.</p>";
    } else {
        $teade = "<p style='color:red;'>Viga kustutamisel: " . $conn->error . "</p>";
    }
}

// Только админ может добавлять
if ($onAdmin && $_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["lisa"])) {
    $nimi = $_POST["nimi"];
    $pnimi = $_POST["pnimi"];
    $auaste = $_POST["auaste"];
    $isikukood = $_POST["isikukood"];
    $osakond_id = intval($_POST["osakond_id"]);

    $stmt = $conn->prepare("INSERT INTO politseinik (nimi, pnimi, auaste, isikukood, osakond_id) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssi", $nimi, $pnimi, $auaste, $isikukood, $osakond_id);
    if ($stmt->execute()) {
        $teade = "<p style='color:green;'>Politseinik lisatud edukalt!</p>";
    } else {
        $teade = "<p style='color:red;'>Viga lisamisel: " . $stmt->error . "</p>";
    }
    $stmt->close();
}

$osakonnad = $conn->query("SELECT id, nimi FROM politseiosakond");

$politseinikud = $conn->query("SELECT p.id, p.nimi, p.pnimi, p.auaste, p.isikukood, o.nimi AS osakond
FROM politseinik p
LEFT JOIN politseiosakond o ON p.osakond_id = o.id");
?>

<!DOCTYPE html>
<html lang="et">
<head>
    <meta charset="UTF-8">
    <title>Politseinike haldus</title>
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
<h2>Politseinike haldus <?= $onAdmin ? "(admin)" : "(vaatamine)" ?></h2>

<?= $teade ?>

<table>
    <tr>
        <th>Eesnimi</th>
        <th>Perekonnanimi</th>
        <th>Auaste</th>
        <th>Isikukood</th>
        <th>Osakond</th>
        <?php if ($onAdmin): ?>
            <th>Tegevus</th>
        <?php endif; ?>
    </tr>
    <?php while ($r = $politseinikud->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($r["nimi"]) ?></td>
            <td><?= htmlspecialchars($r["pnimi"]) ?></td>
            <td><?= htmlspecialchars($r["auaste"]) ?></td>
            <td><?= htmlspecialchars($r["isikukood"]) ?></td>
            <td><?= htmlspecialchars($r["osakond"]) ?></td>
            <?php if ($onAdmin): ?>
                <td><a href="?delete=<?= $r["id"] ?>" onclick="return confirm('Kustuta?')">Kustuta</a></td>
            <?php endif; ?>
        </tr>
    <?php endwhile; ?>
</table>

<?php if ($onAdmin): ?>
    <h3>Lisa uus politseinik</h3>
    <form method="post">
        Eesnimi: <input type="text" name="nimi" required><br>
        Perekonnanimi: <input type="text" name="pnimi" required><br>
        Auaste: <input type="text" name="auaste"><br>
        Isikukood: <input type="text" name="isikukood" maxlength="11" pattern="\d{11}" title="Täpselt 11 numbrit" required><br>
        Osakond:
        <select name="osakond_id">
            <?php while ($o = $osakonnad->fetch_assoc()): ?>
                <option value="<?= $o["id"] ?>"><?= htmlspecialchars($o["nimi"]) ?></option>
            <?php endwhile; ?>
        </select><br><br>
        <input type="submit" name="lisa" value="Lisa">
    </form>
<?php else: ?>
    
<?php endif; ?>


<div class="page-bottom-spacer"></div>
<img src="politseifoto1.png" alt="Photo" class="bottom-image">

</body>
</html>
