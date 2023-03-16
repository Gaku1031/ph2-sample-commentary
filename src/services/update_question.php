<?php
require_once(dirname(__FILE__) . '/../db/pdo.php');

$pdo = Database::get();

$params = [
  "content" => $_POST["content"],
  "supplement" => $_POST["supplement"],
  "id" => $_POST["question_id"],
];
$set_query = "SET content = :content, supplement = :supplement";

//$_FILESでアップロードされた値をとる
//何かの画像がアップロードされている時
if ($_FILES["image"]["tmp_name"] !== "") {
  // .=は結合代入
  //$set_queryの"SET content = :content, supplement = :supplement"に, image = :imageを追加する
  $set_query .= ", image = :image";
  //$paramsのimageは””にしておいて、後で生成されたファイル名を代入する
  $params["image"] = "";
}

$sql = "UPDATE questions $set_query WHERE id = :id";

$pdo = Database::get();
//更新処理なのでトランザクションを使用する(トランザクションについて : https://gray-code.com/php/transaction-by-using-pdo/)
//もしもどっかの処理でエラーが起きたときにそれまでの処理が実行されてしまったらデータが消えるなどの不具合が起きてしまうので、一連の処理をひとまとめにして、エラーが起きなかったら実行するようにする
$pdo->beginTransaction();
try { 
  if(isset($params["image"])) {

//uniqidでuniqueな13桁のidを生成(mt_randは乱数を生成する関数（randより高速)) : 第２引数に true をセットすると、13桁の文字列にプラスしてドット（ . )と9桁の数字が追加される($more_entropyというものをtrueにしているらしい)
// 実行例: 145677405658ddab75790ea7.19793837  (参考URL: https://amatou-papa.com/php-uniqid/)

//substr : 文字列の一部分を返す
//strrchr : 文字列中に文字が最後に現れる場所を取得する
// substr(strrchr($_FILES['image']['name'], '.'), 1)で$_FILES['image']['name']に含まれる「.」以降の文字列を取得する
// ,1)となっているのは、「.」以降の文字の1文字目すなわち「.」を除いた文字を取得するため。
// 参考URL : https://www.php.net/manual/ja/function.strrchr.php

    // strrchr($_FILES['image']['name'], '.')で.pngや.jpegを取得
    // substr(strrchr($_FILES['image']['name'], '.'), 1でpngやjpegなどの識別子を取得
    $image_name = uniqid(mt_rand(), true) . '.' . substr(strrchr($_FILES['image']['name'], '.'), 1);
    $image_path = dirname(__FILE__) . '/../assets/img/quiz/' . $image_name;
    move_uploaded_file(
      $_FILES['image']['tmp_name'], 
      $image_path
    );
    $params["image"] = $image_name;
  }

  $stmt = $pdo->prepare($sql);
  $result = $stmt->execute($params);

  $sql = "DELETE FROM choices WHERE question_id = :question_id ";
  $stmt = $pdo->prepare($sql);
  // edit.phpから送信されてきたquestion_idを使う
  $stmt->bindValue(":question_id", $_POST["question_id"]);
  $stmt->execute();

  $stmt = $pdo->prepare("INSERT INTO choices(name, valid, question_id) VALUES(:name, :valid, :question_id)");
  for ($i = 0; $i < count($_POST["choices"]); $i++) {
    $stmt->execute([
      "name" => $_POST["choices"][$i],
      // edit.phpのname=correctChoiceの value(1or2or3)と$iが一致するものにvalid=1をつける
      "valid" => (int)$_POST['correctChoice'] === $i + 1 ? 1 : 0,
      "question_id" => $_POST["question_id"]
    ]);
  }
  //commitはトランザクションをコミットするときに使う
  $pdo->commit();
  header("Location: ". "http://localhost:8080/admin/index.php");
  //エラーが出たら処理の実行をキャンセルして元に戻す(rollbackする)
} catch(Error $e) {
  $pdo->rollBack();
}
