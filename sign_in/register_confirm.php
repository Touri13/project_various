<?php
  session_start();
  require_once('../function.php');
  header('Content-type: text/html; charset=utf-8');
  //URLからパラメータ取得
  $url_pass = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);
  //ユーザー正式登録
  try {
    // DB接続
    $pdo = new PDO(DNS, USER_NAME, PASSWORD, get_pdo_options());
    //プレースホルダで SQL 作成
    $sql = "SELECT * FROM users WHERE temp_pass = ? AND register_time >= ?;";
    $stmt = $pdo->prepare($sql);
    //10分前の時刻を取得
    $datetime = new DateTime('- 10 min');
    $stmt->bindValue(1, $url_pass, PDO::PARAM_STR);
    $stmt->bindValue(2, $datetime->format('Y-m-d H:i:s'), PDO::PARAM_STR);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    //URLが不正か期限切れ
    if (empty($row)) {
      $_SESSION['error_status'] = 6;
      redirect_to_register();
      exit();
    }
    $id = $row['user_id'];
    $sql = "UPDATE users SET is_user = 1 WHERE user_id = ?;";
    $stmt = $pdo->prepare($sql);
    // トランザクションの開始
    $pdo->beginTransaction();
    try {
      $stmt->bindValue(1, $id, PDO::PARAM_STR);
      $stmt->execute();
      // コミット
      $pdo->commit();
    } catch (PDOException $e) {
      // ロールバック
      $pdo->rollBack();
      throw $e;
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
<h1>登録完了</h1>
ユーザーの登録が終了しました。<br>
ログイン画面からログインしてください。<br><br>
<a href="../index.php">ログイン画面に戻る</a>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>