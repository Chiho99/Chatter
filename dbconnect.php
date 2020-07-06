<?php
    $dsn = 'mysql:dbname=Learn_SNS;host=localhost';
    $user = 'root';
    $password='';
    $dbh = new PDO($dsn, $user, $password);
    // SQL文にエラーがあった際、画面にエラーを出力する設定
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $dbh->query('SET NAMES utf8');
?>
