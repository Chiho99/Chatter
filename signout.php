<?php

session_start();
 
//SESSION変数の破棄
$_SESSION = [];


//サーバー内の$_SESSION変数のクリア
session_destroy();


// signin.phpへ移動
header("Location: signin.php");
exit();

