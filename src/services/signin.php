<?php
require_once(dirname(__FILE__) . '/../db/pdo.php');
require(dirname(__FILE__) . '/../response/create_response.php');

//file_get_contentsでファイルの内容を全て文字列に読み込む
// php://inputは読み取り専用のストリーム
// 参考サイト: https://hacknote.jp/archives/47898/
$raw = file_get_contents('php://input');
$data = (array)json_decode($raw);

$pdo = Database::get();
$sql = "SELECT * FROM users WHERE email = :email";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(":email", $data["email"]);
$stmt->execute();
$user = $stmt->fetch();

//DBに登録されているpasswordと入力されたpasswordが一致しなければログインできない
if (!$user || !password_verify($data['password'], $user["password"])) {
  $message = [
    "error" => [
      "message" => "認証情報が正しくありません"
    ]
  ];
  // レスポンス番号401で$messageの内容を出力
  create_response(401, $message);
  exit;
}

//認証を開始
session_start();
$_SESSION['id'] = $user["id"];
$_SESSION['name'] = $user["name"];
$message = [
  "message" => "ログインに成功しました"
];
create_response(200, $message);
