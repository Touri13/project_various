<?php
  session_start();
  require_once('../function.php');
  header('Content-type: text/html; charset=utf-8');
  $id = $_SESSION['user_id'];
  $old_password = $_POST['old_password'];
  $password = $_POST['password'];
  $confirm_password = $_POST['confirm_password'];
  $token = $_POST['token'];
  // CSRFチェック
  if ($token != $_SESSION['token']) {
     $_SESSION = array();
     session_destroy();
     session_start();
    $_SESSION['error_status'] = 2;
    redirect_to_login();
    exit();
  }
    //パスワード不一致
  if ($password != $confirm_password) {
    $_SESSION['error_status'] = 1;
    //POSTで戻る
    echo_html_submit();
    exit();
  }
  try {
    $pdo = new PDO(DNS, USER_NAME, PASSWORD, get_pdo_options());
    //user_idと旧パスワードチェック
    $sql = "SELECT * FROM users WHERE user_id = ? AND is_user = 1;";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(1, $id, PDO::PARAM_STR);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (empty($row)) {
      $_SESSION['error_status'] = 2;
      echo_html_submit();
      exit();
    }
    $mail = $row['mailaddress'];
    if (!password_verify($old_password, $row['password'])) {
      $_SESSION['error_status'] = 1;
      echo_html_submit();
      exit();
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $sql = "UPDATE users SET password = ?, reset = 0, last_change_pass_time = ? WHERE user_id = ?;";
    $stmt = $pdo->prepare($sql);
    $pdo->beginTransaction();
    try {
      $stmt->bindValue(1, $hashed_password, PDO::PARAM_STR);
      $stmt->bindValue(2, date('Y-m-d H:i:s'), PDO::PARAM_STR);
      $stmt->bindValue(3, $id, PDO::PARAM_STR);
      $stmt->execute();
      $pdo->commit();
    } catch (PDOException $e) {
      $pdo->rollBack();
      throw $e;
    }
  } catch (PDOException $e) {
    die($e->getMessage());
  }
  //メール送信
  /*
  $mail = str_replace(array('\r\n','\r','\n'), '', $mail);  //メールヘッダーインジェクション対策
  $msg = 'パスワードが変更されました。';
  mb_send_mail($mail, 'パスワードの変更', $msg, 'From : ' . SENDER_EMAIL);
  */
  /*
  * HTML を出力してPOSTリクエストで戻る
  */
  function echo_html_submit() {
    echo '<!DOCTYPE html>';
    echo '<head>';
    echo '<meta charset="utf-8">';
    echo '</head>';
    echo '<html lang="ja">';
    echo '<body onload="document.returnForm.submit();">';
    echo '<form name="returnForm" method="post" action="password_change.php">';
    echo '<input type="hidden" name="token" value="' .  htmlspecialchars($_SESSION['token'], ENT_QUOTES, 'UTF-8') . '">';
    echo '</form>';
    echo '</body>';
    echo '</html>';
  }
?>
<!DOCTYPE html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
  <link rel="stylesheet" href="../css/common.css">
</head>
<html lang="ja">
  <body>
    <h1>完了画面</h1>
    パスワードの変更が完了しました。
    <br><br>
    <a href="../index.php">ログイン画面に戻る</a>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
  </body>
</html>