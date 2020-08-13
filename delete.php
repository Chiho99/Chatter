<?php
 require('dbconnect.php');

 $feed_id = $_GET['feed_id'];
 $sql = "DELETE FROM `feeds` WHERE `id` = ?";
 $data = [$feed_id];
 $stmt = $dbh->prepare($sql);
 $stmt->execute($data);
 
header("Location: timeline.php");
exit();