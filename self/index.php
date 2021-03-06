<?php
session_start();
    $dsn = 'mysql:dbname=0521_seed_sns;host=localhost';
    $user = 'root';
    $password = '';
    $dbh = new PDO($dsn, $user, $password);
    $dbh->query('SET NAMES UTF8');

    echo '<br>';
    echo '<br>';
    echo '<br>';
    echo '<br>';
    // echo '<pre>';
      // var_dump($_FILES);
      var_dump($_SESSION);
    //  echo '</pre>';
     if (!empty($_GET['action']) && $_GET['action'] == 'rewrite') {
         $name = $_SESSION['all']['name'];
         $mail = $_SESSION['all']['mail'];
         $password = $_SESSION['all']['password'];
     } else {
         $name = '';
         $mail ='';
         $password = '';
     }

    if (!empty($_POST)) {
      if ($_POST['name'] == '') {
          $error['name'] = 'blank';
      }
      if ($_POST['mail'] == '') {
          $error['mail'] = 'blank';
      }
      if ($_POST['password'] == '') {
          $error['password'] = 'blank';
      } elseif (mb_strlen($_POST['password']) < 4 ) {
          $error['password'] = 'length';
          var_dump($error);
      }
         var_dump($_POST);
      if (!isset($error)) {
          // $sql = 'SELECT COUNT(mail) AS `count` FROM `members` WHERE `mail`=?';
          // $data = array($_POST['mail']);
          // $stmt = $dbh->prepare($sql);
          // $stmt->execute($data);

          // $mail_check = $stmt->fetch(PDO::FETCH_ASSOC);
          // var_dump($mail_check);
          // if ($mail_check['count'] >= 1) {
          //     var_dump($mail_check);
          //     $error['mail'] = 'duplicated';
          // }
        $sql = 'SELECT COUNT(email) AS `count` FROM `members` WHERE `email`=?';
      // AS = カラムのキーを任意の文字に変えられる
      $data = array($_POST['mail']);
      $stmt = $dbh->prepare($sql);
      $stmt->execute($data);
      // 重複しているかどうかの結果を取得する
      $email_count = $stmt->fetch(PDO::FETCH_ASSOC);
      // var_dump($email_count);
      // もし$email_count['count']が1以上の時
      if ($email_count['count'] >= 1) {
        $error['mail'] = 'duplicated';
      }
      echo '<pre>';
      var_dump($error);
      echo '</pre>';

          if (!isset($error)) {
              $ext = substr($_FILES['photo']['name'], -3);
              if ($ext == 'png' || $ext == 'jpg' || $ext == 'png') {
                  $photo = date('Y.m.d') . $_FILES['photo']['name'];
                  move_uploaded_file($_FILES['photo']['tmp_name'], '../photos/' . $photo);
                  $_SESSION['all'] = $_POST;
                  $_SESSION['all']['photo'] = $photo;

                    header('location: check.php');
                   // echo '<pre>';
                   //  var_dump($_SESSION);
                   //  echo '</pre>';
              } else {
                  $error['photo'] = 'type';
              }
          }

      }


    }



 ?>

 <!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>SeedSNS</title>

    <!-- Bootstrap -->
    <link href="../assets/css/bootstrap.css" rel="stylesheet">
    <link href="../assets/font-awesome/css/font-awesome.css" rel="stylesheet">
    <link href="../assets/css/form.css" rel="stylesheet">
    <link href="../assets/css/timeline.css" rel="stylesheet">
    <link href="../assets/css/main.css" rel="stylesheet">
    <!--
      designフォルダ内では2つパスの位置を戻ってからcssにアクセスしていることに注意！
     -->

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
              </ul>
          </div>
          <!-- /.navbar-collapse -->
      </div>
      <!-- /.container-fluid -->
  </nav>

  <div class="container">
    <div class="row">
      <div class="col-md-6 col-md-offset-3 content-margin-top">
        <legend>会員登録</legend>
        <form method="post" action="" class="form-horizontal" role="form" enctype="multipart/form-data">
          <!-- ニックネーム -->
          <div class="form-group">
            <label class="col-sm-4 control-label">ニックネーム</label>
            <div class="col-sm-8">
              <input type="text" name="name" class="form-control" placeholder="例： Seed kun" value="<?php echo $name; ?>">
              <?php if (isset($error['name']) && $error['name'] == 'blank'): ?>
                <p class="error">* ニックネームが書かれていません。</p>
              <?php endif ?>
            </div>
          </div>
          <!-- メールアドレス -->
          <div class="form-group">
            <label class="col-sm-4 control-label">メールアドレス</label>
            <div class="col-sm-8">
              <input type="email" name="mail" class="form-control" placeholder="例： seed@nex.com" value="<?php echo $mail; ?>">
              <?php if (isset($error['mail']) && $error['mail'] == 'blank') { ?>
                <p class="error">* メールアドレスが書かれていません。</p>
              <?php } elseif (isset($error['mail']) && $error['mail'] == 'duplicated') { ?>
                <p class="error">* すでに登録されているメールアドレスです。</p>
              <?php } ?>
            </div>
          </div>
          <!-- パスワード -->
          <div class="form-group">
            <label class="col-sm-4 control-label">パスワード</label>
            <div class="col-sm-8">
              <input type="password" name="password" class="form-control" placeholder="" value="<?php echo $password; ?>">
              <?php if (isset($error['password']) &&$error['password'] == 'blank') { ?>
                <p class="error">* パスワードが書かれていません。</p>
              <?php } elseif (isset($error['password']) && $error['password'] == 'length') { ?>
                <p class="error">* 4文字以上で入力してください。</p>
              <?php } ?>
            </div>
          </div>
          <!-- プロフィール写真 -->
          <div class="form-group">
            <label class="col-sm-4 control-label">プロフィール写真</label>
            <div class="col-sm-8">
              <input type="file" name="photo" class="form-control">
              <?php if (isset($error['photo']) && $error['photo'] == 'type') { ?>
                <p class="error">* pngまたはjpgまたはgifのみ使用できます。</p>
              <?php } ?>
            </div>
          </div>

          <input type="submit" class="btn btn-default" value="確認画面へ">
        </form>
      </div>
    </div>
  </div>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="../assets/js/jquery-3.1.1.js"></script>
    <script src="../assets/js/jquery-migrate-1.4.1.js"></script>
    <script src="../assets/js/bootstrap.js"></script>
  </body>
</html>
