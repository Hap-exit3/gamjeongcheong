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

if (isset($_POST['users_pkey']) && isset($_POST['friends_pkey'])) {
   // print_r($_POST);
    $users_pkey = $_POST['users_pkey'];
    $friends_pkey = $_POST['friends_pkey'];

    $check_sql = "SELECT status FROM friends 
                  WHERE users_pkey = $users_pkey AND friends_pkey = $friends_pkey";
    
//echo "$check_sql";
    $check_result = $conn->query($check_sql);

    if ($check_result->num_rows > 0) {
        // 이미 친구 관계가 존재함
        // echo "<script>
        //         alert('이미 친구입니다.');
        //         window.location.href = 'friendsAdd.php';
        //       </script>";
        exit;
    }

    $sql = "INSERT INTO friends(users_pkey, friends_pkey, status)
            VALUES ($users_pkey, $friends_pkey, 1)";

 if ($conn->query($sql)) {
        echo "<script>
                alert('팔로우 하였습니다.');
                window.location.href = 'friendsAdd.php';
              </script>";
       // exit;
    } else {
        "<script>
                alert('팔로우 중입니다.');
                window.location.href = 'friendsAdd.php';
              </script>";
    }
} else {
    echo "폼 데이터가 부족합니다.";
}

mysqli_close($conn);
?>