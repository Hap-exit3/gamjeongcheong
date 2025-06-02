<?php
$db_host = "localhost";
$db_user = "root";
$db_pwd = "1206";
$db_name = "gamjeongcheongdb";

$conn = new mysqli($db_host, $db_user, $db_pwd, $db_name);

if (!$conn) {
    echo "db_error";
    return;
}

$id = $_POST['id'];
$password = $_POST['password'];

$sql = "SELECT pkey, id, name FROM users WHERE id='$id' AND password='$password'";
$result = mysqli_query($conn, $sql);

$result_login = 0;
$user_pkey = null;

while ($row = mysqli_fetch_array($result)) {
    $result_login = 1;
    $user_pkey = $row['pkey'];
    $user_name = $row['name'];

    // ✅ 쿠키 설정 (유효기간: 시간)
    setcookie("user_pkey", $user_pkey, time() + 360000, "/");
    setcookie("user_name", $user_name, time() + 360000, "/");
}

$link = ($result_login === 1) ? "main.php" : "signIn.php";
echo("<script> location.replace('$link'); </script>");

mysqli_close($conn);
?>
