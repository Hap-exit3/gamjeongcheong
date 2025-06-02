<?php

$db_host = "localhost";
$db_user = "root";
$db_pwd = "1206";
$db_name = "gamjeongcheongdb";

$conn = new mysqli($db_host, $db_user, $db_pwd, $db_name);
echo"jd";
if(!$conn){
    echo "db_error";
    return;
}


$current_user_id = 3;

$sql = "SELECT pkey, name FROM users Where pkey != $current_user_id;"; //DB에서 id, pwd 조회
$result = mysqli_query($conn, $sql);

// while($row = mysqli_fetch_array($result)){
//     echo "<option value='$row["pkey"]'>$row["name"]</option>";
//     //echo "dk";
//     //echo $row['id'];
//     //echo $row['pwd'];
//     //echo $row['birth'];
//     //echo "<br>";
// }

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        // 친구 후보 출력
        echo '<option value="' . $row['pkey'] . '">' . $row['name'] . '</option>';
    }
} else {
    // 오류 또는 결과 없음 처리
    echo '<option disabled>친구 목록이 없습니다.</option>';
}

mysqli_close($conn);
?>