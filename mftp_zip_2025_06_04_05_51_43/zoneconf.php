<?php
$servernimi="d133861.mysql.zonevs.eu";
$kasutaja="d133861_veronica";
$parool="Zxcvbnm1223333333fsSD";
$andmebaas="d133861_veronica";

$yhendus = new mysqli($servernimi,$kasutaja,$parool,$andmebaas);
$conn = $yhendus;
$yhendus->set_charset("utf8");
if ($yhendus->connect_error) {
    die("Andmebaasiühenduse viga: " . $yhendus->connect_error);
}?>