<?php
  require_once('../function.php');
  session_start();
  header('Content-type: text/html; charset=utf-8');
  //CSRF トークン
  $_SESSION['token']  = get_csrf_token();
?>
<!DOCTYPE html>
<html lang="ja">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="../css/common.css">
   <script type="text/javascript">
      /*
      * 登録前チェック
      */
      function conrimMessage() {
        var id = document.getElementById("id").value;
       //必須チェック
       if(id == "") {
          alert("必須項目が入力されていません。");
          return false;
       }
        return true;
      }
   </script>
</head>
<body>
  <div  class="container">
    <h1>パスワードリセット</h1>
    ユーザー名を登録すると、パスワードリセット用のアドレスを登録メールアドレスに送信します。
    <p class="text-danger">現在メール送信を行っていないため、パスワードのリセットは行えません。</p>
      <?php
        if ($_SESSION['error_status'] == 1) {
          echo "<h2 style='color:red;'>パスワードをリセットしてください。</h2>";
        }
        if ($_SESSION['error_status'] == 2) {
          echo "<h2 style='color:red;'>入力内容に誤りがあります。</h2>";
        }
        if ($_SESSION['error_status'] == 3) {
          echo "<h2 style='color:red;'>不正なリクエストです。</h2>";
        }
        if ($_SESSION['error_status'] == 4) {
          echo '<h2 style="color:red;">タイムアウトか不正なURLです。</h2>';
        }
        //エラー情報のリセット
        $_SESSION['error_status'] = 0;
      ?>
    <form action="password_reset_mail.php" method="post" onsubmit="return conrimMessage();">
      <table>
        <tr>
          <td>ユーザー名</td>
          <td><input type="text" name="id" id="id"></td>
        </tr>
      </table>
      <input type="hidden" name="token" value="<?php echo htmlspecialchars($_SESSION['token']  , ENT_QUOTES, "UTF-8") ?>">
      <input type="submit" value="登録">
      <input type="button" value="戻る" onclick="document.location.href='../index.php';">
    </form>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>