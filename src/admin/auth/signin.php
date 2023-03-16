<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>POSSE ログイン</title>
  <!-- スタイルシート読み込み -->
  <link rel="stylesheet" href="./../assets/styles/common.css">
  <link rel="stylesheet" href="./../admin.css">
  <!-- Google Fonts読み込み -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;700&family=Plus+Jakarta+Sans:wght@400;700&display=swap"
    rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>
<body>
  <header>
    <div>posse</div>
  </header>
  <div class="wrapper">
    <main>
      <div class="container">
        <h1 class="mb-4">ログイン</h1>
          <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="text" name="email" class="email form-control" id="email">
          </div>
          <div class="mb-3">
            <label for="password" class="form-label">パスワード</label>
            <input type="password" name="password" id="password" class="form-control">
          </div>
          <button type="submit" disabled class="btn submit" onclick="signin()" >ログイン</button>
          <!-- ユーザー登録をしていない場合にサインアップ画面に遷移できるようにする -->
          <div class="mt-3 text-xs flex justify-between items-center">
            <p>まだアカウントを持っていませんか？</p>
            <a href="./signup.php"><button class="btn submit">登録</button></a>
          </div>
      </div>
    </main>
  </div>
  <!-- バリデーション -->
  <script>
    // email形式のバリデーション（/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/）
    const EMAIL_REGEX = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/
    const submitButton = document.querySelector('.btn.submit')
    const emailInput = document.querySelector('.email')
    inputDoms = Array.from(document.querySelectorAll('.form-control'))
    inputDoms.forEach(inpuDom => {
      inpuDom.addEventListener('input', event => {
        const isFilled = inputDoms.filter(d => d.value).length === inputDoms.length
        submitButton.disabled = !(isFilled && EMAIL_REGEX.test(emailInput.value))
      })
    })
    const signin = async () => {
      try {
        const res = await fetch(`/services/signin.php`, { 
          method: 'POST',
          // JSON.stringifyでオブジェクトや値をJSON形式に変換できる
          body : JSON.stringify({ 
            email : document.querySelector('#email').value,
            password : document.querySelector('#password').value,
          }),
          headers:{
            // AcceptとContent-Typeについて
            // 参考URL : https://qiita.com/satoru_pripara/items/89fff277db5212ec37e1
            'Accept': 'application/json, */*',
            "Content-Type": "application/x-www-form-urlencoded"
          },
        });
        const json = await res.json();
        if (res.status === 401) {
          alert(json["error"]["message"])
        }
        if (res.status === 200) {
          alert('ログインに成功しました')
          location.href = '/admin/index.php'
        } 
      } catch (e) {
        console.error(e)
      }
    }
  </script>
</body>
</html>
