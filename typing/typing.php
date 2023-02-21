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
    //$_SESSION['token'] = get_csrf_token(); // CSRFのトークンを取得する
    //追加しかしないため使わない

    $user_id = $_SESSION['user_id'];
    $score = isset($_POST['End_Score']) ? $_POST['End_Score'] : NULL;
    $submit = isset($_POST['End_Score_Submit']);

    if (!empty($submit)) {
        $pdo = new PDO(DNS, USER_NAME, PASSWORD, get_pdo_options());
        try {
            $sql = "INSERT INTO typing (user_id, score) VALUES (:user_id, :score)";
            $stmt = $pdo->prepare($sql);
            $pdo->beginTransaction();
            try {
                $stmt->bindValue(":user_id" , $user_id, PDO::PARAM_STR);
                $stmt->bindValue(":score" , $score, PDO::PARAM_STR);
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
    $sql = "SELECT * FROM typing WHERE user_id = ? ORDER BY score DESC LIMIT 1";
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
    <title>ランダムアルファベット打</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="../css/common.css">
    <link rel="stylesheet" href="../css/typing.css">
</head>
<body>
    <main>
        <div class="container">
            <div id="data_score_display" class="data-score-display">
                <?php $row = $stmt->fetch(PDO::FETCH_ASSOC); ?>
                <span class="user-id"><?php echo htmlspecialchars($user_id, ENT_QUOTES, "UTF-8") ?></span>
                さんの最高記録は
                <span class='data-score'>
                    <?php
                        if(isset($row['score'])){
                            echo $row['score'];
                        } else {
                            echo 0;
                        }
                    ?>
                </span>
                です。
            </div>
            <div class="timer-score-display">
                <div class="timer-display">
                    残り時間
                    <span class="timer" id="timer"></span>
                    秒
                </div>
                <div class="score-display">
                    スコア
                    <span class="score" id="score">0</span>
                </div>
            </div>
            <div class="game-display">
                <div id="string_display" class="string-display"></div>
            </div>
            <div class="input-display">
                <input id="string_input" class="string-input" autofocus autocomplete="off">
            </div>
            <div class="other-display">
                <div class="start-display">
                    <button id="start_button" class="start-button" onclick="start()">スタート</button>
                    Escepe もしくは Enter キーを押してください。
                </div>
                <form action="" method="post" class="score-form">
                    <input type="hidden" id="end_score" name="End_Score">
                    <input type="submit" id="end_score_submit" class="end-score-submit w-100 btn btn-lg btn-success" 
                            name="End_Score_Submit" value="ランキングに登録" onclick="score_submit()" disabled>
                </form>
                <div>
                    <button class="ranking" onclick="score_submit_confirm();">ランキング</button>
                </div>
                <div>
                    <button class="back" onclick="document.location.href='../sign_in/welcome.php';">戻る</button>
                </div>
            </div>
        </div>
    </main>
<script src="../javascript/typing.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>