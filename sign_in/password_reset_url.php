<?php
  session_start();
  require_once('../function.php');
  header('Content-type: text/html; charset=utf-8');
  //URLからパラメータ取得
  // 現在のURLのドメイン以下のパスを取得し、その内のクエリ部分を取得
  $url_pass = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);
  //CSRF
  $_SESSION['token'] = get_csrf_token();
  //ユーザー正式登録
  try {
    // DB接続
    $pdo = new PDO(DNS, USER_NAME, PASSWORD, get_pdo_options());
    //10分前の時刻を取得
    $datetime = new DateTime('- 10 min');
    $sql = "SELECT * FROM users WHERE temp_pass = ? AND temp_limit_time >= ?;";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(1, $url_pass, PDO::PARAM_STR);
    $stmt->bindValue(2, $datetime->format('Y-m-d H:i:s'), PDO::PARAM_STR);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    //URLが不正か期限切れ
    if (empty($row)) {
      $_SESSION['error_status'] = 4;
      redirect_to_password_reset();
      exit();
    }
    $_SESSION['user_id'] = $row['user_id'];
    $_SESSION['url_pass'] = $url_pass; // エラー制御のため格納
  } catch (PDOException $e) {
    die($e->getMessage());
  }
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
      /*
      * 登録前チェック
      */
      function confirmMessage() {
        var pass = document.getElementById("password").value;
        var conf = document.getElementById("confirm_password").value;
       if((pass == "") || (conf == "")) {
          alert("必須項目が入力されていません。");
          return false;
       }
        if (pass != conf) {
            alert("パスワードが一致していません。");
            return false;
        }
        if (passwordLevel < 3) {
          return confirm("パスワード強度が弱いですがよいですか？");
        }
        return true;
      }
   </script>
</head>
<body>
  <h1>パスワード変更画面（リセット）</h1>
    <?php
       if ($_SESSION['error_status'] == 1) {
        echo '<h2 style="color:red;">パスワードが一致しません。</h2>';
      }
    ?>
  <form action="password_reset_submit.php" method="post" onsubmit="return confirmMessage();">
    <table>
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
    <input type="button" value="戻る" onclick="document.location.href='../index.php';">
  </form>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>