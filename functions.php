<?php

/**
 * XSS対策：エスケープ処理
 * 
 * @param string $str 対象の文字列
 * @return string 処理された文字列
 */
function h($str) {
    return htmlspecialchars($str, ENT_QUOTES,'UTF-8');
}

/**
 * CSRF対策
 * @param void
 * @return string $csrf_token
 */
function setToken() {
    // トークンを生成
    // フォームからそのトークンを送信
    // 送信後の画面でそのトークンを照会
    // トークンを消去
    $csrf_token = bin2hex(random_bytes(32));
    $_SESSION['csrf_token'] = $csrf_token;

    return $csrf_token;

}

// DBへの接続の関数化
// function connect_db() {
//     $param = 'mysql:dbname='.datsu_smartphone.';host'.localhost;
//     $pdo = new PDO($param,riku,Mayoineko1);
//     $pdo->query('SET NAMES utf8;');
//     return $pdo;
// }

// 日付を日(曜日)の形式に変換する
function time_format_dw($date) {
    $format_date = NULL;
    $week = array('日','月','火','水','木','金','土');
    if($date) {
        $format_date = date('j('.$week[date('w',strtotime($date))].')',strtotime($date));
    }

    return $format_date;
}

// 日付を加算するための関数を定義
function AddVtime($a,$b) {
    $aArry = explode(":",$a);
    $bArry = explode(":",$b);

    return
    date("H:i:s",mktime($aArry[0]+$bArry[0],$aArry[1]+$bArry[1],$aArry[2]+$bArry[2]));

}


?>