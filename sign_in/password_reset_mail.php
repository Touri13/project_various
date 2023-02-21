<?php
  session_start();
  require_once('../function.php');
  header('Content-type: text/html; charset=utf-8');
  $id = $_POST['id'];
  $token = $_POST['token'];
  // CSRFチェック
  if ($_SESSION['token'] != $token  || $id == "guest" || true) {
    $_SESSION['error_status'] = 3;
    redirect_to_password_reset();
    exit();
  }
  try {
    $pdo = new PDO(DNS, USER_NAME, PASSWORD, get_pdo_options());
    $sql = "SELECT * FROM users WHERE user_id = ?;";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(1, $id, PDO::PARAM_STR);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (empty($row)) {
      $_SESSION['error_status'] = 2;
      redirect_to_password_reset();
      exit();
    }
    //リセット処理
    $mail = $row['mailaddress'];
    //URLパスワードを作成
    $url_pass = get_url_password();
    $sql = "UPDATE users SET reset = 1, temp_pass = ?, temp_limit_time = ? WHERE user_id = ?;";
    $stmt = $pdo->prepare($sql);
    $pdo->beginTransaction();
    try {
      $stmt->bindValue(1, $url_pass, PDO::PARAM_STR);
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
  //メールヘッダーインジェクション対策
  /*
  $mail = str_replace(array('\r\n','\r','\n'), '', $mail);
  $msg = '以下のアドレスからパスワードのリセットを行ってください。' . PHP_EOL;
  $msg .=  'アドレスの有効時間は１０分間です。' . PHP_EOL . PHP_EOL;
  $msg .= 'http://' . SERVER . '/sign_in/password_reset_url.php?' . $url_pass;
  mb_send_mail($mail, 'パスワードのリセット', $msg, 'From :  ' . SENDER_EMAIL);
  */
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
    <div class="container">
      <h1>メール送信</h1>
      パスワードのリセットのメールを送信しました。
      <br><br>
      現在メール送信を行っていないため、
      <a href="<?php echo 'http://' . SERVER . '/sign_in/password_reset_url.php?' . $url_pass; ?>">こちら</a>
      からパスワードのリセットを行ってください。アドレスの有効時間は１０分間です。
      <br><br>
      <a href="../index.php">ログイン画面に戻る</a>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
  </body>
</html>