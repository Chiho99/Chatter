$(function() {
    $(document).on('click', '.js-like', function() {
        let feed_id = $(this).siblings('.feed-id').text();
        let user_id = $('#signin-user').text();
        // console.log(feed_id);
        // console.log(user_id);
        let like_btn = $(this);
        let like_count = $(this).siblings('.like_count').text();
        $.ajax({
            // 送信先や送信するデータなど
            url: 'like.php',
            type: 'POST',
            datatype: 'json',
            data: {
            'feed_id': feed_id,
            'user_id': user_id
            }
        }).done(function (data) {
            // 成功時の処理
            if (data == 'true') {
                like_count++;
                like_btn.siblings('.like_count').text(like_count);
                like_btn.removeClass('js-like');
                like_btn.addClass('js-unlike');
                like_btn.children('span').text('いいねを取り消す');
            }
        }).fail(function (e) {
            // 失敗時の処理
            console.log(e);
            })
        });
    $(document).on('click', '.js-unlike', function() {
        var feed_id = $(this).siblings('.feed-id').text();
        var user_id = $('#signin-user').text();
        var like_btn = $(this);
        var like_count = $(this).siblings('.like_count').text();
        $.ajax({
            url: 'like.php',
            type: 'POST',
            datatype: 'json',
            data: {
            'feed_id': feed_id,
            'user_id': user_id,
            'is_unlike': true
            }
        }).done(function (data) {
            if (data == 'true') {
                like_count--;
                like_btn.siblings('.like_count').text(like_count);
                like_btn.removeClass('js-unlike');
                like_btn.addClass('js-like');
                like_btn.children('span').text('いいね！');
            }
        }).fail(function (e) {
        console.log(e);
        })
    });
});
            
