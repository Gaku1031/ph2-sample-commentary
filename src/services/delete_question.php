<?php
require('../db/pdo.php');

$pdo = Database::get();
//削除処理なのでrollbackできるようにトランザクション
$pdo->beginTransaction();
try {
  $sql = "DELETE FROM choices WHERE question_id = :question_id";
  $stmt = $pdo->prepare($sql);
  //urlのid=のところから取得するので$_REQUESTよりも＄＿GETの方が良い？？
  $stmt->bindValue(":question_id", $_GET["id"]);
  // $stmt->bindValue(":question_id", $_REQUEST["id"]);
  $stmt->execute();

  $sql = "DELETE FROM questions WHERE id = :id";
  $stmt = $pdo->prepare($sql);
  //urlのid=のところから取得するので$_REQUESTよりも＄＿GETの方が良い？？
  $stmt->bindValue(":id", $_GET["id"]);
  // $stmt->bindValue(":id", $_REQUEST["id"]);
  $stmt->execute();
  $pdo->commit();
  // 成功したら204を返す
  //HTTP1.1で調べると色々出てくる（参考URL: https://qiita.com/hirooka0527/items/13767855358f83db5e02)
  header("HTTP/1.1 204 OK");
} catch(Error $e) {
  $pdo->rollBack();
  header("HTTP/1.1 500 OK");
}

header("Content-Type: application/json; charset=utf-8");
