<?php

require_once(dirname(__FILE__) . '/../db/pdo.php');

// 画像のパスを設定して、読み込んだ画像をmove_upload_fileでimg/quizに移動させる

//uniqidでuniqueな13桁のidを生成(mt_randは乱数を生成する関数（randより高速)) : 第２引数に true をセットすると、13桁の文字列にプラスしてドット（ . )と9桁の数字が追加される($more_entropyというものをtrueにしているらしい)
// 実行例: 145677405658ddab75790ea7.19793837  (参考URL: https://amatou-papa.com/php-uniqid/)

//substr : 文字列の一部分を返す
//strrchr : 文字列中に文字が最後に現れる場所を取得する
// substr(strrchr($_FILES['image']['name'], '.'), 1)で$_FILES['image']['name']に含まれる「.」以降の文字列を取得する
// ,1)となっているのは、「.」以降の文字の1文字目すなわち「.」を除いた文字を取得するため。
// 参考URL : https://www.php.net/manual/ja/function.strrchr.php

$image_name = uniqid(mt_rand(), true) . '.' . substr(strrchr($_FILES['image']['name'], '.'), 1);

// dirname(__FILE__)で自分がいる場所の絶対パスを返す（ファイル名まで返したい時は__FILE__を使う)
$image_path = dirname(__FILE__) . '/../assets/img/quiz/' . $image_name;
// move_upload_file(from, to)
move_uploaded_file(
  $_FILES['image']['tmp_name'], 
  $image_path
);

$pdo = Database::get();
$stmt = $pdo->prepare("INSERT INTO questions(content, image, supplement) VALUES(:content, :image, :supplement)");
$stmt->execute([
  "content" => $_POST["content"],
  "image" => $image_name,
  "supplement" => $_POST["supplement"]
]);
// lastInsertId(最後に挿入された行の ID あるいはシーケンスの値を返す)
$lastInsertId = $pdo->lastInsertId();

$stmt = $pdo->prepare("INSERT INTO choices(name, valid, question_id) VALUES(:name, :valid, :question_id)");

for ($i = 0; $i < count($_POST["choices"]); $i++) {
  $stmt->execute([
    "name" => $_POST["choices"][$i],
    // 選択して送信されたcorrectChoiceのvalue(1or2or3)がfor文の順番と一致した選択肢にはvalid=1をつけ、値がない(null)のものには0をつける
    "valid" => (int)$_POST['correctChoice'] === $i + 1 ? 1 : 0,
    "question_id" => $lastInsertId
  ]);
}

//指定したページにredirectするためにheader関数を使う（引数は「Location: url」）
header("Location: ". "http://localhost:8080/admin/index.php");
