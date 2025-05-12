<?php

$host = "localhost";
$user = "root";
$pwd = "1206";
$db_name = "testdb";

$conn = new mysqli($host, $user, $pwd, $db_name);

if(!$conn){
    echo "db_error";
    return;
}else{
    echo "succeed connet db";
}

$sql = "select * from test_table;";
$result = mysqli_query($conn, $sql);
while($row = mysqli_fetch_array($result)){
    echo $row['name'];
    //echo $row['id'];
    echo $row['pwd'];
   //echo $row['birth'];
    echo "<br>";
}

mysqli_close($conn);
?>