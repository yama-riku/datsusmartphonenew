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

//DB接続
$pdo = connect();
// タスクデータを取得
$work_table =  UserLogic::getworktable($login_user['email']);



// 月別プルダウンの実装------------------------
// タスクデータをテーブルにリスト表示
$day_count = date('t');
$yyyymm = date('Y-m');


// 月別プルダウン表示
if (isset($_GET['m'])) {
    $yyyymm = $_GET['m'];
    $day_count = date('t',strtotime($yyyymm));
}else {
    $yyyymm = date('Y-m');
    $day_count = date('t');
}

// メモ欄編集実装------------------------------
// 年月が取得できているか
if ( isset( $_POST['edit_month'] ) ) {
    $edit_month = $_POST['edit_month'];
    
    // 日にちが取得できているか
    if( isset( $_POST['edit_day'] )) {
        $edit_day = $_POST['edit_day'];
        
        // 入力した日付が正しいかどうかチェック
        if($edit_day <= $day_count) {
            
            $edit_date = $edit_month.'-'.$edit_day;
            
            // 時間が入力されているか（空でもいい）
            if( isset($_POST['edit_hour'],$_POST['edit_minute'],$_POST['edit_second'])) {

                // 各値をそれぞれ変数に入れる
                $edit_hour = $_POST['edit_hour'];
                $edit_minute = $_POST['edit_minute'];
                $edit_second = $_POST['edit_second'];
                
                // $edit_hourの値が24未満であるか判定 
                if($edit_hour < 24) {
                    // データベースに保存するためにフォーマットを合わせる
                    $format = "%02d:%02d:%02d";
                    $edit_time = sprintf($format,$edit_hour,$edit_minute,$edit_second);
                    // データベースから該当する日付の時間を取ってくる
                    $editDataTime = UserLogic::editdataTime($login_user['email'],$edit_date);

                    if(isset($editDataTime['date'])) {

                        // updateの実行
                        $pdo = connect();
                        $update_time['time'] = $edit_time;
                        $update_time['email'] = $login_user['email'];
                        $update_time['date'] = $editDataTime['date'];
                        $updatetime = UserLogic::updateTime($update_time);

                        // ページに反映
                        $work_table = UserLogic::getworktable($login_user['email']);

                    }else {

                        // 新規入力もできるようにする
                        $pdo =connect();

                        $newtime['email'] = $login_user['email'];
                        $newtime['date'] = $edit_date;
                        $newtime['time'] = $edit_time;

                        $new_time = UserLogic::newTime($newtime);

                        // ページに反映
                        $work_table = UserLogic::getworktable($login_user['email']);



                    }
                    
                    



                }else {
                    $correct_day = '※正しい時間を入力して下さい';    
                }
                
            }else {
                $correct_day = '※正しい時間を入力して下さい';

            }

            
            // コメントが入力されているか（空でも良い）
            if ( isset( $_POST['edit_comment'] ) ) {
                $edit_comment = $_POST['edit_comment'];
                
                $edit_Data = UserLogic::editdata($login_user['email'],$edit_date);
                
                if(isset($edit_Data['date'])) {
                    
                    // update実行
                    $pdo = connect();
                    $update_comment['comment'] = $edit_comment;
                    $update_comment['email'] = $login_user['email'];
                    $update_comment['date'] = $edit_Data['date'];
                    $updatecomment = UserLogic::updateComment($update_comment);

                    // ページに反映
                    $work_table =  UserLogic::getworktable($login_user['email']);


                }else{
                
                    // まだ入力されたことがない新しいコメントを入力
                    $pdo = connect();

                    $newcomment['email'] = $login_user['email'];
                    $newcomment['date'] = $edit_date;
                    $newcomment['comment'] = $edit_comment;

                    $new_comment = UserLogic::newComment($newcomment);
                    
                    $work_table =  UserLogic::getworktable($login_user['email']);

                }
                

                
            }

        }else{
            $correct_day = ' ※正しい日付を入力して下さい';
        }   

    }else{
        
        $correct_day = ' ※正しい日付を入力して下さい';
    }
}

?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <link rel = "stylesheet" href = "mypage.css">
    <link href="https://use.fontawesome.com/releases/v5.15.1/css/all.css" rel="stylesheet">
    <link rel=”stylesheet” href=”https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css”>
    <title>マイページ</title>
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
        <form action = "" method = "POST">
            <div class = "edit">
                <div class = "edit_header">
                    <h1>記録・編集</h1>
                </div>
                <div class = "edit_semiheader">
                    <h2><?=$yyyymm?>-</h2>
                    <input type = "hidden" name = "edit_month" value = "<?=$yyyymm?>">
                    <input type = "tel" class = "input_day" name = "edit_day" maxlength = "2" placeholder = "日にち入力" value = "">
                    <input type = "tel" class = "input_hour" name = "edit_hour" maxlength="2" placeholder = "00" value = "">
                    <p>:</p>
                    <input type = "tel" class = "input_minute" name = "edit_minute" maxlength="2" placeholder = "00" value = "">
                    <p>:</p>
                    <input type = "tel" class = "input_second" name = "edit_second" maxlength="2" placeholder = "00" value = "">
                    <div class = pcOnly>
                        <?php if (isset($correct_day)) :?>
                            <p class = "correct_day"><?php echo $correct_day;?></p>
                        <?php endif;?> 
                    </div>         
                </div>
                <div class = sptOnly>
                    <?php if (isset($correct_day)) :?>
                        <p class = "correct_day"><?php echo $correct_day;?></p>
                    <?php endif;?> 
                </div>
                <textarea class = "input_comment" name = "edit_comment" maxlength = "60" placeholder = "60文字以内で入力してください" rows = "4" cols = "40"></textarea><br>
                <input type = "submit" class = "input_edit" value = "変更">
            </div>
        </form>
        <form class = "border rounded bg-white form-time-table" action = "mypage.php">            
            <select class = "form-control rounded-pill mb-3" name="m" onchange="submit(this.select)">
                <option value="<?=date('Y-m')?>"><?= date('Y/m')?></option>
                <?php for ($i = 1; $i < 12; $i++):?>
                    <?php $target_yyyymm = strtotime("-{$i}months");?>
                    <option value="<?= date('Y-m',$target_yyyymm)?>"<?php if($yyyymm == date('Y-m',$target_yyyymm)) echo 'selected'?>><?=date('Y/m',$target_yyyymm)?></option>
                <?php endfor;?>
            </select>
            <table class = "table table-bordered">
                <thead>
                    <tr class = "column">
                        <th class="fix-col">日</th>
                        <th class="fix-col">時間</th>
                        <th>メモ</th>
                        <th class="fix-col"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php for($i = 1;$i <= $day_count;$i++):?>
                        <?php
                            $time = '';
                            $comment = '';
                            $maru_prepare = '';
                            $maru = '';
                            if(isset($work_table[date('Y-m-d',strtotime($yyyymm.'-'.$i))])) {
                                $work = $work_table[date('Y-m-d',strtotime($yyyymm.'-'.$i))];
                                
                                if($work['time']) {
                                    $time = date('H:i:s',strtotime($work['time']));
                                    $maru_prepare = date('H',strtotime($work['time']));
                                } 
                                
                                if($work['comment']) {
                            
                                    $comment = $work['comment'];
                                } 

                            }
                            
                            
                        ?>
                    <tr>
                        <th scope="row"><?= time_format_dw($yyyymm.'-'.$i)?></th>
                        <td><?=$time?></td>
                        <td class = "comment_break"><?=$comment?></td>
                        <td>
                            <?php if ($maru_prepare >= '08'):?>
                                <p class = "aka"><?= $maru = "●";?></p>
                            <?php elseif($maru_prepare >= '04'):?>
                                <p class = "kiro"><?= $maru = "●";?></p>
                            <?php elseif($maru_prepare >= '00'):?>
                                <p class = "midori"><?= $maru = "●";?><p>
                            <?php endif;?>

                        </td>
                        
                    </tr>
                    <?php endfor;?>
                </tbody>
            </table>
        </form>

        <div class = footer>
            <p><small>2022&copy;RIKU</small></p>                    
        </div>

        
    </main>
</body>
</html>
