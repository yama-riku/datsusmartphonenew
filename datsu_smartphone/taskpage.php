<?php
session_start();
require_once '../classes/UserLogic.php';
require_once '../functions.php';


// ログインしているか判定し、していなかったら新規登録画面へ返す
$result = UserLogic::checkLogin();

if (!$result) {
    $_SESSION['login_err'] = 'ユーザー登録してください';
    header('Location: new_login.php');
    return;
}

$login_user = $_SESSION['login_user'];

// var_dump($login_user);　　←これでログイン出来てるか確認出来る(消しても問題ない)

// ストップウォッチの実装
date_default_timezone_set('Asia/Tokyo');

$start = "";
$stop = "";
if ($answer = filter_input(INPUT_POST,'answer')) {
    
  if ($_POST["answer"] == 1) {
    // テーブルのデータを消去
    $timer_delete =  UserLogic::deleteTimer($login_user['email']);
    
    // start時間を取得-------------------------------------
    $start_prepare =  new DateTime();
    $start = $start_prepare->format('Y-m-d H:i:s');
    // 下の出力後で消す
    // var_dump($start);


    //DB接続
    $pdo = connect();

    $timerData['email'] = $login_user['email'];
    $timerData['start'] = $start;
    $timerData['stop'] = NULL;
    //SQL呼び出し
    $timer =  UserLogic::insTimer($timerData);

    // "計測中"の文字呼び出し
    $output_cauculate = "計測中";
    
  }
  if ($_POST["answer"] == 2) {
    
    // SQL呼び出し(startの時間が登録されている)
    if($timerstop =  UserLogic::gettimerstop($login_user['email'])) {
        if($timerstop['stop'] == NULL) {
            // stop時間の記入------------------------------
            $stop_prepare =  new DateTime();
            $stop = $stop_prepare->format('Y-m-d H:i:s');
            
            // var_dump($stop);
            // DB接続
            $pdo = connect();
            $updateData['email'] = $login_user['email'];
            $updateData['stop'] = $stop;
            //SQL呼び出し
            $update =  UserLogic::updateTimer($updateData);
            
    
            // ストップウォッチの時間出力(start時間とstop時間の差分出力)-------------------------
            
            // タイマーテーブルからstartとstopの時間を持ってくる
            $timerstop =  UserLogic::gettimerstop($login_user['email']);
        
    
            //取得した時間を別の変数に入れる
            $start_time = $timerstop['start'];
            $stop_time = $timerstop['stop'];
            
            // 時間の差分を出していく
            function time_diff($start_time,$stop_time){
                // 初期化
                $diffTime = array();
    
                // タイムスタンプ
                $timestamp1 = strtotime($start_time);
                $timestamp2 = strtotime($stop_time);
                
    
                // タイムスタンプの差を計算
                $difseconds = $timestamp2 - $timestamp1;
                
                // 秒の差を取得
                $difftime['seconds'] = $difseconds % 60;
                // 分の差を取得
                $difminutes = ($difseconds - ($difseconds % 60)) /60;
                $difftime['minute'] = $difminutes % 60;
                // 時間の差を取得
                $difhours = ($difminutes - ($difminutes % 60)) / 60;
                $difftime['hours'] = $difhours;
    
                // 結果を返す
                return $difftime;
    
            }    
            // 初期化
            $difftimeoutput = array();
            // 関数を実行
            $difftimeoutput = time_diff($start_time,$stop_time);
            // 配列を逆にする
            $reversed = array_reverse($difftimeoutput);
            // 最終的な時間の出力
            $format = "%02d:%02d:%02d";
            $conclude = sprintf($format,$difftimeoutput['hours'],$difftimeoutput['minute'],$difftimeoutput['seconds']);
            

            // 当日の日付取得
            $yyyymm = date('Y-m-d');

            if($work_table = UserLogic::todaydata($login_user['email'])){
                if($work_table['date'] == $yyyymm){
                    // var_dump($work_table);
                    $totaltime = $work_table['time'];
                    
                    $new_totaltime = AddVtime($conclude,$totaltime);
                    // var_dump($new_totaltime);

                    // DB接続
                    $pdo = connect();
                    $newtotaltime['email'] = $login_user['email'];
                    $newtotaltime['time'] = $new_totaltime;
                    // var_dump($newtotaltime);
                    $update2 =  UserLogic::totalTimer($newtotaltime);
                    // var_dump($update2);

                }

            }else{
                //DB接続
                $pdo = connect();

                $today = '';
                $today = date('Y-m-d');

                $newdaytime['email'] = $login_user['email'];
                $newdaytime['date'] = $today;
                $newdaytime['time'] = $conclude;
                //SQL呼び出し
                $newdate_time =  UserLogic::newdaytime($newdaytime);
            
            }
            
    
    
        }else{
            $nonempty = [];
            $nonempty = "スタートボタンを押してください";
        }
    }else{
        $nonempty = [];
        $nonempty = "スタートボタンを押してください";
    
    }
    
  }  


  if ($_POST["answer"] == 3) {
    // テーブルのデータを消去
    $timer_delete =  UserLogic::deleteTimer($login_user['email']);
  }
    
}



?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <link rel = "stylesheet" href = "taskpage.css">
    <script src = "taskpage.js" defer></script>
    <title>タスクページ</title>
</head>
<body>
    <main>

        <div class = "user_name">
            <h1><?php echo h ($login_user['name']) ?>のページ</h1>
        </div>
        
        <div class = "select">

            <div class = "task">
                <button class = "tasks" onclick = "location.href = './taskpage.php'">TIMER</a>
            </div>
            <div class = "mypage">
                <button class = "mypages" onclick = "location.href = './mypage.php'">HOME</a>
            </div>
            <div class = "logout">
                <form action = "logout.php" method = "POST">
                    <input class = "logouts" type = "submit" name = "logout" value = "LOGOUT">
                </form>
            </div>
        </div>
    
        <div class = "timer">
            <?php if (isset($output_cauculate)) :?>
                <p class = "output_cauculate"><?php echo $output_cauculate;?></p>
            <?php endif;?> 
            <form name="sw" method="POST" action="taskpage.php">
            <input type="hidden" name="answer" value="1">
            <input type="submit" class  = "t_start" value="スタート" >
            </form>

            <form name="sw" method="POST" action="taskpage.php" >
            <input type="hidden" name="answer" value="2">
            <input type="submit" class = "t_stop" value="ストップ">
            </form>
            <?php if (isset($nonempty)) :?>
                <p class = "nonempty"><?php echo $nonempty;?></p>
            <?php endif;?> 

            <form name="sw" method="POST" action="taskpage.php" >
            <input type="hidden" name="answer" value="3">
            <input type="submit" class = "t_reset" value="リセット">
        </div>

        <div class = "pc_only">
            <?php if (isset($output_cauculate)) :?>
                <p ><?php echo $output_cauculate;?></p>
            <?php endif;?>
            <?php if (isset($nonempty)) :?>
                <p ><?php echo $nonempty;?></p>
            <?php endif;?> 
        </div>
        
    </main>
    <footer>
        <p><small>2022&copy;RIKU</small></p>
    </footer>
    
</body>
</html>

<?php


?>