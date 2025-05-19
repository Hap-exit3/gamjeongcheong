<?php

 //$id = $_POST['id'];
 //$pwd = $_POST['pwd'];

 //echo $id," ",

$db_host = "localhost";
$db_user = "root";
$db_pwd = "1234";
$db_name = "gamjeongcheongdb";

$conn = new mysqli($db_host, $db_user, $db_pwd, $db_name);

$id = $_POST['id'];
$password = $_POST['password'];
$name = $_POST['name'];
$email = $_POST['email'];

//중복 id check
$check_sql = "select * from users where id='$id'";
$check_result = mysqli_query($conn, $check_sql);

if (mysqli_num_rows($check_result) > 0) {
    echo "<script>alert('이미 존재하는 ID입니다.'); history.back();</script>";
    exit();
}

$sql = "insert into users (id, password, name) values ('$id', '$password', '$name', '$email')";

if (mysqli_query($conn, $sql)) {
    echo "<script>alert('회원가입 성공!'); location.replace('signIn.php');</script>";
} else {
    echo "<script>alert('회원가입 실패: " . mysqli_error($conn) . "'); history.back();</script>";
}

mysqli_close($conn);
?>