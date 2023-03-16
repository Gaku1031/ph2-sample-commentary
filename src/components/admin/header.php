<header>
  <div>posse</div>
  <div>
    <div onclick="signout()">ログアウト</div>
  </div>
</header>
<script>
const signout = async () => {
  const res = await fetch(`/services/signout.php`, { 
    method: 'DELETE',
    headers:{
      'Accept': 'application/json, */*',
      "Content-Type": "application/x-www-form-urlencoded"
    },
  });
  if (res.status === 204) {
    alert('ログアウトに成功しました')
    location.href = '/'
  } else {
    alert('ログアウトに失敗しました')
  }
}
</script>
