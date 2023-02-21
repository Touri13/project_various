<?php
  session_start();
  require_once('../function.php');
  header('Content-type: text/html; charset=utf-8');
  $id = $_POST['id'];
  $mail = $_POST['mail'];
  $password = $_POST['password'];
  $confirm_password = $_POST['confirm_password'];
  $token = $_POST['token'];
  // CSRF チェック
  if ($token != $_SESSION['token']) {
    $_SESSION['error_status'] = 4;
    redirect_to_register();
    exit();
  }
  // 必須項目チェック
  if (empty($id) || empty($mail) || empty($password) || empty($confirm_password)) {
    $_SESSION['error_status'] = 1;
    redirect_to_register();
    exit();
  }
  //パスワード不一致
  if ($password != $confirm_password) {
    $_SESSION['error_status'] = 2;
    redirect_to_register();
    exit();
  }
  //IDチェック
  try {
    // DB接続
    $pdo = new PDO(DNS, USER_NAME, PASSWORD, get_pdo_options());
    //プレースホルダで SQL 作成
    $sql = "SELECT COUNT(*) AS cnt FROM users WHERE user_id = ?;";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(1, $id, PDO::PARAM_STR);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    //既にIDが登録されていた
    if (!empty($row) && $row['cnt'] > 0) {
      $_SESSION['error_status'] = 3;
      redirect_to_register();
      exit();
    }
  } catch (PDOException $e) {
    die($e->getMessage());
  }
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
  <h1>確認画面</h1>
  <h2>登録しますか？</h2>
  <form action="register_submit.php" method="post">
    <table>
      <tr>
        <td>ユーザー名</td>
        <td><?php echo htmlspecialchars($id, ENT_QUOTES, 'UTF-8') ?></td>
      </tr>
     <tr>
        <td>メールアドレス</td>
        <td><?php echo htmlspecialchars($mail, ENT_QUOTES, 'UTF-8') ?></td>
      </tr>
    </table>
    <input type="hidden" name="id" value="<?php echo htmlspecialchars($id  , ENT_QUOTES, 'UTF-8') ?>">
    <input type="hidden" name="mail" value="<?php echo htmlspecialchars($mail  , ENT_QUOTES, 'UTF-8') ?>">
    <input type="hidden" name="password" value="<?php echo htmlspecialchars($password  , ENT_QUOTES, 'UTF-8') ?>">
    <input type="hidden" name="token" value="<?php echo htmlspecialchars($_SESSION['token']  , ENT_QUOTES, 'UTF-8') ?>">
    <input type="submit" value="登録">
    <input type="button" value="戻る" onclick="document.location.href='register.php';">
  </form>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>