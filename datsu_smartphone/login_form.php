<?php
session_start();

require_once '../classes/UserLogic.php';

$result = UserLogic::checkLogin();
if($result) {
    header('Location: mypage.php');
    return;
}

$err = $_SESSION;


// セッションを消す
$_SESSION = array();
session_destroy();


?>

<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
        <link rel = "stylesheet" href = "login_form.css">
        <title>脱スマホ！</title>
    </head>
    <body>
        <main>
            <div class = "login">
                <h1>脱スマホ！</h1>                
                <form action = "login.php" method = "POST">
                    <div class = "privatedata">
                        <p>メールアドレス</p>
                        <input class = "email" type = "email" name = "email">  
                        <?php if (isset($err['email'])) :?>
                            <p class = "alert"><?php echo $err['email'];?></p>
                        <?php endif;?>              
                        <p>パスワード</p>
                        <input class = "password" type = "password" name = "password"><br>
                        <?php if (isset($err['password'])) :?>
                            <p class = "alert"><?php echo $err['password'];?></p>
                        <?php endif;?> 
                        <?php if (isset($err['msg'])) :?>
                            <p class = "alert"><?php echo $err['msg'];?></p>
                        <?php endif;?> 
                        <br>
                        <div class = "button">
                            <input class = "login_button" type = "submit" value = "ログイン">
                        </div>
                    </div> 
                <div class = "new_login">
                    <a href = "new_login.php">※新規登録はこちら</a>
                </div>
            </div>
        </main>
    </body>
</html>