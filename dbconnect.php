<?php
    // $dsn = 'mysql:dbname=Learn_SNS;host=localhost';
    // $user = 'root';
    // $password='';
    // $dbh = new PDO($dsn, $user, $password);

  
    $host = getenv('host'); //MySQLがインストールされてるコンピュータ
    $dbname = getenv('dbname'); //使用するDB
    $charset = "utf8"; //文字コード
    $dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";
    $user = getenv('username'); //MySQLにログインするユーザー名
    $password = getenv('password'); //ユーザーのパスワード
    $dbh = new PDO($dsn, $user, $password);
    // SQL文にエラーがあった際、画面にエラーを出力する設定
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $dbh->query('SET NAMES utf8');
?>


