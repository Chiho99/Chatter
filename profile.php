<?php
    require('dbconnect.php');
    session_start();
    // userのidをsessionで持たせる
    
    // サインインユーザー
    $sql = 'SELECT * FROM `users` WHERE `id` = ?';
    $data = [$_SESSION['LearnSNS']['id']];
    $stmt = $dbh->prepare($sql);
    $stmt->execute($data);
    $signin_user = $stmt->fetch(PDO::FETCH_ASSOC);
    $_SESSION['id'] = $signin_user['id'];
    // var_dump( $_SESSION['id']);
    // プロフィールを表示しているユーザーのID
    $sql = 'SELECT * FROM `users` WHERE `id`=?';
    $data = [$_GET['user_id']];
    $stmt = $dbh->prepare($sql);
    $stmt->execute($data);
    $profile = $stmt->fetch(PDO::FETCH_ASSOC);

    // var_dump($profile);
    // フォローする・解除するボタンの切り替え
    $sql = 'SELECT `id` FROM `followers` WHERE `user_id` = ? AND `follower_id` = ?';
    $data = [$profile['id'], $signin_user['id']];
    $stmt = $dbh->prepare($sql);
    $stmt->execute($data);
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
        // var_dump($record);
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
                      <button class="btn btn-default btn-block">Unfollow</button>
                    </a>
                  <!-- 2. フォローしているかどうかの条件分岐 -->
                  <?php else: ?>  
                    <a href="follow.php?following_id=<?php echo $profile['id']; ?>">
                        <button class="btn btn-default btn-block">Follow</button>
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
                        <?php foreach ($followers as $follower): ?>
                        <div class="thumbnail">
                            <div class="row">
                                <div class="col-xs-2">
                                    <img src="user_profile_img/<?php echo $follower['img_name']; ?>" width="80px">
                                </div>
                                <div class="col-xs-10">
                                    Name: <a href="profile.php?user_id=<?php echo $follower['id']; ?>" style="color: #7F7F7F;">
                                    <?php echo $follower['name']; ?></a>
                                    <br>
                                    Since: <?php echo $follower['created'];?>
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
                                    Name: <a href="profile.php?user_id=<?php echo $following['id']; ?>" style="color: #7F7F7F;">
                                    <?php echo $following['name'];?></a>
                                    <br>
                                    Since: <?php echo $following['created']; ?>
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