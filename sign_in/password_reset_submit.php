<?php
  session_start();
  require_once('../function.php');
  header('Content-type: text/html; charset=utf-8');
  $id = $_SESSION['user_id'];
  $password = $_POST['password'];
  $confirm_password = $_POST['confirm_password'];
  $token = $_POST['token'];
  //CSRF エラー
  if ($token != $_SESSION['token']) {
     $_SESSION['error_status'] = 2;
     redirect_to_login();
     exit();
  }
  //パスワード不一致
  if ($password != $confirm_password) {
    $_SESSION['error_status'] = 1;
    header('HTTP/1.1 301 Moved Permanently');
    header('Location: password_reset_url.php?' . $_SESSION['url_pass']);
    exit();
  }
  //パスワード更新
  try {
    $pdo = new PDO(DNS, USER_NAME, PASSWORD, get_pdo_options());
    $sql = "SELECT * FROM users WHERE user_id = ? AND reset = 1;";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(1, $id, PDO::PARAM_STR);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (empty($row)) {
      $_SESSION['error_status'] = 3;
      redirect_to_password_reset();
      exit();
    }
    $mail = $row['mailaddress'];
    $sql = "UPDATE users SET reset = 0, is_user = 1, password = ?, last_change_pass_time = ? WHERE user_id = ?;";
    $stmt = $pdo->prepare($sql);
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
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
  $msg = 'パスワードがリセットされました。' ;
  mb_send_mail($mail, 'パスワードのリセット完了', $msg, 'From :  ' . SENDER_EMAIL);
  */
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
  <link rel="stylesheet" href="../css/common.css">
</head>
<body>
<h1>パスワードリセット完了</h1>
パスワードのリセットが終了しました。<br>
ログイン画面からログインしてください。<br><br>
<a href="../index.php">ログイン画面に戻る</a>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>