<?php
  session_start();
  require_once('../function.php');
  $token = $_POST['token'];
  // CSRF チェック
  if ($token != $_SESSION['token']) {
    redirect_to_welcome();
    exit();
  }
  //セッション破棄
  $_SESSION = array();
  session_destroy();
  redirect_to_login();