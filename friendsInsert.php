<?php

$db_host = "localhost";
$db_user = "root";
$db_pwd = "1234";
$db_name = "gamjeongcheongdb";

$conn = new mysqli($db_host, $db_user, $db_pwd, $db_name);

if(!$conn){
    echo "db_error";
    return;
}   

if (isset($_POST['users_pkey']) && isset($_POST['friends_pkey']) && isset($_POST['status'])) {
    $users_pkey = $_POST['users_pkey'];
    $friends_pkey = $_POST['friends_pkey'];
    $status = $_POST['status'];

    $check_sql = "SELECT * FROM friends 
                  WHERE users_pkey = $users_pkey AND friends_pkey = $friends_pkey";
    $check_result = $conn->query($check_sql);

    if ($check_result->num_rows > 0) {
        // 이미 친구 관계가 존재함
        echo "<script>
                alert('이미 친구입니다.');
                window.location.href = 'friendsAdd.php';
              </script>";
        exit;
    }

    $sql = "INSERT INTO friends(users_pkey, friends_pkey, status)
            VALUES ($users_pkey, $friends_pkey, $status)";

 if ($conn->query($sql)) {
        echo "<script>
                alert('친구신청을 완료하였습니다.');
                window.location.href = 'friendsAdd.php';
              </script>";
        exit;
    } else {
        echo "에러 발생: " . $conn->error;
    }
} else {
    echo "폼 데이터가 부족합니다.";
}     

mysqli_close($conn);
?>