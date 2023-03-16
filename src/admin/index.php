<?php

require_once(dirname(__FILE__) . '/../db/pdo.php');
//sessionIDがない場合はログイン画面に遷移する
if (!isset($_SESSION['id'])) {
  header('Location: /admin/auth/signin.php');
} else {
  $pdo = Database::get();
  $questions = $pdo->query("SELECT * FROM questions")->fetchAll(PDO::FETCH_ASSOC);
  $is_empty = count($questions) === 0;
}

?>
<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>POSSE 管理画面ダッシュボード</title>
  <!-- スタイルシート読み込み -->
  <link rel="stylesheet" href="./assets/styles/common.css">
  <link rel="stylesheet" href="./admin.css">
  <!-- Google Fonts読み込み -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;700&family=Plus+Jakarta+Sans:wght@400;700&display=swap"
    rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
  <script src="../assets/scripts/common.js" defer></script>
</head>

<body>
  <?php include(dirname(__FILE__) . '/../components/admin/header.php'); ?>
  <div class="wrapper">
    <?php include(dirname(__FILE__) . '/../components/admin/sidebar.php'); ?>
    <main>
      <div class="container">
        <h1 class="mb-4">問題一覧</h1>
        <?php if(!$is_empty) { ?>
        <table class="table">
          <thead>
            <tr>
                <th>ID</th>
                <th>問題</th>
                <th></th>
            </tr>
         </thead>
         <tbody>
            <?php foreach($questions as $question) { ?>
            <tr id="question-<?= $question["id"] ?>">
                <td><?= $question["id"]; ?></td>
                <td>
                  <!-- urlの中にidを含ませて送信し、edit.phpで$_REQUESTを使ってidを取得する -->
                  <a href="./questions/edit.php?id=<?= $question["id"] ?>">
                    <?= $question["content"]; ?>
                  </a>
                </td>
                <td onclick="deleteQuestion(<?= $question['id'] ?>)">削除</td>
            </tr>
            <?php } ?>
          </tbody>
        </table>
        <?php } else {?>
          問題がありません。
        <?php } ?>
      </div>
    </main>
  </div>
  <script>
    const deleteQuestion = async (questionId) => {
      if (!confirm('削除してもよろしいでしょうか？')) return
      const res = await fetch(`http://localhost:8080/services/delete_question.php?id=${questionId}`, { method: 'DELETE' });
      if (res.status === 204) {
        alert('削除に成功しました')
        document.getElementById(`question-${questionId}`).remove()
      } else {
        alert('削除に失敗しました')
      }
    }
  </script>
</body>

</html>
