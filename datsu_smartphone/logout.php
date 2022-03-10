<?php
session_start();
require_once '../classes/UserLogic.php';

if (!$logout = filter_input(INPUT_POST, 'logout'))
{
    exit('不正なリクエストです');
}

// ログインしているか判定し、セッションが切れていたらログインしてくださいとメッセージを出す。
$result = UserLogic::checkLogin();

if (!$result) {
    exit('セッションが切れましたので、ログインし直してください。');
}

// ログアウトする
UserLogic::logout();


?>

<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
        <link rel = "stylesheet" href = "logout.css">
        <title>ログアウト</title>
    </head>
    <body>
        <main>
            <div class = "logout">
                <h1>ログアウト完了</h1>
                <a href = "login_form.php">ログイン画面へ</a>
            </div>
        </main>
    </body>
</html>