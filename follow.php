<?php
session_start();
require('dbconnect.php');

// フォローするユーザーとフォローされるユーザーのIDを
// DBに保存にすれば、それがフォロー状態を示す。
$user_id = $_GET['following_id'];
$follower_id = 16;
// GET送信でprofile.phpから受け取る
if(isset($_GET['unfollow'])){
    // フォロー解除する
    $sql = 'DELETE FROM `followers` WHERE `user_id` = ? AND `follower_id` = ?';
}else{
    // フォローする
    $sql = 'INSERT INTO `followers` (`user_id`, `follower_id`) VALUES(?, ?)';
}
$data = [$user_id, $follower_id];
$stmt = $dbh->prepare($sql);
$stmt->execute($data);
// $follower_id = $stmt->fetch(PDO::FETCH_ASSOC);
// フォロー状態を保存できたら元のプロフィール画面に戻る
header('Location: profile.php?user_id=' . $user_id);
exit();