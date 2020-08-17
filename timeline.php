<?php
    require('dbconnect.php');
    session_start();

    $sql = 'SELECT * FROM `users` WHERE `id`=?';
    $data = [$_SESSION['LearnSNS']['id']];
    // $data = [$_GET['user_id']];
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
        $comment_sql = 'SELECT `c`.*, `u`.`name`, `u`.`img_name` FROM `comments` AS `c` JOIN `users` AS `u` ON `c`.`user_id` = `u`.`id` WHERE `feed_id` = ?';
        $comment_data = [$record['id']];
        $comment_stmt = $dbh->prepare($comment_sql);
        $comment_stmt->execute($comment_data);
        $comments = [];
        while(true){
            $comment = $comment_stmt->fetch(PDO::FETCH_ASSOC);
            if($comment == false){
                break;
            }
              $comments[] = $comment;
        }
        $record['comments'] = $comments;
        $comment_cnt_sql = 'SELECT COUNT(*) AS `comment_cnt` FROM `comments` WHERE `feed_id` = ?';
        $comment_cnt_data = [$record['id']];
        $comment_cnt_stmt = $dbh->prepare($comment_cnt_sql);
        $comment_cnt_stmt->execute($comment_cnt_data);
        $comment_cnt_result = $comment_cnt_stmt->fetch(PDO::FETCH_ASSOC);
        $record['comment_cnt'] = $comment_cnt_result['comment_cnt'];
        // $feeds[] = $record; 
        // いいね済みかどうか
        $like_flg_sql = 'SELECT * FROM `likes` WHERE `user_id` = ? AND `feed_id` = ?';
        $like_flg_data = [$signin_user['id'], $record['id']];
        $like_flg_stmt = $dbh->prepare($like_flg_sql);
        $like_flg_stmt->execute($like_flg_data);
        $is_liked = $like_flg_stmt->fetch(PDO::FETCH_ASSOC);
        $record['is_liked'] = $is_liked ? true : false;
       
         // 何件いいねされているか確認
         $like_sql = 'SELECT COUNT(*) AS `like_cnt` FROM `likes` WHERE `feed_id` = ?';
         $like_data = [$record['id']];
         $like_stmt = $dbh->prepare($like_sql);
         $like_stmt->execute($like_data);
         $like = $like_stmt->fetch(PDO::FETCH_ASSOC);
         $record['like_cnt'] = $like['like_cnt'];
         $feeds[] = $record;  
    };

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
    <span hidden id="signin-user"><?php echo $signin_user['id'];?></span>
    <div class="container">
        <div class="row">
            <div class="col-xs-3">
                <ul class="nav nav-pills nav-stacked">
                    <li class="active"><a href="timeline.php?feed_select=news">New</a></li>
                    <li><a href="timeline.php?feed_select=likes">Likes</a></li>
                </ul>
            </div>
            <div class="col-xs-9">
                <div class="feed_form thumbnail">
                    <form method="POST" action="timeline.php">
                        <div class="form-group">
                            <textarea name="feed" class="form-control" rows="3" placeholder="What's on your mind?" style="font-size: 24px;"></textarea><br>
                            <?php if (isset($errors['feed']) && $errors['feed'] == 'blank') { ?>
                            <p class="text-danger">Please fill in the box.</p>
                            <?php } ?>

                          </div>
                        <input type="submit" value="Post" class="btn btn-primary">
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
                            <?php if ($feed['is_liked']): ?>
                              <button class="btn btn-default btn-xs js-unlike">
                                <span>いいねを取り消す</span>
                              </button>
                            <?php else: ?>
                              <span hidden class="feed-id"><?php echo $feed['id']; ?></span>
                              <button class="btn btn-default js-like">
                                  <span >Like！</span>
                              </button>
                            <?php endif; ?>
                            Likes：<span class="like-count"><?php echo $feed['like_cnt']; ?></span>
                            <a href="#collapseComment<?php echo $feed['id']; ?>" data-toggle="collapse" aria-expanded="false"><span>Comment</span></a>
                            <span class="comment-count">Comments：<?php echo $feed['comment_cnt']; ?></span>
                            <?php if($feed['user_id'] == $signin_user['id']): ?>
                            <a href="edit.php?feed_id=<?php echo $feed['id']?>" class="btn btn-success btn-xs">Edit</a>
                            <a onclick="return confirm('Are you sure you want to delete this post?');" href="delete.php?feed_id=<?php echo $feed['id']?>" class="btn btn-danger btn-xs">Delete</a>
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