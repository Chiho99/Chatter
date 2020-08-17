<?php
    session_start();
    require('../dbconnect.php');
    if (!isset($_SESSION['LearnSNS'])) {
      header('Location: signup.php');
      exit();
    }

    $name = $_SESSION['LearnSNS']['name'];
    $email = $_SESSION['LearnSNS']['email'];
    $password = $_SESSION['LearnSNS']['password'];
    $img_name = $_SESSION['LearnSNS']['img_name'];
     // 登録ボタンが押された時のみ処理するif文
     if (!empty($_POST)) {
      $sql = 'INSERT INTO `users` (`name`, `email`, `password`, `img_name`, `created`) VALUES (?,?,?,?, NOW())';
      $data = [$name, $email, password_hash($password, PASSWORD_DEFAULT), $img_name];
      $stmt = $dbh->prepare($sql);
      $stmt->execute($data);
      unset($_SESSION['LearnSNS']);
      header('Location: thanks.php');
      exit();

      }


?>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Chatter</title>
  <link rel="stylesheet" type="text/css" href="../assets/css/bootstrap.css">
  <link rel="stylesheet" type="text/css" href="../assets/font-awesome/css/font-awesome.css">
  <link rel="stylesheet" type="text/css" href="../assets/css/style.css">
</head>
<body style="margin-top: 60px">
  <div class="container">
    <div class="row">
      <div class="col-xs-8 col-xs-offset-2 thumbnail">
        <h2 class="text-center content_header">Confirm the account.</h2>
        <div class="row">
          <div class="col-xs-4">
            <img src="../user_profile_img/<?php echo htmlspecialchars($img_name); ?>" class="img-responsive img-thumbnail">
          </div>
          <div class="col-xs-8">
            <div>
              <span>Name</span>
              <p class="lead"><?php echo htmlspecialchars($name); ?></p>
            </div>
            <div>
              <span>Email</span>
              <p class="lead"><?php echo htmlspecialchars($email); ?></p>
            </div>
            <div>
              <span>Password</span>
              <!-- ② -->
              <p class="lead">●●●●●●●●</p>
            </div>
            <!-- ③ -->
            <form method="POST" action="">
              <!-- ④ -->
              <a href="signup.php?action=rewrite" class="btn btn-default">&laquo;&nbsp;Back</a> | 
              <!-- ⑤ -->
              <input type="hidden" name="action" value="submit">
              <input type="submit" class="btn btn-primary" value="Register">
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script src="../assets/js/jquery-3.1.1.js"></script>
  <script src="../assets/js/jquery-migrate-1.4.1.js"></script>
  <script src="../assets/js/bootstrap.js"></script>
</body>
</html>