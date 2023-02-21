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

    $user_id = $_SESSION['user_id'];

    $pdo = new PDO(DNS, USER_NAME, PASSWORD, get_pdo_options());
    try {
        $sql = "SELECT * FROM typing ORDER BY score DESC LIMIT 100";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
    } catch(PDOException $e) {
        echo $e->getMessage();
        die(); 
    }
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ランキング</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="../css/common.css">
</head>
<body>
    <div class="container list-display">
        <div>
            <button class="back" onclick="document.location.href='typing.php';">戻る</button>
        </div>
        <table class="table table-bordered table-striped border-success p-2">
            <thead>
                <tr class="scope">
                    <th scope="col" width="7%">順位</th>
                    <th scope="col" width="30%">ユーザー</th>
                    <th scope="col" width="10%">スコア</th>
                </tr>
            </thead>
            <tbody>
                <?php for ($i = 1; $row = $stmt->fetch(PDO::FETCH_ASSOC); $i++) : ?>
                <tr>
                    <td><?php echo $i; ?></td>
                    <td><?php echo htmlspecialchars($row["user_id"], ENT_QUOTES, "UTF-8"); ?></td>
                    <td><?php echo $row["score"]; ?></td>
                </tr>
                <?php endfor; ?>
            </tbody>
        </table>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script></body>
</html>