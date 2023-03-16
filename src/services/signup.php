<?php
require_once(dirname(__FILE__) . '/../db/pdo.php');
require(dirname(__FILE__) . '/../response/create_response.php');

//認証を開始
session_start();

//file_get_contentsでファイルの内容を全て文字列に読み込む
// php://inputは読み取り専用のストリーム
// 参考サイト: https://hacknote.jp/archives/47898/
$raw = file_get_contents('php://input');
$data = (array)json_decode($raw);

//入力欄のpasswordとpassword_confirmが一致しない時
if ($data["password"] !== $data["password_confirm"]) {
  $message = [
    "error" => [
      "message" => "パスワードが一致しません"
    ]
  ];
  create_response(422, $message);
  exit;
}

$pdo = Database::get();
$sql = "SELECT * FROM users WHERE email = :email";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(":email", $data["email"]);
$stmt->execute();
$user = $stmt->fetch();

$pdo = Database::get();
$sql = "SELECT * FROM user_invitations WHERE token = :token AND user_id = :user_id";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(":token", $data["token"]);
$stmt->bindValue(":user_id", $user["id"]);
$stmt->execute();
$user_invitation = $stmt->fetch();

// 招待から1日以内に承認しないと有効期限切れになる
$diff = (new DateTime())->diff(new DateTime($user_invitation["invited_at"]));
$is_expired = $diff->days >= 1;
if ($is_expired) {
  $message = [
    "error" => [
      "message" => "招待期限が切れています。管理者に連絡してください。"
    ]
  ];
  create_response(401, $message);
  exit;
}

//is_nullでnullかどうか調べる
//activated_atがnullじゃなければ認証済みなのでエラー
$is_activated = !is_null($user_invitation["activated_at"]);
if ($is_activated) {
  $message = [
    "error" => [
      "message" => "既に認証済みです。"
    ]
  ];
  create_response(401, $message);
  exit;
}

//更新処理なのでトランザクション
try {
  $pdo->beginTransaction();

  $sql = "UPDATE users SET name = :name, password = :password WHERE id = :id";
  $stmt = $pdo->prepare($sql);
  //PASSWORD_DEFAULTはパスワードのハッシュ化のアルゴリズムを指定している
  //PASSWORD_DEFAULTというアルゴリズムでハッシュ化
  $stmt->bindValue(":password", password_hash($data["password"], PASSWORD_DEFAULT));
  $stmt->bindValue(":name", $data["name"]);
  $stmt->bindValue(":id", $user["id"]);
  $result = $stmt->execute();

  $sql = "UPDATE user_invitations SET activated_at = :activated_at WHERE user_id = :user_id";
  $stmt = $pdo->prepare($sql);
  $stmt->bindValue(":user_id", $user["id"]);
  $stmt->bindValue(":activated_at", (new DateTime())->format('Y-m-d H:i:s'));
  $result = $stmt->execute();
  
  $pdo->commit();
  
  $_SESSION['id'] = $user["id"];
  $message = [
    "message" => "ユーザー登録に成功しました"
  ];
  create_response(200, $message);
} catch(PDOException $e) {
  $pdo->rollBack();
  $message = [
    "error" => [
      "message" => $e->getMessage()
    ]
  ];
  create_response(500, $message);
  }
