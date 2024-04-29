<?php

    $hostname = 'localhost';
    $username = 'root';
    $password = '';
    $dbname   = 'jamur';

    $conn = mysqli_connect($hostname, $username, $password, $dbname) or die ('Gagal terhubung DB')
?>