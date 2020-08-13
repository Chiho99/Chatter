<?php
    require('dbconnect.php');
    session_start();

    $sql = 'SELECT * FROM `users` WHERE `id`=?';
    $data = [$_SESSION['LearnSNS']['id']];
    $stmt = $dbh->prepare($sql);
    $stmt->execute($data);
    $signin_user = $stmt->fetch(PDO::FETCH_ASSOC);

    // 初期化
    $errors = [];
    // ユーザーが投稿ボタンを押したら発動
    if (!empty($_POST)) {
        // バリデーション
        $feed = $_POST['feed']; // 投稿データ
        // var_dump($feed);
        // 投稿の空チェック
        if ($feed != '') {
            // 投稿処理
            $sql = 'INSERT INTO `feeds` (`feed`, `user_id`, `created`) VALUES (?,?,now())';
            $data = [$feed, $signin_user['id']];
            $stmt = $dbh->prepare($sql);
            $stmt->execute($data);

            header('Location: timeline.php');
            exit();
        } else {
            $errors['feed'] = 'blank';
        }
        
    }
    // 一覧表示機能
    // LEFT JOIN で全件取得
    // feedsテーブルにusersテーブルの情報を紐付けを全件取得できるようにする
    $sql = 'SELECT `f`.*,`u`.`name`, `u`.`img_name` FROM `feeds` AS `f` LEFT JOIN `users` AS `u` ON `f`.`user_id` = `u`.`id` ORDER BY `f`. `created` DESC';
    // var_dump($sql);
    $stmt = $dbh->prepare($sql);
    $stmt->execute();

    // 表示用の配列を初期化
    $feeds = [];
    // while文の条件は「投稿情報が取れなくなるまで」
    while(true){
        $record = $stmt->fetch(PDO::FETCH_ASSOC);
        if($record == false){
        break;
        }
        $feeds[] = $record;
    }
    // $var_dump($feeds);
    // 投稿数だけ繰り返す
    
    
    

    
?>

<?php include('layouts/header.php'); ?>
<body style="margin-top: 60px; background: #E4E6EB;">
    <?php include('navbar.php'); ?>
    <div class="container">
        <div class="row">
            <div class="col-xs-3">
                <ul class="nav nav-pills nav-stacked">
                    <li class="active"><a href="timeline.php?feed_select=news">新着順</a></li>
                    <li><a href="timeline.php?feed_select=likes">いいね！済み</a></li>
                </ul>
            </div>
            <div class="col-xs-9">
                <div class="feed_form thumbnail">
                    <form method="POST" action="timeline.php">
                        <div class="form-group">
                            <textarea name="feed" class="form-control" rows="3" placeholder="Happy Hacking!" style="font-size: 24px;"></textarea><br>
                            <?php if (isset($errors['feed']) && $errors['feed'] == 'blank') { ?>
                            <p class="text-danger">投稿データを入力してください</p>
                            <?php } ?>

                          </div>
                        <input type="submit" value="投稿する" class="btn btn-primary">
                    </form>
                </div>
                <div class="thumbnail">
                <?php foreach($feeds as $feed): ?>
                    <div class="row">
                        <div class="col-xs-1">
                            <img src="user_profile_img/<?php echo $feed['img_name'];?>" width="40px">
                        </div>
                        <div class="col-xs-11">
                            <?php echo $feed['name']; ?><br>
                            <a href="profile.php" style="color: #7f7f7f;"><?php echo $feed['created']; ?></a>
                        </div>
                    </div>
                    <div class="row feed_content">
                        <div class="col-xs-12">
                            <span style="font-size: 24px;><?php echo $feed['feed']; ?></span>
                        </div>
                    </div>
                    <div class="row feed_sub">
                        <div class="col-xs-12">
                            <button class="btn btn-default">いいね！</button>
                            いいね数：
                            <span class="like-count">10</span>
                            <a href="#collapseComment" data-toggle="collapse" aria-expanded="false"><span>コメントする</span></a>
                            <span class="comment-count">コメント数：5</span>
                            <?php if($feed['user_id'] == $signin_user['id']): ?>
                            <a href="edit.php?feed_id=<?php echo $feed['id']?>" class="btn btn-success btn-xs">編集</a>
                            <a onclick="return confirm('ほんとに消すの？');" href="delete.php?"feed_id=<?php echo $feed['id']?>" class="btn btn-danger btn-xs">削除</a>
                            <?php endif; ?>
                        </div>
                        <?php include('comment_view.php'); ?>
                    </div>
                </div>
                <?php endforeach; ?>
                <div aria-label="Page navigation">
                    <ul class="pager">
                        <li class="previous disabled"><a><span aria-hidden="true">&larr;</span> Newer</a></li>
                        <li class="next disabled"><a>Older <span aria-hidden="true">&rarr;</span></a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</body>
<?php include('layouts/footer.php'); ?>
</html>