<?php
require_once 'env.php';
ini_set('display_errors',true);
function connect()
{
  $host = DB_HOST;
  $db   = DB_NAME;
  $user = DB_USER;
  $pass = DB_PASS;

  $dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";

  try {
    $pdo = new PDO($dsn,$user,$pass,[
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
      // $pdo = new PDO('mysql:dbname=datsu_smartphone;host=localhost;charset=utf8','riku','Mayoineko1');
      // $pdo = new PDO("mysql:host=localhost; dbname=datsu_smartphone;charset=utf8",'riku', 'Mayoineko1');
    ]);
    return $pdo;
  } catch (PDOException $e) {
    echo 'DB接続エラー！: ' . $e->getMessage();
    // error_log($e,3,'../error.log');
    exit();
  }
    
}




?>