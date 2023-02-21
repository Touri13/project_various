<?php
  require_once('../function.php');
  session_start();
  header('Content-type: text/html; charset=utf-8');
  $token = $_POST['token'];
  //CSRF エラー
  if ($token != $_SESSION['token']) {
     $_SESSION['error_status'] = 2;
     redirect_to_login();
     exit();
  }
  $_SESSION['token'] = get_csrf_token(); // CSRFのトークンを取得する
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
  <link rel="stylesheet" href="../css/common.css">
  <script src="../javascript/passwordchecker.js" type="text/javascript"></script>
  <script src="../javascript/common.js" type="text/javascript"></script>
  <script type="text/javascript">
    //登録前チェック
    function conrimMessage() {
      var old_pass = document.getElementById("old_password").value;
      var pass = document.getElementById("password").value;
      var conf = document.getElementById("confirm_password").value;
      if((old_pass == "") || (pass == "") || (conf == "")) {
        alert("必須項目が入力されていません。");
        return false;
      }
      if (pass != conf) {
          alert("パスワードが一致していません。");
          return false;
      }
      const passwowordLevel = getPasswordLevel(pass);
      if (passwordLevel < 3) {
        return confirm("パスワード強度が弱いですがよいですか？");
      }
      return true;
    }
  </script>
</head>
<body>
  <h1>パスワード変更画面</h1>
    <?php
       if ($_SESSION['error_status'] == 1) {
        echo '<h2 style="color:red;">入力内容に誤りがあります。</h2>';
      }
      if ($_SESSION['error_status'] == 2) {
        echo '<h2 style="color:red;">不正なリクエストです。</h2>';
      }
    ?>
  <form action="password_change_submit.php" method="post" onsubmit="return conrimMessage();">
    <table>
      <tr>
        <td>古いパスワード</td>
        <td><input type="password" name="old_password" id="old_password"></td>
      </tr>
      <tr>
        <td>新しいパスワード</td>
        <td><input type="password" name="password" id="password" onkeyup="setMessage(this.value);"></td>
        <td><div id="pass_message"></div></td>
      </tr>
      <tr>
        <td>新しいパスワード（確認）</td>
        <td><input type="password" name="confirm_password" id="confirm_password" onkeyup="setConfirmMessage(this.value);"></td>
        <td><div id="pass_confirm_message"></div></td>
      </tr>
    </table>
    <input type="hidden" name="token" value="<?php echo htmlspecialchars($_SESSION['token']  , ENT_QUOTES, "UTF-8") ?>">
    <input type="submit" value="更新">
    <input type="button" value="戻る" onclick="document.location.href='welcome.php';">
  </form>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>