<?php

SESSION_start();

//建立一個DB的類別
class DB
{

    //設定屬性
    private $dsn = "mysql:host=localhost;charset=utf8;dbname=db01";
    private $root = "root";
    private $password = "";
    private $table;
    private $pdo;

    //設定建構式
    public function __construct($table)
    {
        //將傳入的資料表名稱指定給類別內的資料表變數table
        $this->table = $table;

        //建立pdo的連線資訊，並將pdo連線指定給類別內的變數pdo
        $this->pdo = new PDO($this->dsn, $this->root, $this->password);
    }

    //取得全部資料的函式
    public function all(...$arg)
    {
        //建立基本sql語法
        $sql = "select * from $this->table ";

        //判斷第一個參數是否存在同時是否為陣列
        //我們自行規範第一個參數必須為陣列才進行處理
        //如果第一個參數不是陣列,則跳過不處理
        if (!empty($arg[0]) && is_array($arg[0])) {
            foreach ($arg[0] as $key => $value) {
                $tmp[] = sprintf("`%s`='%s'", $key, $value);
            }

            //組合條件語句字串
            $sql = $sql . " where " . implode(" && ", $tmp);
        }

        //如果第二個參數存在，
        //則我們規定第二個參數為一個sql的語法字串
        //因此直接將語法字串接在$sql之後即可
        if (!empty($arg[1])) {
            $sql = $sql . $arg[1];
        }

        //以fetchAll的方式回傳查詢的結果
        return $this->pdo->query($sql)->fetchAll();
    }


    //取得單筆資料
    public function find($arg)
    {
        //建立基本sql語法
        $sql = "SELECT * from $this->table WHERE ";

        //判斷參數是否為陣列，
        //如果參數為陣列，則將陣列處理成where條件語句
        //如果參數不是陣列，則視為資料id值
        if (is_array($arg)) {

            //利用迴圈及sprintf()來建立條件的字串陣列
            foreach ($arg as $key => $value) {
                $tmp[] = sprintf("`%s`='%s'", $key, $value);
            }

            //將字串陣列組合成條件語句字串
            $sql = $sql . implode(" && ", $tmp);
        } else {

            //將參數視為id，並建立起條件語句字串
            $sql = $sql . "`id`='$arg'";
        }
        //回傳查詢後的結果，並限定只取得單筆資料，同時排除索引資料
        return $this->pdo->query($sql)->fetch(PDO::FETCH_ASSOC);
    }

    //計算筆數
    public function count(...$arg)
    {

        //建立基本sql語法
        $sql = "select count(*) from $this->table ";


        //判斷第一個參數是否存在同時是否為陣列
        //我們自行規範第一個參數必須為陣列才進行處理
        //如果第一個參數不是陣列,則跳過不處理
        if (!empty($arg[0]) && is_array($arg[0])) {

            foreach ($arg[0] as $key => $value) {
                $tmp[] = sprintf("`%s`='%s'", $key, $value);
            }

            //組合條件語句字串
            $sql = $sql . " where " . implode(" && ", $tmp);
        }

        //如果第二個參數存在，
        //則我們規定第二個參數為一個sql的語法字串
        //因此直接將語法字串接在$sql之後即可
        if (!empty($arg[1])) {
            $sql = $sql . $arg[1];
        }

        //以fetchColumn的方式回傳查詢的結果
        return $this->pdo->query($sql)->fetchColumn();
    }

    //新增/更新資料
    //這裏我們規定參數必須為陣列，如果不是陣列則此函式會直接報錯
    public function save($arg)
    {

        //藉由判斷陣列中是否有key為id的資料來決定要進行的動作是新增還是更新
        if (!empty($arg['id'])) {
            //更新
            //update $this->table set xxx=yyy  where `id`='xxx'
            //使用迴圈來將陣列中的key ,value兩個值組合成sql用得到的語法字串
            foreach ($arg as $key => $value) {
                //排除key為id的資料不處理
                if ($key != 'id') {
                    $tmp[] = sprintf("`%s`='%s'", $key, $value);
                }
            }

            //組合成sql的語句字串
            $sql = "update $this->table set " . implode(",", $tmp) . " where `id`='" . $arg['id'] . "'";
        } else {
            //新增
            //insert into $this->table (``,``,``) values('','','')
            //組合key和value兩個陣列為新增用的sql語句字串
            $sql = "insert into $this->table (`" . implode("`,`", array_keys($arg)) . "`) values('" . implode("','", $arg) . "')";
        }

        //執行sql語句，此處的回傳值為語句影響的筆數，
        //大於1表示新增了至少1筆資料或至少更新了一筆資料
        return $this->pdo->exec($sql);
    }

    //刪除資料
    public function del($arg)
    {

        //建立基本sql語法
        $sql = "delete from $this->table ";

        //判斷參數是否為陣列，
        //如果參數為陣列，則將陣列處理成where條件語句
        //如果參數不是陣列，則視為資料id值
        if (is_array($arg)) {

            //利用迴圈及sprintf()來建立條件的字串陣列
            foreach ($arg as $key => $value) {
                $tmp[] = sprintf("`%s`='%s'", $key, $value);
            }

            //將字串陣列組合成條件語句字串
            $sql = $sql . " where " . implode(" && ", $tmp);
        } else {

            //將參數視為id，並建立起條件語句字串
            $sql = $sql . " where `id`='$arg'";
        }

        //echo $sql;
        //執行sql語句，此處的回傳值為語句影響的筆數，
        //大於1表示刪除了至少1筆資料
        return $this->pdo->exec($sql);
    }

    //萬用語法，此函式只是籍由類別中的pdo來執行sql語句
    //因此即使在sql中要直接查詢其它資料表的內容也是可以的
    function q($sql)
    {

        //以fetchAll的方式回傳查詢的結果
        return $this->pdo->query($sql)->fetchAll();
    }
}

//頁面導向
function to($url)
{
    header("location:" . $url);
}

//登入狀態與否的判斷
if (empty($_SESSION['visited'])) {
    $total = new DB('total');
    $tt = $total->find(1);
    $tt['total']++;
    $total->save($tt);
    $_SESSION['visited'] = 1;
}
