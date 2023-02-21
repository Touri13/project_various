<?php
  session_start();
  require_once('function.php');
  header('Content-type: text/html; charset=utf-8');
  $_SESSION['token'] = get_csrf_token(); // CSRFのトークンを取得する
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">  <title>Sign In</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
  <link href="css/common.css" rel="stylesheet">
</head>
<body class="text-center">
  <main class="form-signin">
    <div  class="container">
      
    <h1>サインイン</h1>

      <?php
        if(isset($_SESSION['error_status'])){
          if ($_SESSION['error_status'] == 1) {
            echo '<h2 style="color:red">IDまたはパスワードが異なります。</h2>';
          }
          if ($_SESSION['error_status'] == 2) {
            echo '<h2 style="color:red">不正なリクエストです。</h2>';
          }
        }
        //エラー情報のリセット
        $_SESSION['error_status'] = 0;
      ?>

      <form action="sign_in/login_check.php" class="mb-2" method="post">
        <div class="form-floating">
          <input type="text" class="form-control" name="id" id="floatingInput" placeholder="ユーザー名">
          <label for="floatingInput">ユーザー名</label>
        </div>
        <div class="form-floating"> 
          <input type="password" class="form-control" name="password" placeholder="password">
          <label for="floatingInput">password</label>
        </div>
        <input type="hidden" name="token" value="<?php echo htmlspecialchars($_SESSION['token'], ENT_QUOTES, "UTF-8") ?>">
        <button class="w-100 btn btn-lg btn-primary">ログイン</button>
      </form>

      <form action="sign_in/login_check.php" method="post">
        <input type="hidden" name="token" value="<?php echo htmlspecialchars($_SESSION['token'], ENT_QUOTES, "UTF-8") ?>">
        <input type="hidden" name="id" value="guest">
        <button class="w-100 btn btn-lg btn-success">共有アカウント</button>
      </form>

    </div>
    <br>
    <a href="sign_in/register.php">新規登録</a><br>
    <a href="sign_in/password_reset.php">パスワードリセット</a>
  </main>   
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>