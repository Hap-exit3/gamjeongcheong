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

// 로그인한 사용자 정보는 쿠키에서 가져오기
$users_pkey = $_COOKIE['users_pkey'] ?? null;

$friends_pkey = $_POST['friends_pkey'] ?? null;

$selected_friend_pkey = $_POST['friends_pkey'] ?? null;

$sql = "SELECT pkey, name FROM users Where pkey != $users_pkey;"; //DB에서 id, pwd 조회
$result = mysqli_query($conn, $sql);

if (isset($_COOKIE['users_pkey']) && isset($_COOKIE['friends_pkey'])) {
    $users_pkey = $_COOKIE['users_pkey'];
    $friends_pkey = $_POST['friends_pkey'];
}
    // 친구 상태 확인
    echo "<h3>친구 여부</h3>";

   // $sql = "SELECT pkey, name FROM users WHERE pkey != {$users_pkey}";

  //  $result = $conn->query($sql);



// 친구 상태 확인
if ($selected_friend_pkey) {
    $sql = "SELECT * FROM friends 
    WHERE users_pkey = {$users_pkey}
    AND friends_pkey = {$selected_friend_pkey}";
    echo "$sql";
    $result = $conn->query($sql);


    echo "<br>";
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
       // print_r($row);
        $status = $row['status'];

        if ($status == 1) {
            echo "팔로우 중입니다.";
        } else if ($status == 2) {
            echo "팔로우 하지 않았습니다.";
        } else {
            echo "알 수 없는 상태입니다. (status = $status)";
        }
    } else {
        echo "팔로우하지 않았습니다";
    }
    echo "<br>";
        echo '<form action="friendsList.php" method="get" style="display:inline;">';
        echo '  <button type="submit">유저 목록 보기</button>';
        echo '</form>';
}



mysqli_close($conn);
?>


