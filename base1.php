<?php

SESSION_start();

class DB
{
  // 設定屬性
  private $dsn = "mysql:host=localhost;charset=utf8;dbname=invoice";
  private $root = "root";
  private $password = "";
  private $table;
  private $pdo;

  //設定建構式
  public function __construct($table)
  {
    // 將傳入的資料表名稱指定給類別內的資料表變數$table
    $this->table = $table;

    // 建立pdo連線，並將pdo連線指定給類別內的變數pdo
    $this->pdo = new PDO($this->dsn, $this->root, $this->password);
  }


  //取得全部資料
  public function all(...$arg)
  {
    $sql = "SELECT * FROM $this->table WHERE ";
    // 判斷第一個參數是否存在且為陣列
    if (!empty($arg[0]) && is_array($arg[0])) {
      foreach ($arg[0] as $key => $value) {
        $tmp[] = sprintf("`%s`='%s'", $key, $value);
      }
      $sql = $sql . implode(" && ", $tmp);
    }
    // 如果第二個參數存在，則規定為SQL語法字串，直接接在第一個參數後面
    if (!empty($arg[1])) {
      $sql = $sql . $arg[1];
    }
    // 以fetchAll的方式回傳查詢結果
    return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
  }


  // 取得單筆資料
  public function find($arg)
  {
    $sql = "SELECT * FROM $this->table WHERE ";

    //判斷參數是否為陣列
    if (is_array($arg)) {
      foreach ($arg as $key => $value) {
        $tmp[] = sprintf("`%s`='%s'", $key, $value);
      }
      $sql = $sql . implode(" && ", $tmp);
    } else {
      //將參數設定為ID，直接建立SQL語句字串
      $sql = $sql . "`id`='$arg'";
    }
    // 回傳查詢後的結果並取得單筆資料，並排除數字索引值
    return $this->pdo->query($sql)->fetch(PDO::FETCH_ASSOC);
  }


  //計算筆數
  public function counts(...$arg)
  {
    $sql = "SELECT count(*) FROM $this->table WHERE ";

    // 基本上這邊跟all()是相當相似的，第一參數需要是一個陣列
    if (!empty($arg[0]) && is_array($arg[0])) {
      foreach ($arg[0] as $key => $value) {
        $tmp[] = sprintf("`%s`='%s'", $key, $value);
      }
      $sql = $sql . implode(" && ", $tmp);
    }
    if (!empty($arg[1])) {
      // 如果第二參數存在，直接接在SQL語法字串後面
      $sql = $sql . $arg[1];
    }
    // 以fetchColumn()的方式回傳查詢指定參數的筆數
    return $this->pdo->query($sql)->fetchColumn();
  }


  // 新增或編輯資料
  public function save($arg)
  {
    // 直接規定參數必須為陣列
    if (is_array($arg)) {
      // 更新
      if (!empty($arg['id'])) {
        foreach ($arg as $key => $value) {
          if ($key != 'id') {
            $tmp[] = sprintf("`%s`='%s'", $key, $value);
          }
        }
        $sql = "update $this->table set " . implode(",", $tmp) . " where `id`='" . $arg['id'] . "'";
      } else {
        // 新增
        $sql = "insert into $this->table (`" . implode("`,`", array_keys($arg)) . "`) values('" . implode("','", $arg) . "')";
      }
    }
    // 此處回傳值為影響的筆數，大於1表示至少新增或更新超過1筆
    return $this->pdo->exec($sql);
  }


  // 刪除資料
  public function del($arg)
  {
    // 基本SQL語法字串
    $sql = "delete from $this->table where ";
    if (is_array($arg)) {
      foreach ($arg as $key => $value) {
        $tmp[] = sprintf("`%s`='%s'", $key, $value);
      }
      $sql = $sql . implode(" && ", $tmp);
    } else {
      // 若不是陣列，則設定為ID
      $sql = $sql . "`id`='$arg'";
    }
    // 此處回傳值為影響的筆數，大於1表示刪除超過1筆
    return $this->pdo->exec($sql);
  }


  //萬用語法，此函式只是籍由類別中的pdo來執行sql語句
  //因此即使在sql中要直接查詢其它資料表的內容也是可以的
  function q($sql)
  {
    //以fetchAll的方式回傳查詢結果
    return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
  }
}

// 頁面導向
function to($url)
{
  header('location:' . $url);
}

//判斷進站人數是否要增加
if (empty($_SESSION['visited'])) {
  $total = new DB('total');
  $tt = $total->find(1);
  $tt['total']++;
  $total->save($tt);
  $_SESSION['visited'] = 1;
}
