<?php
include("auth.php");

$db_host = "localhost";
$db_user = "root";
$db_pwd = "1234";
$db_name = "gamjeongcheongdb";

$conn = new mysqli($db_host, $db_user, $db_pwd, $db_name);

if(!$conn){
    echo "db_error";
    return;
}    

// 로그인한 사용자 정보는 쿠키에서 가져오기
$users_pkey = $_COOKIE['users_pkey'] ?? null;

// 친구 정보는 GET 방식으로 주소에서 받기 (예: friends.php?friends_pkey=2)
$friends_pkey = $_POST['friends_pkey'] ?? null;

echo "user_Pkey: " . $users_pkey . "<br>";
echo "friends_Pkey: " . $friends_pkey . "<br>";

if (isset($_COOKIE['users_pkey']) && isset($_COOKIE['friends_pkey'])) {
    $users_pkey = $_COOKIE['users_pkey'];
    $friends_pkey = $_COOKIE['friends_pkey'];
}
    // 친구 상태 확인
    echo "<h3>내 친구목록</h3>";

    $sql = "SELECT * FROM users WHERE users_pkey != $users_pkey;";

    $result = $conn->query($sql);

//     if ($result && $result->num_rows > 0) {
//         $row = $result->fetch_assoc();
//         $status = $row['status'];

//         // 상태 값에 따른 설명
//         if ($status == 0) {
//             echo "친구 요청 중입니다.";
//         } else if ($status == 1) {
//             echo "이미 친구입니다.";
//         } else {
//             echo "알 수 없는 상태입니다. (status=$status)";
//         }
//     } else {
//         echo "아직 친구가 아닙니다.";
//     }
// } else {
//     echo "필수 정보가 전달되지 않았습니다.";
// }

if ($result && $result->num_rows > 0) {
    echo "<form method='POST' action='friends.php'>";
    echo "Friends <select name='friends_pkey'>";
    while ($row = $result->fetch_assoc()) {
        $selected = ($selected_friend_pkey == $row['friends_pkey']) ? "selected" : "";
        echo "<option value='{$row['friends_pkey']}' $selected>{$row['name']}</option>";
    }
    echo "</select>";
    echo " <input type='submit' value='친구 상세보기'>";
    echo "</form>";
} else {
    echo "등록된 친구가 없습니다.";
}

// 친구 상태 확인
if ($selected_friend_pkey) {
    $sql = "SELECT status FROM friends 
            WHERE users_pkey = $users_pkey AND friends_pkey = $selected_friend_pkey";

    $result = $conn->query($sql);

    echo "<br><br>";
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $status = $row['status'];

        if ($status == 0) {
            echo "친구 요청 중입니다.";
        } else if ($status == 1) {
            echo "이미 친구입니다.";
        } else {
            echo "알 수 없는 상태입니다. (status = $status)";
        }
    } else {
        echo "아직 친구가 아닙니다.";
    }
}

mysqli_close($conn);
?>
