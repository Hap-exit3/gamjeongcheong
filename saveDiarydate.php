<?php
// DB 연결
$conn = new mysqli("localhost", "root", "1234", "gamjeongcheongdb");
if ($conn->connect_error) {
    die("DB 연결 실패: " . $conn->connect_error);
}

// // 쿠키에서 user_id 가져오기
// $user_id = $_COOKIE['user_id'] ?? null;
// if (!$user_id) {
//     die("로그인이 필요합니다.");
// }

// POST 데이터 수신
$date = $_POST['date'] ?? null;
$edit_mode = $_POST['edit_mode'] ?? 0;
$weather_id = $_POST['weather_id'] ?? null;
$emotion_id = $_POST['emotion_id'] ?? null;
$content = $_POST['content'] ?? '';
$hashtags = $_POST['hashtags'] ?? '';
$insert_date = $date . " 00:00:00"; // 선택한 날짜로 고정

// 필수값 체크
if (!$date || !$weather_id || !$emotion_id || !$content) {
    echo "<script>alert('필수 항목 누락'); history.back();</script>";
    exit;
}

if ($edit_mode) {
    // 수정 처리
    $sql = "UPDATE diary_entry 
            SET weathers_pkey=?, emotions_pkey=?, contents=?, update_date=NOW() 
            WHERE users_pkey=? AND DATE(insert_date)=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiiss", $weather_id, $emotion_id, $content, $user_id, $date);
    $stmt->execute();
    $diary_entry_pkey = $conn->query("SELECT pkey FROM diary_entry WHERE users_pkey=$user_id AND DATE(insert_date)='$date'")->fetch_assoc()['pkey'];
} else {
    // 신규 작성
    $sql = "INSERT INTO diary_entry (users_pkey, weathers_pkey, emotions_pkey, contents, insert_date, is_deleted)
            VALUES (?, ?, ?, ?, ?, 0)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiiis", $user_id, $weather_id, $emotion_id, $content, $insert_date);
    $stmt->execute();
    $diary_entry_pkey = $conn->insert_id;
}

// 해시태그 처리
if ($hashtags) {
    $tags = array_filter(array_map('trim', explode(',', $hashtags)));
    foreach ($tags as $tag) {
        if ($tag === '') continue;

        // tag 테이블에 INSERT IGNORE
        $conn->query("INSERT IGNORE INTO tag (name) VALUES ('$tag')");

        // tag_pkey 가져오기
        $res = $conn->query("SELECT pkey FROM tag WHERE name = '$tag'");
        if ($row = $res->fetch_assoc()) {
            $tag_pkey = $row['pkey'];

            // tag_search INSERT
            $map_sql = "INSERT INTO tag_search (tag_pkey, diary_entry_pkey, insert_date)
                        VALUES (?, ?, NOW())";
            $map_stmt = $conn->prepare($map_sql);
            $map_stmt->bind_param("ii", $tag_pkey, $diary_entry_pkey);
            $map_stmt->execute();
        }
    }
}

echo "<script>alert('일기 저장 완료!'); location.href='diaryCalender.php';</script>";
$conn->close();
?>
