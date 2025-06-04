<?php
$kasutaja="d133861_veronica";
$parool="Zxcvbnm1223333333fsSD";
$andmebaas="d133861_veronica";
$serverinimi="localhost";
$conn = new mysqli($serverinimi, $kasutaja, $parool, $andmebaas);
$conn->set_charset("utf8");