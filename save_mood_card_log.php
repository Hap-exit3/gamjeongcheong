
<?php
$db_host = "localhost";
$db_user = "root";
$db_pwd = "1234";
$db_name = "gamjeongcheongdb";

$conn = new mysqli($db_host, $db_user, $db_pwd, $db_name);

if ($conn->connect_error) {
    die("DB 연결 실패: " . $conn->connect_error);
}

$diary_entry_pkey = $_POST['diary_entry_pkey'];  
$mood_card_pkey = $_POST['mood_card_pkey'];      
$insert_date = date("Y-m-d H:i:s");            

$sql = "INSERT INTO mood_card_log (diary_entry_pkey, mood_card_pkey, insert_date)
        VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iis", $diary_entry_pkey, $mood_card_pkey, $insert_date);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo "카드 발급 성공!";
} else {
    echo "카드 발급 실패!";
}

$conn->close();
?>
