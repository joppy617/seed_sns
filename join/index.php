<?php 
    session_start();
    require('../db_connect.php');

    echo '<br>';
    echo '<br>';
    echo '<br>';
    echo '<br>';

    if (!empty($_GET['action']) && $_GET['action'] == 'rewrite') {
           $nickname = $_SESSION['join']['nickname'];
           $email = $_SESSION['join']['email'];
           $password = $_SESSION['join']['password'];
    } else {
          $nickname = '';
          $email = '';
          $password = '';
    }

    if (!empty($_POST)) {
        if ($_POST['nickname'] == '') {
            $error['nickname'] = 'blank';
        }
        if ($_POST['email'] == '') {
            $error['email'] = 'blank';
        }
        if ($_POST['password'] == '') {
            $error['password'] = 'blank';
        } elseif (mb_strlen($_POST['password']) < 4) {
            $error['password'] = 'length';
        }

        if (!isset($error)) {
            $sql = 'SELECT COUNT(email) AS `count` FROM `members` WHERE `email`=? ';
            $data = array($_POST['email']);
            $stmt = $dbh->prepare($sql);
            $stmt->execute($data);
            $email_count = $stmt->fetch(PDO::FETCH_ASSOC);

            var_dump($email_count);
            if ($email_count['count'] >= 1) {
                $error['email'] = 'duplicated';
            }
            if (!isset($error)) {
                $ext = substr($_FILES['picture_path']['name'], -3);
                if ($ext == 'jpg' || $ext == 'png' || $ext == 'gif') {
                    $picture_name = date('Y.m.d') . $_FILES['picture_path']['name'];
                    move_uploaded_file($_FILES['picture_path']['tmp_name'], '../picture_path/' . $picture_name);
                    $_SESSION['join'] = $_POST;
                    $_SESSION['join']['picture_path'] = $picture_name;
                     header('Location: check.php');

                } else {
                    $error['picture_path'] = 'type';
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
              <input type="text" name="nickname" class="form-control" placeholder="例： Seed kun" value="<?php echo $nickname; ?>">
              <?php if (isset($error['nickname']) && $error['nickname'] == 'blank' ) { ?>
                <p class="error">* ニックネームが書かれていません。</p>
              <?php } ?>
            </div>
          </div>
          <!-- メールアドレス -->
          <div class="form-group">
            <label class="col-sm-4 control-label">メールアドレス</label>
            <div class="col-sm-8">
              <input type="email" name="email" class="form-control" placeholder="例： seed@nex.com" value="<?php echo $email; ?>">
              <?php if (isset($error['email']) && $error['email'] == 'blank') { ?>
                <p class="error">* メールアドレスが書かれていません。</p>
              <?php } elseif (isset($error['email']) && $error['email'] == 'duplicated') { ?>
                <p class="error">* すでに登録されていれるメールアドレスです。</p>
              <?php } ?>
            </div>
          </div>a
          <!-- パスワード -->
          <div class="form-group">
            <label class="col-sm-4 control-label">パスワード</label>
            <div class="col-sm-8">
              <input type="password" name="password" class="form-control" placeholder="" value="<?php echo $password; ?>">
              <?php if (isset($error['password']) && $error['password'] == 'blank') { ?>
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
              <input type="file" name="picture_path" class="form-control">
            </div>
            <?php if (isset($error['picture_path']) && $error['picture_path'] == 'type') { ?>
                <p class="error">* jpgまたはpngまたはgifのみ使用できます。</p>
            <?php } ?>
          </div>
          <input type="submit" class="btn btn-default" value="確認画面へ">
          <a href="../login.php" class="btn btn-default">ログイン</a>
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
