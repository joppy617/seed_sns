<?php 
    session_start();
    require('db_connect.php');

    echo '<br>';
    echo '<br>';
    echo '<br>';
    echo '<br>';

   // var_dump($_SESSION);

    if (isset($_SESSION['login_id']) && $_SESSION['time'] + 3600 > time()) {
          $_SESSION['time'] = time();
          $sql = 'SELECT * FROM `members` WHERE `member_id`=? ';
          $data = array($_SESSION['login_id']);
          $stmt = $dbh->prepare($sql);
          $stmt->execute($data);
          $login_member = $stmt->fetch(PDO::FETCH_ASSOC);

    } else {
        header('Location: login.php');
    }

    if (!empty($_POST)) {
          if ($_POST['tweet'] == '') {
                $error['tweet'] = 'blank';
          }

          if (!isset($error)) {
                $sql = 'INSERT INTO `tweets` SET `tweet`=?, `member_id`=?, `reply_tweet_id`=-1, `created`=NOW() ';
                $data = array($_POST['tweet'], $login_member['member_id'],);
                $stmt = $dbh->prepare($sql);
                $stmt->execute($data);

          }

    }

          if (isset($_GET['page'])) {
              $page = $_GET[ 'page'];
          } else {
              $page = 1;
          }

              $page = max($page, 1);
              $max_page_tweet = 5;

              $page_sql = 'SELECT COUNT(*) AS `count` FROM `tweets` WHERE `delete_flag`=0 ';
              $page_stmt = $dbh->prepare($page_sql);
              $page_stmt->execute();

              $max_tweets = $page_stmt->fetch(PDO::FETCH_ASSOC);
              $all_pages_number = ceil($max_tweets['count'] / $max_page_tweet);
              $page = min($page, $all_pages_number);

              $start_page = ($page -1) * $max_page_tweet;




          $tweet_sql = 'SELECT `tweets`.*, `members`.`nickname`, `members`.`email`, `members`.`picture_path` FROM `tweets` LEFT JOIN `members` ON `tweets`.`member_id`=`members`.`member_id` WHERE `delete_flag`=0 ORDER BY `tweets`.`created` DESC LIMIT '.$start_page.','.$max_page_tweet;
          $tweet_stmt = $dbh->prepare($tweet_sql);
          $tweet_stmt->execute();


          $tweets = array();

          while (true) {
                $tweet = $tweet_stmt->fetch(PDO::FETCH_ASSOC);
                if ($tweet == false) {
                  break;
                }
                $like_sql = 'SELECT COUNT(*) AS `like_count` FROM `likes` WHERE `tweet_id`=?';
                $like_data = array($tweet['tweet_id']);
                $like_stmt = $dbh->prepare($like_sql);
                $like_stmt->execute($like_data);
                $tweet_likes = $like_stmt->fetch(PDO::FETCH_ASSOC);
                var_dump($tweet_likes['like_count']);
                $tweet['tweet_likes'] = $tweet_likes['like_count'];

                $login_like_sql = 'SELECT COUNT(*) AS `like_count` FROM `likes` WHERE `tweet_id`=? AND `member_id`=?';
                $login_like_data = array($tweet['tweet_id'], $login_member['member_id']);
                $login_like_stmt = $dbh->prepare($login_like_sql);
                $login_like_stmt->execute($login_like_data);
                $login_count = $login_like_stmt->fetch(PDO::FETCH_ASSOC);
                // var_dump($login_count['like_count']);
                $tweet['like_count'] = $login_count['like_count'];

                $tweets[] = $tweet;


          }


            echo '<pre>';
            var_dump($tweets);
            echo '</pre>';


 ?>
 <!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>SeedSNS</title>

    <!-- Bootstrap -->
    <link href="assets/css/bootstrap.css" rel="stylesheet">
    <link href="assets/font-awesome/css/font-awesome.css" rel="stylesheet">
    <link href="assets/css/form.css" rel="stylesheet">
    <link href="assets/css/timeline.css" rel="stylesheet">
    <link href="assets/css/main.css" rel="stylesheet">

  </head>
  <body>
  <nav class="navbar navbar-default navbar-fixed-top">
      <div class="container">
          <!-- Brand and toggle get grouped for better mobile display -->
          <div class="navbar-header page-scroll">
              <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                  <span class="sr-only">Toggle navigation</span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
              </button>
              <a class="navbar-brand" href="index.html"><span class="strong-title"><i class="fa fa-twitter-square"></i> Seed SNS</span></a>
          </div>
          <!-- Collect the nav links, forms, and other content for toggling -->
          <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
              <ul class="nav navbar-nav navbar-right">
                <li><a href="logout.php">ログアウト</a></li>
              </ul>
          </div>
          <!-- /.navbar-collapse -->
      </div>
      <!-- /.container-fluid -->
  </nav>

  <div class="container">
    <div class="row">
      <div class="col-md-4 content-margin-top">
        <legend>ようこそ<a href="login_user.php?member_id=<?php echo $login_member['member_id']; ?>"><?php echo $login_member['nickname']; ?></a>さん！</legend>
        <form method="post" action="" class="form-horizontal" role="form">
            <!-- つぶやき -->
            <div class="form-group">
              <label class="col-sm-4 control-label">つぶやき</label>
              <div class="col-sm-8">
                <textarea name="tweet" cols="50" rows="5" class="form-control" placeholder="例：Hello World!"></textarea>
              </div>
            </div>
          <ul class="paging">
            <input type="submit" class="btn btn-info" value="つぶやく">
                &nbsp;&nbsp;&nbsp;&nbsp;
                <?php if ($page != 1) { ?>
                <li><a href="index.php?page=<?php echo $page -1; ?>" class="btn btn-default">前</a></li>
                <?php } else { ?>
                  <li>*前</li>
                <?php } ?>
                &nbsp;&nbsp;|&nbsp;&nbsp;
                <?php if ($page != $all_pages_number) { ?>
                <li><a href="index.php?page=<?php echo $page + 1; ?>" class="btn btn-default">次</a></li>
                <?php } else { ?>
                  <li>*次</li>
                <?php } ?>
          </ul>
        </form>
      </div>

      <div class="col-md-8 content-margin-top">
       <?php foreach ($tweets as $tweet) { ?>
        <div class="msg">
          <img src="picture_path/<?php echo $tweet['picture_path']; ?>" width="48" height="48">
          <p>
            <?php echo $tweet['tweet']; ?><span class="name"><a href="profile.php?member_id=<?php echo $tweet['member_id']; ?>"> (<?php echo $tweet['nickname']; ?>)</a> </span>
            [<a href="reply.php?tweet_id=<?php echo $tweet['tweet_id']; ?>">Re</a>]
          </p>
          <p class="day">
            <a href="view.php?tweet_id=<?php echo $tweet['tweet_id']; ?>">
              <?php echo $tweet['created']; ?>
            </a>
            <?php if ($login_member['member_id'] == $tweet['member_id']): ?>
            [<a href="edit.php?tweet_id=<?php echo $tweet['tweet_id']; ?>" style="color: #00994C;">編集</a>]
            [<a href="delete.php?tweet_id=<?php echo $tweet['tweet_id']; ?>" style="color: #F33;">削除</a>]
            <?php endif ?>
            <a href="like.php?like_tweet_id=<?php echo $tweet['tweet_id']; ?>" style="color: blue;"><i class="fa fa-thumbs-o-up">[いいね]</i></a>
            <a href="like.php?dislike_tweet_id=<?php echo $tweet['tweet_id']; ?>" style="color: blue;"><i class="fa fa-thumbs-o-down">[よくないね]</i></a>
          </p>
        </div>
       <?php } ?>
      </div>

    </div>
  </div>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="assets/js/jquery-3.1.1.js"></script>
    <script src="assets/js/jquery-migrate-1.4.1.js"></script>
    <script src="assets/js/bootstrap.js"></script>
  </body>
</html>

