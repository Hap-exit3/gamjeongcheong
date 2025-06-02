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

$sql = "SELECT pkey, name FROM users Where users.pkey != $current_user_id;"; //DB에서 id, pwd 조회


while($row = mysqli_fetch_array($result)){
    echo "<option value='$row["pkey"]'>$row["name"]</option>";
    //echo "dk";
    //echo $row['id'];
    //echo $row['pwd'];
    //echo $row['birth'];
    //echo "<br>";
}

mysqli_close($conn);
?>