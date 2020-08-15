<?php
    require('dbconnect.php');
    $sql = 'SELECT * FROM `users` WHERE `id` = ?';
    // GET送信で受け取る
    $data = [$_GET['user_id']];
    // var_dump($data);
    $stmt = $dbh->prepare($sql);
    $stmt->execute($data);
    $profile = $stmt->fetch(PDO::FETCH_ASSOC);

    // フォローする・解除するボタンの切り替え
    $sql = 'SELECT `id` FROM `followers` WHERE `user_id` = ? AND `follower_id` = ?';
    $data = [$profile['id'], $signin_user['id']];
    $stmt = $dbh->prepare($sql);
    $stmt->execute($data);
    // フォロー状態を示す
    // fetchを行ってもレコードが取得できない場合falseが入る
    $is_followed = $stmt->fetch(PDO::FETCH_ASSOC);

    // フォロワーの一覧表示
    $sql = 'SELECT `u`. * FROM `followers` AS `f` LEFT JOIN `users` AS `u` ON `u`.`id` = 
    `f`.`follower_id` WHERE `f`.`user_id` = ?';
    $data = [$profile['id']];
    $stmt = $dbh->prepare($sql);
    $stmt->execute($data);

    $followers = [];
    while(true){
        $record = $stmt->fetch(PDO::FETCH_ASSOC);
        if($record == false){
            break;
        }
        $followers[] = $record;
    }
    // フォローイングの一覧表示
    $sql = 'SELECT `u`.* FROM `followers` AS `f` LEFT JOIN `users` AS `u` ON `u`.`id` =
    `f`.`user_id` WHERE `f`.`follower_id` = ?';
    $data = [$profile['id']];
    $stmt = $dbh->prepare($sql);
    $stmt->execute($data);

    $followings = [];
    while(true){
        $record = $stmt->fetch(PDO::FETCH_ASSOC);
        if($record == false){
            break;
        }
        $followings[] = $record;
    }
?>
<?php include('layouts/header.php'); ?>
<body style="margin-top: 60px; background: #E4E6EB;">
    <?php include("navbar.php"); ?>
    <div class="container">
        <div class="row">
            <div class="col-xs-3 text-center">
                <img src="user_profile_img/<?php echo $profile['img_name']; ?>" class="img-thumbnail" />
                <h2><?php echo $profile['name']; ?></h2>
                <!-- フォローボタンを押したときの処理 -->
                <!-- サインインユーザーのidと選択されたユーザーのidを比較し、
                同一だった場合表示されない、自分以外の場合ボタンが表示される -->
                <?php if($signin_user['id'] != $profile['id']): ?>
                    <!-- 1. 自分と同一かを条件分岐 -->
                  <?php if($is_followed): ?>
                    <!-- URL?キー１=値１＆キー2=値2 -->
                    <a href="follow.php?following_id=<?php echo $profile['id']; ?>&unfollow=true">
                        <button class="btn btn-default btn-block">フォロー解除する</button>
                    </a>
                  <!-- 2. フォローしているかどうかの条件分岐 -->
                  <?php else: ?>  
                    <a href="follow.php?following_id=<?php echo $profile_user['id']; ?>">
                        <button class="btn btn-default btn-block">フォローする</button>
                    </a>
                  <?php endif; ?>
                <?php endif; ?>
            </div>

            <div class="col-xs-9">
                <ul class="nav nav-tabs">
                    <li class="active">
                        <a href="#tab1" data-toggle="tab">Followers</a>
                    </li>
                    <li>
                        <a href="#tab2" data-toggle="tab">Following</a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div id="tab1" class="tab-pane fade in active">
                        <?php foreach ($followers as $follow): ?>
                        <div class="thumbnail">
                            <div class="row">
                                <div class="col-xs-2">
                                    <img src="user_profile_img/<?php echo $follower['img_name']; ?>" width="80px">
                                </div>
                                <div class="col-xs-10">
                                    名前 <a href="profile.php?user_id=<?php echo $follower['id']; ?>" style="color: #7F7F7F;">野原みさえ</a>
                                    <br>
                                    <?php echo $follower['created'];?>からメンバー
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <div id="tab2" class="tab-pane fade">
                        <?php foreach ($followings as $following): ?>
                        <div class="thumbnail">
                            <div class="row">
                                <div class="col-xs-2">
                                    <img src="user_profile_img/<?php echo $following['img_name']; ?>" width="80px">
                                </div>
                                <div class="col-xs-10">
                                    名前 <a href="profile.php?user_id=<?php echo $following['id']; ?>" style="color: #7F7F7F;"><?php echo $following['name'];?></a>
                                    <br>
                                    <?php echo $following['created']; ?>からメンバー
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
<?php include('layouts/footer.php'); ?>
</html>