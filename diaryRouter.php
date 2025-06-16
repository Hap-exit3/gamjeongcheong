<?php
include("auth.php");

// DB 연결
$conn = new mysqli("localhost", "root", "1234", "gamjeongcheongdb");
if ($conn->connect_error) {
    die("DB 연결 실패: " . $conn->connect_error);
}

$user_id = 1; // 임시로 고정
$date = $_GET['date'] ?? date('Y-m-d');

// 오늘 날짜보다 미래면 차단
$today = date('Y-m-d');
if ($date > $today) {
    echo "<script>alert('미래 날짜에는 일기를 작성할 수 없습니다.'); history.back();</script>";
    exit;
}

// 해당 날짜에 작성된 일기 있는지 확인
$sql = "SELECT pkey FROM diary_entry 
        WHERE users_pkey = ? AND DATE(entry_date) = ? AND is_deleted = 0 
        LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $user_id, $date);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $entry_id = $row['pkey'];
    header("Location: diaryDetail.php?entry_id=$entry_id");
} else {
    header("Location: diaryWriting.php?date=$date");
}
exit;
?>
