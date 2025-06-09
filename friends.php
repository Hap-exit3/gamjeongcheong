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
echo "User_Pkey";
echo "jjj".$_POST['users_pkey'];
echo "Friends_Pkey";
echo "ll".$_POST['friends'];

if (isset($_POST['users_pkey']) && isset($_POST['friends_pkey'])) {
    $users_pkey = $_POST['users_pkey'];
    $friends_pkey = $_POST['friends_pkey'];

    // 친구 상태 확인
    $sql = "SELECT status FROM friends 
            WHERE users_pkey = $users_pkey AND friends_pkey = $friends_pkey";

    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $status = $row['status'];

        // 상태 값에 따른 설명
        if ($status == 0) {
            echo "친구 요청 중입니다.";
        } else if ($status == 1) {
            echo "이미 친구입니다.";
        } else {
            echo "알 수 없는 상태입니다. (status=$status)";
        }
    } else {
        echo "아직 친구가 아닙니다.";
    }
} else {
    echo "필수 정보가 전달되지 않았습니다.";
}

?>
