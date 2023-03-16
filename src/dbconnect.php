<?php
$dsn = 'mysql:host=db;dbname=posse;charset=utf8;';
$user = 'root';
$password = 'root';

try {
  $db = new PDO($dsn, $user, $password);
  $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
  echo '接続失敗: ' . $e->getMessage();
  exit();
}

// $sql = 'SELECT * FROM questions';
// foreach ($db->query($sql) as $row) {
//   print_r($row);
// }

// $questions = $db->query("SELECT * FROM questions")->fetchAll(PDO::FETCH_ASSOC);
// $choices = $db->query("SELECT * FROM choices")->fetchAll(PDO::FETCH_ASSOC);

#array_column($questions, 'id')で[0]=>1のようになる
// foreach ($choices as $key => $choice) {
//   $index = array_search($choice["question_id"], array_column($questions, 'id')); 
//   $questions[$index]["choices"][] = $choice;
//   print_r($questions);
// }
