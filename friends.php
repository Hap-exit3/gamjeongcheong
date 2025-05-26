<?php

$db_host = "localhost";
$db_user = "root";
$db_pwd = "1206";
$db_name = "gamjeongcheongdb";

$conn = new mysqli($db_host, $db_user, $db_pwd, $db_name);

if(!$conn){
    echo "db_error";
    return;
}    


$users_pkey = $_POST['users_pkey']
$friends_pkey = $_POST['friends_pkey']
$status = $_POST['status']



?>
