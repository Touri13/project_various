<?php
  session_start();
  require_once('../function.php');
  $id = $_POST['id'];
  $password = isset($_POST['password']) ? $_POST['password'] : NULL;
  $token = $_POST['token'];
  // CSRF チェック
  if ($token != $_SESSION['token']) {
    $_SESSION['error_status'] = 2;
    redirect_to_login();
    exit();
  }
  try {
    $pdo = new PDO(DNS, USER_NAME, PASSWORD, get_pdo_options());
    $sql = "SELECT * FROM users WHERE user_id = ?;";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(1, $id, PDO::PARAM_STR);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (empty($row)) {  //user_idが無ければリダイレクト
      $_SESSION['error_status'] = 1;
      redirect_to_login();
      exit();
    }

    $id = $row['user_id'];
    $db_password = $row['password'];
    $reset = $row['reset'];
    $is_user = $row['is_user'];
    //パスワードリセット対応
    /*
    if ($reset == 1) {
      $_SESSION['error_status'] = 1;
     redirect_to_password_reset();
     exit();
    }*/
    // ログイン判定
    if ($is_user == 1 && password_verify($password, $db_password)
        || $id == 'guest') {
      session_regenerate_id(true); // セッション ID の振り直し
      $_SESSION['user_id'] = $id;
      $sql = "UPDATE users SET last_login_time = ? WHERE user_id = ?";
      $stmt = $pdo->prepare($sql);
      $pdo->beginTransaction();
      try {
        $stmt->bindValue(1, date('Y-m-d H:i:s'), PDO::PARAM_STR);
        $stmt->bindValue(2, $id, PDO::PARAM_STR);
        $stmt->execute();
        $pdo->commit();
      } catch (PDOException $e) {
        $pdo->rollBack();
        throw $e;
      }
      redirect_to_welcome();
    } else {
      $_SESSION['error_status'] = 1;
      redirect_to_login();
      exit();
    }
  } catch (PDOException $e) {
    die($e->getMessage());
  }