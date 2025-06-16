<?php
include("auth.php");

// DB 연결
$conn = new mysqli("localhost", "root", "1234", "gamjeongcheongdb");
if ($conn->connect_error) {
    die("DB 연결 실패: " . $conn->connect_error);
}

//  GET 또는 POST에서 entry_id 받기
$entry_id = $_POST['entry_id'] ?? $_GET['entry_id'] ?? null;

if ($entry_id) {
    // 논리적 삭제 처리
    $stmt = $conn->prepare("UPDATE diary_entry SET is_deleted = 1 WHERE pkey = ?");
    $stmt->bind_param("i", $entry_id);
    $stmt->execute();

    echo "<script>location.href='diaryEmpty.php';</script>";
} else {
    echo "<script>alert('삭제할 일기 ID가 없습니다.'); history.back();</script>";
}
?>
