<?php
  session_start();
  require_once('../function.php');
  header('Content-type: text/html; charset=utf-8');
  //強制ブラウズはリダイレクト
  if (!isset($_SESSION['user_id'])){
    $_SESSION['error_status'] = 2;
    redirect_to_login();
    exit();
  }
  $_SESSION['token'] = get_csrf_token(); // CSRFのトークンを取得する
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
  <link rel="stylesheet" href="../css/common.css"></head>
<body>
  <div class="container text-center">
    <h1>ようこそ</h1>
    <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 gy-5 gx-0">
      <div class="col">
        <form action="../todo/todo.php" method="post">
          <input type="hidden" name="token" value="<?php echo htmlspecialchars($_SESSION['token']  , ENT_QUOTES, "UTF-8") ?>">
          <button class="welcome-btn btn-primary">to-doリスト</button>
        </form>
      </div>
      <div class="col">
        <form action="../typing/typing.php" method="post">
          <input type="hidden" name="token" value="<?php echo htmlspecialchars($_SESSION['token']  , ENT_QUOTES, "UTF-8") ?>">
          <button class="welcome-btn btn-primary">ランダムアルファベット打</button>
        </form>
      </div>
      <div class="col">
        <form action="" method="post">
          <input type="hidden" name="token" value="<?php echo htmlspecialchars($_SESSION['token']  , ENT_QUOTES, "UTF-8") ?>">
          <button class="welcome-btn btn-primary">Sample</button>
        </form>
      </div>
      <div class="col">
        <form action="" method="post">
          <input type="hidden" name="token" value="<?php echo htmlspecialchars($_SESSION['token']  , ENT_QUOTES, "UTF-8") ?>">
          <button class="welcome-btn btn-primary">Sample</button>
        </form>
      </div>
      <div class="col">
        <form action="" method="post">
          <input type="hidden" name="token" value="<?php echo htmlspecialchars($_SESSION['token']  , ENT_QUOTES, "UTF-8") ?>">
          <button class="welcome-btn btn-primary">Sample</button>
        </form>
      </div>
    </div>
    <br><br>
    <div class="d-grid gap-4">
    <form action="password_change.php" method="post">
      <input type="hidden" name="token" value="<?php echo htmlspecialchars($_SESSION['token']  , ENT_QUOTES, "UTF-8") ?>">
      <button class="btn w-50 btn-secondary welcome-other-btn" name="password_change">パスワード変更</button>
    </form>
    <form action="logout.php" method="post">
      <input type="hidden" name="token" value="<?php echo htmlspecialchars($_SESSION['token']  , ENT_QUOTES, "UTF-8") ?>">
      <button class="btn w-50 btn-secondary welcome-other-btn" name="logout">ログアウト</button>
    </form>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>