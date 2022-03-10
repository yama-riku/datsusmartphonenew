<?php

session_start();

require_once '../functions.php';
require_once '../classes/UserLogic.php';

$result = UserLogic::checkLogin();

if($result) {
    header('Location:mypage.php');
    return;
}

$login_err = isset($_SESSION['login_err']) ? $_SESSION['login_err'] : null;
unset($_SESSION['login_err']);

?>


<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0,viewport-fit=cover">
    <link rel="stylesheet" href="new_login.css">
    <title>脱スマホ！</title>
</head>
<body>
    <main>
        <div class = "new_login">
            <div class = "login_sub">
                <h1>脱スマホ！</h1>
                <?php if (isset($login_err)) :?>
                        <p class = "alert"><?php echo $login_err;?></p>
                <?php endif;?>            
                <form action = "complete.php" method = "POST">
                    <div class = "privatedata">
                        <p>ユーザー名</p>
                        <input class = "userdata" type = "text" name = "username">
                        <p>メールアドレス</p>
                        <input class = "userdata" type = "email" name = "email">
                        <p>パスワード</p>
                        <input class = "userdata" type = "password" name = "password">
                        <p>パスワード確認</p>
                        <input class = "userdata" type = "password" name = "password_conf">
                    </div>
                    <input type="hidden" name="csrf_token" value="<?php echo h(setToken()); ?>">
                    <div class = "new_submit">
                        <input type = "submit" class = "input_user" value = "新規登録">
                    </div>
                </form>
            
                <div class ="return">
                    <a href = "login_form.php">※ログインする</a>
                </div>
            </div>
        </div>
    </main>
    

    
</body>
</html>