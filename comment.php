<?php
session_start();
require('dbconnect.php');

$user_id = $_SESSION['LearnSNS']['id'];
$feed_id = $_POST['feed_id'];
$comment = $_POST['write_comment'];

$sql = 'INSERT INTO `comments`(`user_id`, `feed_id`, `comment`, `created`)VALUES(?, ?, ?, now())';
$data = [$user_id, $feed_id, $comment];
$stmt = $dbh->prepare($sql);
$stmt->execute($data);

header('Location: timeline.php');
exit();

