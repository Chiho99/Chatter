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
    // $var_dump($feeds);
    if(isset($_GET['page'])){
        $page = $_GET['page'];
    }else{
        $page = 1;
    }
    // 一覧表示機能
    const CONTENT_PER_PAGE = 5;
    // -1などのページ数として不正な値を渡された場合の対策
    $page = max($page, 1);
    // ヒットしたレコードの数を取得するSQL
    $sql_count = 'SELECT COUNT(*) AS `cnt` FROM `feeds`';
    $stmt_count = $dbh->prepare($sql_count);
    $stmt_count->execute();
    $record_cnt = $stmt_count->fetch(PDO::FETCH_ASSOC);
    
    // 最後のページが何ページになるのかを算出
    // 最後のページ = 取得してページ数 + 1ページあたりに表示する件数
    $last_page = ceil($record_cnt['cnt'] / CONTENT_PER_PAGE);

    // 最後のページより大きい値が渡された場合の対策
    $page = min($page, $last_page);

    $start = ($page - 1) * CONTENT_PER_PAGE;

    // LEFT JOIN で全件取得
    // feedsテーブルにusersテーブルの情報を紐付けを全件取得できるようにする
    $sql = 'SELECT `f`.*, `u`.`name`, `u`.`img_name` FROM `feeds` AS `f` LEFT JOIN 
    `users` AS `u` ON `f`.`user_id`=`u`.`id` ORDER BY `created` DESC LIMIT '.CONTENT_PER_PAGE .' OFFSET ' . $start;

   
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
    if (isset($_GET['search_word'])) {
        // 検索を行なった場合の遷移
        $sql = 'SELECT `f`.*, `u`.`name`, `u`.`img_name` FROM `feeds` AS `f` LEFT JOIN `users`
        AS `u` ON `f`.`user_id`=`u`.`id` WHERE `f`.`feed` LIKE "%"?"%" ORDER BY `created` DESC LIMIT '. CONTENT_PER_PAGE .' OFFSET ' . $start;
        $data = [$_GET['search_word']];
    } else {
        // その他の遷移
        $sql = 'SELECT `f`.*, `u`.`name`, `u`.`img_name` FROM `feeds` AS `f` LEFT JOIN 
        `users` AS `u` ON `f`.`user_id`=`u`.`id` ORDER BY `created` DESC LIMIT '. CONTENT_PER_PAGE .' OFFSET ' . $start;
        $data = [];
    }
   
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
                            <span style="font-size: 24px;"><?php echo $feed['feed']; ?></span>
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
                            <a onclick="return confirm('ほんとに消すの？');" href="delete.php?feed_id=<?php echo $feed['id']?>" class="btn btn-danger btn-xs">削除</a>
                            <?php endif; ?>
                        </div>
                        <?php include('comment_view.php'); ?>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <div aria-label="Page navigation">
                    <ul class="pager">
                        <?php if($page == 1):?>
                         <li class="previous disabled"><a href="timeline.php?page=<?php echo $page + 1; ?>"><span aria-hidden="true">&larr; Newer</span></a></li>
                        <?php else: ?>
                          <li class="previous"><a href="timeline.php?page=<?php echo$page -1;?>"><span area-hidden="true">&larr;</span>Newer</a></li>
                        <?php endif; ?>

                        <?php if($page == $last_page):?>
                          <li class="next disabled"><a href="timeline.php?page=<?php echo $page + 1; ?>">Older<span aria-hidden="true"> Older &rarr;</span></a></li>
                        <?php else:?>
                          <li class="next"><a href="timeline.php?page=<?php echo $page + 1; ?>">Older<span aria-hidden="true"> Older &rarr;</span></a></li>
                        <?php endif;?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</body>
<?php include('layouts/footer.php'); ?>
</html>