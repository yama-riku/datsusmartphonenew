<?php

require_once '../dbconnect.php';

class UserLogic
{
    /**
     * ユーザー登録する
     * @param array $userData
     * @return bool $result
     */
    public static function createUser($userData)
    {
        $result = false;

        $sql = 'INSERT INTO datsu_smartphone(name,email,
        password) VALUES (?,?,?)';

        // ユーザーデータを配列に加える
        $arr = [];
        $arr[] = $userData['username'];
        $arr[] = $userData['email'];
        $arr[] = password_hash($userData['password'],
        PASSWORD_DEFAULT);
        
        try {
            $stmt = connect()->prepare($sql);
            $result = $stmt->execute($arr);
            return $result;
        }catch(\Exception $e){
            return $result;
        }
    }

    /**
     * ログイン処理
     * @param string $email
     * @param string $password
     * @return bool $result
     */
    public static function login($email,$password)
    {
        // 結果
        $result = false;
        // ユーザーをemailから検索して取得
        $user = self::getUserByEmail($email);

        if(!$user) {
            $_SESSION['msg'] = 'emailが一致しません';
            return $result;
        }

        // パスワードの照会
        if (password_verify($password,$user['password'])) {
            // ログイン成功
            session_regenerate_id(true);
            $_SESSION['login_user'] = $user;
            $result = true;
            return $result;
        }

        $_SESSION['msg'] = 'パスワードが一致しません';
        return $result;

    }

    /**
    * emailからユーザを取得
    * @param string $email
    * @return array|bool $user|false
    */
    public static function getUserByEmail($email)
    {
      // SQLの準備
      // SQLの実行
      // SQLの結果を返す
      $sql = 'SELECT * FROM datsu_smartphone WHERE email = ?';

      // emailを配列に入れる
      $arr = [];
      $arr[] = $email;

      try{
          $stmt = connect()->prepare($sql);
          $stmt->execute($arr);
          //   SQLの結果を返す
          $user = $stmt->fetch();
          return $user;
      } catch(\Exception $e){
          return false;
      }
    }   
     
    
    /**
    * ログインチェック
    * @param void
    * @return bool $result
    */
    public static function checkLogin()
    {
        $result = false;

        // セッションにログインユーザーが入っていなかったらfalse
        if (isset($_SESSION['login_user']) && $_SESSION['login_user']['email'] != '') {
            return $result = true;
        }

        return $result;
    }

    // タイマーテーブルの消去
    public static function deleteTimer($deleteData){
        $sql = 'DELETE 
                  FROM timer
                 WHERE email = ?
                  ';

        //　タイマーデータを配列に加える
        $arr = [];
        $arr[] = $deleteData;

        try {
            $stmt = connect()->prepare($sql);
            $stmt->execute($arr);
            //sqlの結果を返す
            $user = $stmt->fetch();
            return $user;
          }catch(\Exception $e){
            return false;
          }

        
    }
    

    
    /**
     *タイマー時間取得１
     */
    public static function insTimer($timerData)
    {
      /**
     * 時間を登録する
     * @param array $timerData
     * @return bool $result
     */
      
      $result = false;

      $sql = 'INSERT INTO timer(email,start,stop) VALUES (?,?,?)';

      //　タイマーデータを配列に加える
      $arr = [];
      $arr[] = $timerData['email'];
      $arr[] = $timerData['start'];
      $arr[] = $timerData['stop'];
    
      try {
        $stmt = connect()->prepare($sql);
        $result = $stmt->execute($arr);
        return $result;
      }catch(\Exception $e){
        return $result;
      }

      
    }
    
    /**
     *タイマー時間取得２(startの時間の最新のレコードを取り出す)
     */
    public static function getTimerstop($timerstopData)
    {
      /**
     * 時間をデータから取り出して差分を出力する1
     * @param array $timerstopData
     * @return bool $result
     */
      

      $sql = 'SELECT timer.timer_id
                    ,timer.email
                    ,timer.start
                    ,timer.stop
                FROM timer
               WHERE timer.email = ?
               ORDER by timer.timer_id DESC
            ';

      //　タイマーデータを配列に加える
      $arr = [];
      $arr[] = $timerstopData;
    
      try {
        $stmt = connect()->prepare($sql);
        $stmt->execute($arr);
        //sqlの結果を返す
        $user = $stmt->fetch();
        return $user;
      }catch(\Exception $e){
        return false;
      }

      
    }
    
    /**
     *タイマー時間取得３(stopの時間の登録(アップデート登録))
     */
    public static function updateTimer($updateData)
    {
      /**
     * 時間をデータから取り出して差分を出力する2
     * @param array $updateData
     * @return bool $result
     */
      

      $sql = 'UPDATE timer
                 SET stop = ?
               WHERE timer.email = ?
            ';

      //　タイマーデータを配列に加える
      $arr = [];
      $arr[] = $updateData['stop'];
      $arr[] = $updateData['email'];


    
      try {
        $stmt = connect()->prepare($sql);
        $result = $stmt->execute($arr);        
        return $result;
      }catch(\Exception $e){
        return $result;
      }
      
    }

    /**
     *datsu_workテーブルにある当日のタスク合計時間を取得
     */
    public static function todaydata($todayData)
    {
      /**
     * 時間をデータから取り出す
     * @param array $todayData
     * @return bool $result
     */
      
      $today = '';
      $today = date('Y-m-d');
      
      // emailとdateは後で消す
      $sql = 'SELECT datsu_work.date 
                    ,datsu_work.time
                FROM datsu_work
               WHERE datsu_work.email = ? AND datsu_work.date = ?
            ';

      //　タイマーデータを配列に加える
      $arr = [];
      $arr[] = $todayData;
      $arr[] = $today;
    
      try {
        $stmt = connect()->prepare($sql);
        $stmt->execute($arr);
        //sqlの結果を返す
        $user = $stmt->fetch();
        return $user;
      }catch(\Exception $e){
        return false;
      }

      
    }

    /**
     *一日の新しい合計時間を入力(アップデート登録))
     */
    public static function totalTimer($newtotaltime)
    {
      /**
     * @param array $newtotaltime
     * @return bool $result
     */

      $today = '';
      $today = date('Y-m-d');

      $result = false;
        

      $sql = 'UPDATE datsu_work
                 SET time = ?
               WHERE datsu_work.email = ? AND datsu_work.date = ?
            ';

      //　タイマーデータを配列に加える
      $arr = [];
      $arr[] = $newtotaltime['time'];
      $arr[] = $newtotaltime['email'];
      $arr[] = $today;

    
      try {
        $stmt = connect()->prepare($sql);
        $result = $stmt->execute($arr);        
        return $result;
      }catch(\Exception $e){
        return $result;
      }
      
    }

    /**
     *タイマー時間取得１
     */
    public static function newdaytime($newdaytime)
    {
      /**
     * 時間を登録する
     * @param array $newdaytime
     * @return bool $result
     */

      $today = '';
      $today = date('Y-m-d');
      
      $result = false;

      $sql = 'INSERT INTO datsu_work(email,date,time) VALUES (?,?,?)';

      //　タイマーデータを配列に加える
      $arr = [];
      $arr[] = $newdaytime['email'];
      $arr[] = $today;
      $arr[] = $newdaytime['time'];
    
      try {
        $stmt = connect()->prepare($sql);
        $result = $stmt->execute($arr);
        return $result;
      }catch(\Exception $e){
        return $result;
      }

      
    }



    // mypage.phpの実装--------------------------------------------
    
    /**
     * ワークテーブルのデータを取得
     * @param array $workData
     * @return bool $result
     */
    
    public static function getworktable($workData)
    {
      // var_dump($yyyymm);
      $sql = 'SELECT datsu_work.date
                    ,datsu_work.email
                    ,datsu_work.time
                    ,datsu_work.comment
                    ,datsu_work.id
                FROM datsu_work
               WHERE datsu_work.email = ? 
            ';


      // 配列に入れる
      $arr = [];
      $arr[] = $workData;

      try{
          $stmt = connect()->prepare($sql);
          $stmt->execute($arr);
          //   SQLの結果を返す
          $user = $stmt->fetchall(PDO::FETCH_UNIQUE);
          return $user;
      } catch(\Exception $e){
          return false;
      }
    }

    /**
     *datsu_workテーブルにあるタスクデータ取得(時間修正時)
     */
    public static function editdataTime($editData,$editDate)
    {
      /**
     * 時間をデータから取り出す
     * @param array $editData
     * @return bool $result
     */
    
      
      $sql = 'SELECT datsu_work.date 
                    ,datsu_work.time
                FROM datsu_work
               WHERE datsu_work.email = ?
                 AND datsu_work.date = ?
            ';

      //　タイマーデータを配列に加える
      $arr = [];
      $arr[] = $editData;
      $arr[] = $editDate;
    
      try {
        $stmt = connect()->prepare($sql);
        $stmt->execute($arr);
        //sqlの結果を返す
        $user = $stmt->fetch();
        return $user;
      }catch(\Exception $e){
        return false;
      }

      
    }

    /**
     *編集した新しい時間入力(アップデート登録)
     */
    public static function updateTime($update_time)
    {
      /**
     * @param array $update_time
     * @return bool $result
     */


      $result = false;
        

      $sql = 'UPDATE datsu_work
                 SET time = ?
               WHERE datsu_work.email = ? AND datsu_work.date = ?
            ';

      //　配列に加える
      $arr = [];
      $arr[] = $update_time['time'];
      $arr[] = $update_time['email'];
      $arr[] = $update_time['date'];

    
      try {
        $stmt = connect()->prepare($sql);
        $result = $stmt->execute($arr);        
        return $result;
      }catch(\Exception $e){
        return $result;
      }
      
    }

    /**
     *入力されてない日付のコメントを入力する
     */
    public static function newTime($newtime)
    {
      /**
     * 時間を登録する
     * @param array $newtime
     * @return bool $result
     */
      
      $result = false;

      $sql = 'INSERT INTO datsu_work(email,date,time) VALUES (?,?,?)';

      //　タイマーデータを配列に加える
      $arr = [];
      $arr[] = $newtime['email'];
      $arr[] = $newtime['date'];
      $arr[] = $newtime['time'];
    
      try {
        $stmt = connect()->prepare($sql);
        $result = $stmt->execute($arr);
        return $result;
      }catch(\Exception $e){
        return $result;
      }

      
    }


    /**
     *datsu_workテーブルにあるタスクデータ取得(コメント修正時)
     */
    public static function editdata($editData,$editDate)
    {
      /**
     * 時間をデータから取り出す
     * @param array $editData
     * @return bool $result
     */
    
      
      $sql = 'SELECT datsu_work.date 
                    ,datsu_work.comment
                FROM datsu_work
               WHERE datsu_work.email = ?
                 AND datsu_work.date = ?
            ';

      //　タイマーデータを配列に加える
      $arr = [];
      $arr[] = $editData;
      $arr[] = $editDate;
    
      try {
        $stmt = connect()->prepare($sql);
        $stmt->execute($arr);
        //sqlの結果を返す
        $user = $stmt->fetch();
        return $user;
      }catch(\Exception $e){
        return false;
      }

      
    }


    /**
     *編集した新しいコメント入力(アップデート登録)
     */
    public static function updateComment($update_comment)
    {
      /**
     * @param array $update_comment
     * @return bool $result
     */


      $result = false;
        

      $sql = 'UPDATE datsu_work
                 SET comment = ?
               WHERE datsu_work.email = ? AND datsu_work.date = ?
            ';

      //　配列に加える
      $arr = [];
      $arr[] = $update_comment['comment'];
      $arr[] = $update_comment['email'];
      $arr[] = $update_comment['date'];

    
      try {
        $stmt = connect()->prepare($sql);
        $result = $stmt->execute($arr);        
        return $result;
      }catch(\Exception $e){
        return $result;
      }
      
    }


    /**
     *入力されてない日付のコメントを入力する
     */
    public static function newComment($newcomment)
    {
      /**
     * 時間を登録する
     * @param array $newcomment
     * @return bool $result
     */
      
      $result = false;

      $sql = 'INSERT INTO datsu_work(email,date,comment) VALUES (?,?,?)';

      //　タイマーデータを配列に加える
      $arr = [];
      $arr[] = $newcomment['email'];
      $arr[] = $newcomment['date'];
      $arr[] = $newcomment['comment'];
    
      try {
        $stmt = connect()->prepare($sql);
        $result = $stmt->execute($arr);
        return $result;
      }catch(\Exception $e){
        return $result;
      }

      
    }

    




    /**
     * ログアウト処理
     */
    public static function logout()
    {
        $_SESSION = array();
        session_destroy();
    }
}




?>