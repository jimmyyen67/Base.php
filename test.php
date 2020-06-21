<?php
include_once "base1.php";
$table = 'invoice';
$db = new DB($table);

echo "<hr>";
echo "<h2> Practice: to() </h2>";
echo "測試成功，註解起來。";
// to("https://www.google.com");


echo "<hr>";
echo "<h2> Practice: q() </h2>";
$sql = "select * from prizenumber";
$q = $db->q($sql);
foreach($q as $q){
  echo "<pre>";
  print_r($q);
  echo "</pre>";
}


echo "<hr>";
echo "<h2> Practice: del() </h2>";
echo "測試成功，註解起來。";
// 【方法一】：使用陣列去安排要刪除的指定資料
// $del = [];
// $del['code'] = 'JZ';
// $del['number'] = '09885557';
// echo "<pre>";
// print_r($del);
// echo "</pre>";
// 【方法二】：直接使用ID去刪除指定資料
// $del = '4';
// $delete = $db->del($del);
// echo $delete;

echo "<hr>";
echo "<h2> Practice: add() </h2>";
echo "測試成功，註解起來。";
// $add['year'] = '2020';
// $add['period'] = '3';
// $add['code'] = 'JZ';
// $add['number'] = '09885557';
// $add['expend'] = '599';
// echo "<pre>";
// print_r($add);
// echo "</pre>";
// $res = $db -> save($add);
// echo $res;

echo "<hr>";
echo "<h2> Practice: update() </h2>";
echo "測試成功，註解起來。";
// $invoice  = $db->find(1);
// $invoice['code'] = 'PL';
// $update = $db->save($invoice);
// echo $update;

echo "<hr>";
echo "<h2> Practice: count() </h2>";
$counts = $db->counts(['period' => '2']);
echo $counts;

echo "<hr>";
echo "<h2> Practice: find() </h2>";
$row = $db->find('7');
echo "<pre>";
print_r($row);
echo "</pre>";
$row = $db->find(['number' => '92151169']);
echo "<pre>";
print_r($row);
echo "</pre>";

echo "<hr>";
echo "<h2> Practice: all() </h2>";
$rows = $db->all(['period' => 1], "order by id desc");
foreach ($rows as $row) {
  echo "<pre>";
  print_r($row);
  echo "</pre>";
}
