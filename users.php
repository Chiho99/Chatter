<?php
    session_start();
    require('dbconnect.php');

    // サインインしているユーザーの情報を取得
    $sql = 'SELECT * FROM `users` WHERE `id` = ?';
    $data = [$_SESSION['LearnSNS']['id']];
    $stmt = $dbh->prepare($sql);
    $stmt->execute($data);
    $signin_user = $stmt->fetch(PDO::FETCH_ASSOC);
    // var_dump($signin_user['id']);
    // ユーザーの一覧を取得
    $sql = 'SELECT * FROM `users`';
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    $users = [];
    while(true){
        $record = $stmt->fetch(PDO::FETCH_ASSOC);
        if($record == false){
            break;
        }
        // 各ユーザーのつぶやき数を取得
        $feed_sql = 'SELECT COUNT(*) AS `cnt` FROM `feeds` WHERE `user_id` = ?';
        $feed_data = [$record['id']];
        $feed_stmt = $dbh->prepare($feed_sql);
        $feed_stmt->execute($feed_data);
        $feed = $feed_stmt->fetch(PDO::FETCH_ASSOC);
        //$連想配列名[新しいキー] = 値;
        $record['feed_cnt'] = $feed['cnt'];
        $users[] = $record;
    }
?>
<?php include('layouts/header.php'); ?>
<body style="margin-top: 60px; background: #E4E6EB;">
    <?php include('navbar.php'); ?>
    <div class="container">
        <?php foreach($users as $user):?>
        <div class="row">
            <div class="col-xs-12">
                <div class="thumbnail">
                    <div class="row">
                        <div class="col-xs-2">
                            <img src="user_profile_img/<?php echo $user['img_name'];?>" width="80px">
                        </div>
                        <div class="col-xs-11">
                            名前  <?php echo $user['name'];?><br>
                            <!-- ユーザー一覧からどのユーザーが選択されたかを示すために、GETパラメータを追加 -->
                            <!-- 送るべき値はどのユーザーかなので、一意に絞り込めるIDをパラメータに利用 -->
                            <a href="profile.php?user_id=<?php echo $user['id']; ?>" style="color: #7f7f7f;">
                                <?php echo $user['created']; ?>からメンバー
                            </a>
                        </div>
                    </div>
                    <div class="row feed_sub">
                        <div class="col-xs-12">
                            <span class="comment_count">つぶやき数：<?php echo $user['feed_cnt']?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</body>
<?php include('layouts/footer.php'); ?>
</html>
