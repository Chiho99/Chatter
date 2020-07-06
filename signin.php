<?php
    require('dbconnect.php');
    // 初期化
    session_start();
    $errors = [];
    if (!empty($_POST)) {
      // ①
      $email = $_POST['input_email'];
      $password = $_POST['input_password'];
      // var_dump($email);
      if ($email != '' && $password != '') {
          // データベースとの照合処理
        $sql = 'SELECT * FROM `users` WHERE `email`=?';
        $data = [$email];
        $stmt = $dbh->prepare($sql);
        $stmt->execute($data);
        $record = $stmt->fetch(PDO::FETCH_ASSOC);
        // var_dump($record['id']);
        // var_dump($record);
        // メールアドレスでの本人確認
        if ($record == false) {
          $errors['signin'] = 'failed';
        } 
      } else {
        $errors['signin'] = 'blank';
      }
      if (password_verify($password,$record['password'])){
        //認証成功
        //※追加部分 
        //SESSION変数にIDを保存
        $_SESSION['LearnSNS']['id'] = $record['id'];
        //timeline.phpに移動
        header("Location: timeline.php");
        exit();
      }else{
        $errors['signin'] = 'failed';
      }
    }
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <title>Learn SNS</title>
    <link rel="stylesheet" type="text/css" href="./assets/css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="./assets/font-awesome/css/font-awesome.css">
    <link rel="stylesheet" type="text/css" href="./assets/css/style.css">
</head>
<body style="margin-top: 60px">
    <div class="container">
      <div class="row">
          <div class="col-xs-8 col-xs-offset-2 thumbnail">
              <h2 class="text-center content_header">サインイン</h2>
              <form method="POST" action="signin.php">
                  <div class="form-group">
                    <label for="email">メールアドレス</label>
                    <input type="email" name="input_email" class="form-control" id="email" placeholder="example@gmail.com">
                    <?php if(isset($errors['signin']) && $errors['signin'] == 'blank'): ?>
                      <p class="text-danger">メールアドレスとパスワードを正しく入力してください</p>
                    <?php endif; ?>
                    <?php if(isset($errors['signin']) && $errors['signin'] == 'failed'): ?>
                      <p class="text-danger">サインインに失敗しました</p>
                    <?php endif; ?>
                  </div>
                  <div class="form-group">
                    <label for="password">パスワード</label>
                    <input type="password" name="input_password" class="form-control" id="password" placeholder="4 ~ 16文字のパスワード">
                  </div>
                  <input type="submit" class="btn btn-info" value="サインイン">
              </form>
          </div>
      </div>
    </div>
</body>
<script src="./assets/js/jquery-3.1.1.js"></script>
<script src="./assets/js/jquery-migrate-1.4.1.js"></script>
<script src="./assets/js/bootstrap.js"></script>
</html>