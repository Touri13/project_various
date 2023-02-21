<?php
  session_start();
  require_once('../function.php');
  header('Content-type: text/html; charset=utf-8');
  //強制ブラウズはリダイレクト
  if (!isset($_SESSION['user_id'])){
    $_SESSION['error_status'] = 2;
    redirect_to_login_from_main();
    exit();
  }
  $token = $_POST['token'];
  if ($token != $_SESSION['token']) {
    $_SESSION['error_status'] = 4;
    redirect_to_login_from_main();
    exit();
  }
  //$_SESSION['token'] = get_csrf_token();

  $todo_id = $_POST['todo_id'];

  $pdo = new PDO(DNS, USER_NAME, PASSWORD, get_pdo_options());
  try {
    $sql = "DELETE FROM todo WHERE todo_id = :todo_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(":todo_id", $todo_id);
    $stmt->execute();

    header("Location: todo.php");
    exit;
  } catch (PDOException $e) {
    echo $e->getMessage();
    die();
  }
?>