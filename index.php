<?php

?>
<?php include('layouts/header.php'); ?>
<body style="margin-top: 60px">
    <div class="container">
        <div class="row">
            <div class="col-xs-8 col-xs-offset-2 thumbnail">
                <h2 class="text-center content_header">Welcome to Chatter <img src="https://a.slack-edge.com/production-standard-emoji-assets/10.2/google-large/1f5e3-fe0f@2x.png" style="width: 40px;"></h2>
                <div class="text-center">
                  <div>
                    <p>Login to your account.</p>
                    <a href="signin.php">
                      <input type="submit" class="btn btn-info" value="Log in">
                    </a>
                  </div>
                  <hr>
                  <div style="margin-top: 20px">
                    <p>New to Chatter ?</p>
                    <a href="register/signup.php">
                      <input type="submit" class="btn btn-success" value="Sign Up">
                    </a>
                  </div>
                </div>
            </div>
        </div>
    </div>
</body>
<?php include('layouts/footer.php'); ?>
</html>
