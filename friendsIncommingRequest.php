<?php
include("auth.php");

$db_host = "localhost";
$db_user = "root";
$db_pwd = "1206";
$db_name = "gamjeongcheongdb";

$conn = new mysqli($db_host, $db_user, $db_pwd, $db_name);

if(!$conn){
    echo "db_error";
    return;
}

$users_pkey = $_COOKIE['users_pkey'];

// 4) 나에게 온 “친구 요청(status=2)” 목록 조회
$sql = "
    SELECT * FROM friends 
    LEFT JOIN users ON friends.users_pkey = users.pkey
    where friends.status AND friends.friends_pkey = $users_pkey
";

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html>
<head>
  <title>팔로워 목록</title>
</head>
<body>
  <h1> 팔로워 </h1>

  <?php if ($result && $result->num_rows > 0): ?>
    <ul>
      <?php while ($row = $result->fetch_assoc()): ?>
        <li>
          <?= htmlspecialchars($row['name']) ?>
          <!-- 나중에 수락/거절 링크를 걸고 싶다면 여기에 추가 -->
        </li>
      <?php endwhile; ?>
    </ul>
  <?php else: ?>
    <p>아직 온 친구 요청이 없습니다.</p>
  <?php endif; ?>

  <p>
    <a href="friendsList.php">유저 목록으로 돌아가기</a>
  </p>
</body>
</html>

<?php $conn->close(); ?>