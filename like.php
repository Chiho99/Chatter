<?php 
require('dbconnect.php');
// session_start();
$feed_id = $_POST['feed_id'];
$user_id = $_POST['user_id'];
// $user_id = [$_SESSION['LearnSNS']['id']];
// var_dump($user_id);
if (isset($_POST['is_unlike'])) {
    $sql = 'DELETE FROM `likes` WHERE `user_id` = ? AND `feed_id` = ?';
} else {
    $sql = 'INSERT INTO `likes` (`feed_id`, `user_id`) VALUES (?, ?)';
}
$data = [$feed_id, $user_id];
$stmt = $dbh->prepare($sql);
$res = $stmt->execute($data);
echo json_encode($res);
// echo json_encode('test');