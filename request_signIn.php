<?php

setcookie("ZLZN", "콜라", time() + 3600, "/");
echo $_COOKIE["쿠키"];

 //$id = $_POST['id'];
 //$pwd = $_POST['pwd'];

 //echo $id," ",



$db_host = "localhost";
$db_user = "root";
$db_pwd = "1206";
$db_name = "gamjeongcheongdb";

$conn = new mysqli($db_host, $db_user, $db_pwd, $db_name);

if(!$conn){
    echo "db_error";
    return;
}

$id = $_POST['id'];
$password = $_POST['password'];

$sql = "select * from users where id='$id' and password='$password';"; //DB에서 id, pwd 조회
$result = mysqli_query($conn, $sql);

$result_login = 0;

while($row = mysqli_fetch_array($result)){
    //echo $row['name'];
    //echo $row['id'];
    //echo $row['pwd'];
    //echo $row['birth'];
    //echo "<br>";
    $result_login = 1;
}

$link = ""; //login page 넘어갈 때 이용할 Link

if($result_login===1){
    $link = "main.php"; //login 성공 후, main.php page로 이동한다.
} else{
    $link = "signIn.php"; // login 실패 시, login.php page로 돌아간다
}
echo("<script> location.replace('$link'); </script>");

mysqli_close($conn);
?>