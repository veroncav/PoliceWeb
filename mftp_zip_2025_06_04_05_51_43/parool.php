<?php
$parool = '12345';
$krypt = password_hash($parool, PASSWORD_DEFAULT);
echo $krypt;