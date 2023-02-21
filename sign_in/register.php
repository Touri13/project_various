<?php
  session_start();
  require_once('../function.php');
  header('Content-type: text/html; charset=utf-8');

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
    <script src="passwordchecker.js" type="text/javascript"></script>
    <script src="../javascript/common.js" type="text/javascript"></script>
    <script type="text/javascript">
      /*
      * 登録前チェック
      */
      function conrimMessage() {
        var id = document.getElementById("id").value;
        var mail = document.getElementById("mail").value;
        var pass = document.getElementById("password").value;
        var conf = document.getElementById("confirm_password").value;
       if((id == "") || (mail == "") || (pass == "") || (conf == "")) {
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
    <h1>登録画面</h1>
    <?php
      if ($_SESSION['error_status'] == 1) {
        echo '<h2 style="color:red;">必須項目が入力されてません。</h2>';
      }
      if ($_SESSION['error_status'] == 2) {
        echo '<h2 style="color:red;">パスワードが不一致です。</h2>';
      }
      if ($_SESSION['error_status'] == 3) {
        echo '<h2 style="color:red;">IDは既に登録されています。</h2>';
      }
      if ($_SESSION['error_status'] == 4) {
        echo '<h2 style="color:red;">不正なリクエストです。</h2>';
      }
      if ($_SESSION['error_status'] == 5) {
        echo '<h2 style="color:red;">登録処理に失敗しました。</h2>';
      }
      if ($_SESSION['error_status'] == 6) {
        echo '<h2 style="color:red;">タイムアウトか不正な URL です。</h2>';
      }
      //エラー情報リセット
      $_SESSION['error_status'] = 0;
    ?>
    <p class="text-danger">現在メールアドレスは必要ありません。</p>
    <form action="register_check.php" method="post" onsubmit="return conrimMessage();">
      <table>
        <tr>
          <td>ユーザー名</td>
          <td><input type="text" name="id" id="id"></td>
        </tr>
        <tr>
          <td>メールアドレス </td>
          <input type="hidden" name="mail" value="name@example.com">
          <td><input type="text" name="mail_disabled" id="mail" disabled value="name@example.com"></td>
        </tr>
        <tr>
          <td>パスワード</td>
          <td><input type="password" name="password" id="password" onkeyup="setMessage(this.value);"></td>
          <td><div id="pass_message"></div></td>
        </tr>
        <tr>
          <td>パスワード（確認）</td>
          <td><input type="password" name="confirm_password" id="confirm_password" onkeyup="setConfirmMessage(this.value);"></td>
          <td><div id="pass_confirm_message"></div></td>
        </tr>
      </table>
      <input type="hidden" name="token" value="<?php echo htmlspecialchars($_SESSION['token'], ENT_QUOTES, "UTF-8") ?>">
      <input type="submit" value="登録">
      <input type="reset" value="リセット">
      <input type="button" value="戻る" onclick="document.location.href='../index.php';">
    </form>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
  </body>
</html>