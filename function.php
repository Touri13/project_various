<?php
define('DNS','mysql:host=localhost;dbname=project_various;charset=utf8');
/*
define('USER_NAME', 'mysql');
define('PASSWORD', 'Mysql@1234');
define('SERVER', '192.168.33.10');
define('SENDER_EMAIL', 'admin@example.com');
*/
define('USER_NAME', 'user');
define('PASSWORD', 'H3E80iHUKBwD8K10');
define('SERVER', 'localhost/project_various');
define('SENDER_EMAIL', '1545ito1545@gmail.com');

/*
* PDO の接続オプション取得
例外処理を表示する（デバッグしやすくなる）
SQLの複文禁止（SQLインクジェクション対策）
静的プレースホルダにしている
（SQLインクジェクション対策、副次的に複文禁止となる）
*/
function get_pdo_options() {
  return array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
               PDO::MYSQL_ATTR_MULTI_STATEMENTS => false,
               PDO::ATTR_EMULATE_PREPARES => false);
}
/*
* CSRF トークン作成
引数桁の疑似ランダムなバイト文字列を生成
文字列を16進表記のASCIIで表す
*/
function get_csrf_token() {
 $token_legth = 16;//16*2=32byte
 $bytes = openssl_random_pseudo_bytes($token_legth);
 return bin2hex($bytes);
}
/*
* URL の一時パスワードを作成
*/
function get_url_password() {
  $token_legth = 16;//16*2=32byte
  $bytes = openssl_random_pseudo_bytes($token_legth);
  return hash('sha256', $bytes);
}
/*
* ログイン画面へのリダイレクト
「永久的な移転」を表すコードで、ページを移転したので
旧アドレスには二度と戻ってこないということを表すコードです。
このコードを用いて新アドレスに移転することで、旧アドレスでの今までの検索エンジンの
評価を受け継ぎ、旧ページが検索結果に出ないようにはじくことができます。
*/
function redirect_to_login() {
  header('HTTP/1.1 301 Moved Permanently');
  header('Location: ../index.php');
}
// mainからのログイン画面へのリダイレクト
/*
function redirect_to_login_from_main() {
  header('HTTP/1.1 301 Moved Permanently');
  header('Location: ../sign_in/login.php');
}
*/
/*
* パスワードリセット画面へのリダイレクト
*/
function redirect_to_password_reset() {
  header('HTTP/1.1 301 Moved Permanently');
  header('Location: password_reset.php');
}
/*
* Welcome画面へのリダイレクト
*/
function redirect_to_welcome() {
  header('HTTP/1.1 301 Moved Permanently');
  header('Location: welcome.php');
}
/*
* 登録画面へのリダイレクト
*/
function redirect_to_register() {
  header('HTTP/1.1 301 Moved Permanently');
  header('Location: register.php');
}

//入力値に不正なデータがないかなどをチェックする関数
function checkInput($var){
  if(is_array($var)){
      //$var が配列の場合、checkInput()関数をそれぞれの要素について呼び出す
      return array_map('checkInput', $var);
  }else{
      //PHP 7.4.x で get_magic_quotes_gpc() は非推奨になりました
      //php.iniでmagic_quotes_gpcが「on」の場合の対策
      /*if(get_magic_quotes_gpc()){  
          $var = stripslashes($var);
      }*/
      //NULLバイト攻撃対策
      if(preg_match('/\0/', $var)){  
          die('不正な入力です。');
      }
      //文字エンコードのチェック
      if(!mb_check_encoding($var, 'UTF-8')){ 
          die('不正な入力です。');
      }
      //改行以外の制御文字及び最大文字数のチェック
      if(preg_match('/\A[\r\n[:^cntrl:]]{0,100}\z/u', $var) === 0){  
          die('不正な入力です。最大文字数は100文字です。また、制御文字は使用できません。');
      }
      return $var;
  }
}