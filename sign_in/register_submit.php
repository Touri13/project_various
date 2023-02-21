<?php
  session_start();
  require_once('../function.php');
  header('Content-type: text/html; charset=utf-8');
  $id = $_POST['id'];
  $mail = $_POST['mail'];
  $password = $_POST['password'];
  $token = $_POST['token'];
  //CSRF チェック
  if ($token != $_SESSION['token']) {
    $_SESSION['error_status'] = 4;
    redirect_to_register();
    exit();
  }
  //ユーザーの仮登録
  //一時URLパスワード作成
  $url_pass = get_url_password();
  // パスワードハッシュ化
  $hashed_password = password_hash($password, PASSWORD_DEFAULT);
  // 現在日時
  $datetime = date('Y-m-d H:i:s');
  try {
    // DB接続
    $pdo = new PDO(DNS, USER_NAME, PASSWORD, get_pdo_options());
    //プレースホルダで SQL 作成
    $sql = "INSERT INTO users (user_id,password,mailaddress,temp_pass,temp_limit_time,register_time) ";
    $sql .=  "VALUES (?,?,?,?,?,?);";
    $stmt = $pdo->prepare($sql);
    // トランザクションの開始
    $pdo->beginTransaction();
    try {
      $stmt->bindValue(1, $id, PDO::PARAM_STR);
      $stmt->bindValue(2, $hashed_password, PDO::PARAM_STR);
      $stmt->bindValue(3, $mail, PDO::PARAM_STR);
      $stmt->bindValue(4, $url_pass, PDO::PARAM_STR);
      $stmt->bindValue(5, $datetime, PDO::PARAM_STR);
      $stmt->bindValue(6, $datetime, PDO::PARAM_STR);
      $stmt->execute();
      // コミット
      $pdo->commit();
    } catch (PDOException $e) {
      // ロールバック
      $pdo->rollBack();
      throw $e;
    }
  } catch (PDOException $e) {
    // ID重複の可能性
    $_SESSION['error_status'] = 5;
    redirect_to_register();
    exit();
  }
  //ユーザーにメールの送信
  //メールヘッダーインジェクション対策
  /*
  $mail = str_replace(array('\r\n','\r','\n'), '', $mail);
  $url = 'https://' . SERVER .  '/sign_in/register_confirm.php?' . $url_pass;
  $msg = '以下のアドレスからアカウトを有効にしてください。' . PHP_EOL;
  $msg .= 'アドレスの有効時間は１０分間です。' . PHP_EOL;
  $msg .= '有効時間後はパスワードのリセットを行ってください。' . PHP_EOL . PHP_EOL;
  $msg .= $url;
  mb_send_mail($mail, 'ユーザー登録', $msg, 'From: ' . SENDER_EMAIL);
  //' From: '　のようにForm:の前に空白があるとエラーが出る
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
  <div class="container">
    <h1>仮登録完了</h1>
    仮登録が完了しました。<br>
    登録を完了するには、送信されたメールで手続きを行ってください。
    <br><br>

    現在メール送信を行っていないため、
    <a href="<?php echo 'http://' . SERVER .  '/sign_in/register_confirm.php?' . $url_pass; ?>">こちら</a>
    から手続きを行ってください。アドレスの有効時間は１０分です。有効時間後はパスワードのリセットを行ってください。

    <br><br>
    <a href="../index.php">ログイン画面に戻る</a>
  </div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>