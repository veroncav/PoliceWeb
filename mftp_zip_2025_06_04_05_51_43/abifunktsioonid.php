<?php
require_once('zoneconf.php');

// Функция для получения данных о преступлениях
function kysiKuritegevus($sorttulp = "toimus", $otsisona = '') {
    global $yhendus;
    $lubatudtulbad = array("toimus", "liik", "piirkond");

    if (!in_array($sorttulp, $lubatudtulbad)) {
        return "lubamatu tulp";
    }

    $otsisona = "%" . addslashes(stripslashes($otsisona)) . "%";
    $kask = $yhendus->prepare("SELECT id, toimus, liik, piirkond FROM kuritegevus
        WHERE toimus LIKE ? OR liik LIKE ? OR piirkond LIKE ?
        ORDER BY $sorttulp");

    $kask->bind_param("sss", $otsisona, $otsisona, $otsisona);
    $kask->bind_result($id, $toimus, $liik, $piirkond);
    $kask->execute();

    $hoidla = array();
    while ($kask->fetch()) {
        $r = new stdClass();
        $r->id = $id;
        $r->toimus = htmlspecialchars($toimus);
        $r->liik = htmlspecialchars($liik);
        $r->piirkond = htmlspecialchars($piirkond);
        $hoidla[] = $r;
    }
    return $hoidla;
}

// Функции добавления, удаления и изменения можно добавить по желанию
