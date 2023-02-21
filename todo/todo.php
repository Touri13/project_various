<?php
  session_start();
  require_once('../function.php');
  header('Content-type: text/html; charset=utf-8');
  //強制ブラウズはリダイレクト
  if (!isset($_SESSION['user_id'])){
    $_SESSION['error_status'] = 2;
    redirect_to_login();
    exit();
  }
  $_SESSION['token'] = get_csrf_token(); // CSRFのトークンを取得する

  $user_id = $_SESSION['user_id'];
  //$_POST = checkInput($_POST);
  $title = isset($_POST['title']) ? $_POST['title'] : NULL;
  $content = isset($_POST['content']) ? $_POST['content'] : NULL;
  $submit = isset($_POST['submit']) ? $_POST['submit'] : NULL;


  if (!empty($submit)) {
    $pdo = new PDO(DNS, USER_NAME, PASSWORD, get_pdo_options());
    try {
      $sql = "INSERT INTO todo (user_id, title, content) ";
      $sql .= "VALUES (:user_id, :title, :content)";
      $stmt = $pdo->prepare($sql);
      $pdo->beginTransaction();
      try {
        $stmt->bindValue(":user_id", $user_id, PDO::PARAM_STR);
        $stmt->bindValue(":title", $title, PDO::PARAM_STR);
        $stmt->bindValue(":content", $content, PDO::PARAM_STR);
        $stmt->execute();
        $pdo->commit();
      } catch (PDOException $e) {
        $pdo->rollBack();
        throw $e;
      }
    } catch (PDOException $e) {
      echo $e->getMessage();
      die();
    }
  }

  $pdo = new PDO(DNS, USER_NAME, PASSWORD, get_pdo_options());
  try {
    $sql = "SELECT * FROM todo WHERE user_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(1 , $user_id, PDO::PARAM_STR);
    $stmt->execute();
  } catch(PDOException $e) {
    echo $e->getMessage();
    die(); 
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
  <link rel="stylesheet" href="../css/todo.css">
</head>
<body>
  <div class="container list-display">
    <h1>to-doリスト</h1>
    <form class="form-todo" action="" method="POST">
      <input type="text" class="input-title-todo" name="title" placeholder="タイトル" required autofocus>
      <input type="text" class="input-content-todo" name="content" placeholder="本文" required>
      <input type="submit" class="input-submit-todo submit" name="submit" value="登録">
    </form>

    <table class="table table-striped">
      <thead>
        <tr class="scope">
          <th scope="col" width="25%">タイトル</th>
          <th scope="col">本文</th>
          <th scope="col" width="5%">削除</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) : ?>
          <tr>
            <form action="delete.php" method="post">
              <input type="hidden" name="token" value="<?php echo htmlspecialchars($_SESSION['token'], ENT_QUOTES, "UTF-8") ?>">
              <input type="hidden" name="todo_id" value="<?php echo $row['todo_id'] ?>">
              <td><?php echo htmlspecialchars($row["title"], ENT_QUOTES, "UTF-8"); ?></td>
              <td><?php echo htmlspecialchars($row["content"], ENT_QUOTES, "UTF-8"); ?></td>
              <td class="delete"><input type="submit" value="削除">
            </form>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>

    <form action="" method="post">
      <input type="button" value="戻る" onclick="document.location.href='../sign_in/welcome.php';">
    </form>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>