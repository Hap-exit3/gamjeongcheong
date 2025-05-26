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


$sql = "SELECT * FROM users Where users.pkey != 3;"; //DB에서 id, pwd 조회
$result = mysqli_query($conn, $sql);


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