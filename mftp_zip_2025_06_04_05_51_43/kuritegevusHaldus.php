<?php
require_once("abifunktsioonid.php");
session_start();

$teade = "";
if (isset($_SESSION["teade"])) {
    $teade = $_SESSION["teade"];
    unset($_SESSION["teade"]);
}

if (!isset($_SESSION["onadmin"])) {
    header("Location: login2.php");
    exit;
}

$kas_on_admin = $_SESSION["onadmin"] == "1";

// Kustutamine
if ($kas_on_admin && isset($_GET["delete"])) {
    $id = intval($_GET["delete"]);
    $yhendus->query("DELETE FROM kuriteo_kurjategija WHERE kuritegevus_id=$id");
    $yhendus->query("DELETE FROM kuritegevus WHERE id=$id");
    $teade = "<p style='color:green;'>Kuritegu kustutatud.</p>";
}

// Uuendamine
if ($kas_on_admin && isset($_POST["muuda_kuritegu"])) {
    $id = intval($_POST["muuda_id"]);
    $tyyp = $_POST["kuriteg_tyyp"];
    $kirjeldus = $_POST["kirjeldus"];
    $kuupaev = $_POST["kuupaev"];
    $asukoht = $_POST["asukoht"];
    $politseinik_id = intval($_POST["politseinik_id"]);
    $kurjategija_id = intval($_POST["kurjategija_id"]);

    $stmt = $yhendus->prepare("UPDATE kuritegevus SET kuriteg_tyyp=?, kirjeldus=?, kuupaev=?, asukoht=?, politseinik_id=? WHERE id=?");
    $stmt->bind_param("ssssii", $tyyp, $kirjeldus, $kuupaev, $asukoht, $politseinik_id, $id);
    $stmt->execute();
    $stmt->close();

    $yhendus->query("DELETE FROM kuriteo_kurjategija WHERE kuritegevus_id=$id");
    $stmt2 = $yhendus->prepare("INSERT INTO kuriteo_kurjategija (kuritegevus_id, kurjategija_id) VALUES (?, ?)");
    $stmt2->bind_param("ii", $id, $kurjategija_id);
    $stmt2->execute();
    $stmt2->close();

    $_SESSION["teade"] = "<p style='color:green;'>Kuritegu muudetud edukalt!</p>";
    header("Location: kuritegevusHaldus.php");
    exit;
}

// Lisamine
if ($kas_on_admin && $_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["lisa"])) {
    if (
        (empty($_POST["kurjategija_id"]) || $_POST["kurjategija_id"] === "") &&
        empty(trim($_POST["uus_kurjategija"]))
    ) {
        $teade = "<p style='color:red;'>Vali kurjategija või sisesta uus nimi!</p>";
    } else {
        $tyyp = $_POST["kuriteg_tyyp"];
        $kirjeldus = $_POST["kirjeldus"];
        $kuupaev = $_POST["kuupaev"];
        $asukoht = $_POST["asukoht"];
        $politseinik_id = intval($_POST["politseinik_id"]);

        if (!empty($_POST["uus_kurjategija"])) {
            $nimi = trim($_POST["uus_kurjategija"]);
            $stmt3 = $yhendus->prepare("INSERT INTO kurjategija (nimi, pnimi) VALUES (?, '')");
            $stmt3->bind_param("s", $nimi);
            $stmt3->execute();
            $kurjategija_id = $stmt3->insert_id;
            $stmt3->close();
        } else {
            $kurjategija_id = intval($_POST["kurjategija_id"]);
        }

        $stmt = $yhendus->prepare("INSERT INTO kuritegevus (kuriteg_tyyp, kirjeldus, kuupaev, asukoht, politseinik_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssi", $tyyp, $kirjeldus, $kuupaev, $asukoht, $politseinik_id);
        $stmt->execute();
        $new_id = $stmt->insert_id;
        $stmt->close();

        $stmt2 = $yhendus->prepare("INSERT INTO kuriteo_kurjategija (kuritegevus_id, kurjategija_id) VALUES (?, ?)");
        $stmt2->bind_param("ii", $new_id, $kurjategija_id);
        $stmt2->execute();
        $stmt2->close();

        $_SESSION["teade"] = "<p style='color:green;'>Kuritegu lisatud edukalt!</p>";
        header("Location: kuritegevusHaldus.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="et">
<head>
    <meta charset="UTF-8">
    <title>Kuritegude haldus</title>
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
<h2>Kuritegude haldus</h2>
<?= $teade ?>
<table>
    <tr>
        <th>Tüüp</th><th>Kirjeldus</th><th>Kuupäev</th><th>Asukoht</th><th>Politseinik</th><th>Kurjategija</th><th>Muuda</th><th>Kustuta</th>
    </tr>
    <?php
    $kask = $yhendus->query("SELECT k.id, k.kuriteg_tyyp, k.kirjeldus, k.kuupaev, k.asukoht, k.politseinik_id,
                                    p.nimi AS politseinik_nimi, p.pnimi AS politseinik_pnimi,
                                    kg.id AS kurjategija_id, GROUP_CONCAT(CONCAT(kg.nimi, ' ', kg.pnimi) SEPARATOR ', ') AS kurjategijad
                            FROM kuritegevus k
                            LEFT JOIN politseinik p ON k.politseinik_id = p.id
                            LEFT JOIN kuriteo_kurjategija kk ON k.id = kk.kuritegevus_id
                            LEFT JOIN kurjategija kg ON kk.kurjategija_id = kg.id
                            GROUP BY k.id");
    while ($r = $kask->fetch_assoc()): ?>
    <tr>
        <?php if ($kas_on_admin && isset($_GET["edit"]) && $_GET["edit"] == $r["id"]): ?>
        <form method="post">
            <input type="hidden" name="muuda_id" value="<?= $r["id"] ?>">
            <td><input type="text" name="kuriteg_tyyp" value="<?= htmlspecialchars($r["kuriteg_tyyp"]) ?>"></td>
            <td><input type="text" name="kirjeldus" value="<?= htmlspecialchars($r["kirjeldus"]) ?>"></td>
            <td><input type="date" name="kuupaev" value="<?= $r["kuupaev"] ?>"></td>
            <td><input type="text" name="asukoht" value="<?= htmlspecialchars($r["asukoht"]) ?>"></td>
            <td>
                <select name="politseinik_id">
                    <?php
                    $politseinikud = $yhendus->query("SELECT id, nimi, pnimi FROM politseinik");
                    while ($p = $politseinikud->fetch_assoc()) {
                        $sel = ($p["id"] == $r["politseinik_id"]) ? "selected" : "";
                        echo "<option value='{$p["id"]}' $sel>{$p["nimi"]} {$p["pnimi"]}</option>";
                    }
                    ?>
                </select>
            </td>
            <td>
                <select name="kurjategija_id">
                    <?php
                    $kurjategijad = $yhendus->query("SELECT id, nimi, pnimi FROM kurjategija");
                    while ($k = $kurjategijad->fetch_assoc()) {
                        $sel = ($k["id"] == $r["kurjategija_id"]) ? "selected" : "";
                        echo "<option value='{$k["id"]}' $sel>{$k["nimi"]} {$k["pnimi"]}</option>";
                    }
                    ?>
                </select>
            </td>
            <td colspan="2">
                <button type="submit" name="muuda_kuritegu">💾 Salvesta</button>
                <a href="kuritegevusHaldus.php">✖ Loobu</a>
            </td>
        </form>
        <?php else: ?>
        <td><?= htmlspecialchars($r["kuriteg_tyyp"]) ?></td>
        <td><?= htmlspecialchars($r["kirjeldus"]) ?></td>
        <td><?= htmlspecialchars($r["kuupaev"]) ?></td>
        <td><?= htmlspecialchars($r["asukoht"]) ?></td>
        <td><?= htmlspecialchars($r["politseinik_nimi"] . " " . $r["politseinik_pnimi"]) ?></td>
        <td><?= htmlspecialchars($r["kurjategijad"]) ?></td>
        <td><a href="?edit=<?= $r["id"] ?>">Muuda</a></td>
        <td><a href="?delete=<?= $r["id"] ?>" onclick="return confirm('Kustutada?')">Kustuta</a></td>
        <?php endif; ?>
    </tr>
    <?php endwhile; ?>
</table>

<?php if ($kas_on_admin): ?>
<h3>Lisa uus kuritegu</h3>
<form method="post">
    Kuriteo tüüp: <input type="text" name="kuriteg_tyyp" required><br>
    Kirjeldus: <textarea name="kirjeldus" required></textarea><br>
    Kuupäev: <input type="date" name="kuupaev" required><br>
    Asukoht: <input type="text" name="asukoht"><br>
    Politseinik:
    <select name="politseinik_id">
        <?php
        $politseinikud = $yhendus->query("SELECT id, nimi, pnimi FROM politseinik");
        while ($r = $politseinikud->fetch_assoc()) {
            echo "<option value='{$r["id"]}'>" . htmlspecialchars($r["nimi"]) . " " . htmlspecialchars($r["pnimi"]) . "</option>";
        }
        ?>
    </select><br>
    Kurjategija:
    <select name="kurjategija_id">
        <option value="">Vali olemasolev</option>
        <?php
        $kurjategijad = $yhendus->query("SELECT id, nimi, pnimi FROM kurjategija");
        while ($r = $kurjategijad->fetch_assoc()) {
            echo "<option value='{$r["id"]}'>" . htmlspecialchars($r["nimi"]) . " " . htmlspecialchars($r["pnimi"]) . "</option>";
        }
        ?>
    </select><br>
    Või sisesta uus kurjategija nimi: <input type="text" name="uus_kurjategija"><br><br>
    <input type="submit" name="lisa" value="Lisa kuritegu">
</form>
<?php endif; ?>

<div class="page-bottom-spacer"></div>
<img src="politseifoto1.png" alt="Photo" class="bottom-image">
</body>
</html>

