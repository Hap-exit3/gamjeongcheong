<?php
include("auth.php");

// DB 연결
$conn = new mysqli("localhost", "root", "1234", "gamjeongcheongdb");
if ($conn->connect_error) {
    die("DB 연결 실패: " . $conn->connect_error);
}

// POST 데이터 수신
$users_pkey     = $_POST['user_id'] ?? 1; // 개발 중엔 1로 고정
$weathers_pkey  = $_POST['weather_id'] ?? null;
$emotions_pkey  = $_POST['emotion_id'] ?? null;
$contents       = $_POST['content'] ?? null;
$hashtags       = $_POST['hashtags'] ?? [];
$insert_date    = date("Y-m-d H:i:s");
$entry_date     = $_POST['entry_date'] ?? date("Y-m-d"); // 오늘 또는 캘린더 선택 날짜

// 디버깅 출력
echo "<h2>🔍 POST 데이터 확인</h2><pre>";
print_r($_POST);
echo "\n[entry_date]: $entry_date\n";
echo "</pre>";

// 필수 항목 누락 검사
$missing = [];
if (!$users_pkey) $missing[] = 'users_pkey';
if (!$weathers_pkey) $missing[] = 'weathers_pkey';
if (!$emotions_pkey) $missing[] = 'emotions_pkey';
if (!$contents) $missing[] = 'contents';

if (!empty($missing)) {
    echo "<p style='color:red;'> 필수 항목 누락: " . implode(', ', $missing) . "</p>";
    exit;
}

// 1. INSERT 실행
$diary_sql = "
    INSERT INTO diary_entry 
    (users_pkey, weathers_pkey, emotions_pkey, contents, insert_date, update_date, is_deleted, entry_date)
    VALUES (?, ?, ?, ?, ?, ?, 0, ?)
";
$stmt = $conn->prepare($diary_sql);
$stmt->bind_param("iiissss", $users_pkey, $weathers_pkey, $emotions_pkey, $contents, $insert_date, $insert_date, $entry_date);

if ($stmt->execute()) {
    $diary_entry_pkey = $conn->insert_id;

    // 2. 해시태그 삽입
    $tags = array_filter(array_map('trim', (array)$hashtags));
    foreach ($tags as $tag) {
        if ($tag === '') continue;

        // 2-1. tag 테이블 존재 확인
        $check_stmt = $conn->prepare("SELECT pkey FROM tag WHERE name = ?");
        $check_stmt->bind_param("s", $tag);
        $check_stmt->execute();
        $check_stmt->store_result();

        if ($check_stmt->num_rows > 0) {
            $check_stmt->bind_result($tag_pkey);
            $check_stmt->fetch();
        } else {
            // 없으면 삽입
            $insert_tag_stmt = $conn->prepare("INSERT INTO tag (name) VALUES (?)");
            $insert_tag_stmt->bind_param("s", $tag);
            $insert_tag_stmt->execute();
            $tag_pkey = $insert_tag_stmt->insert_id;
            $insert_tag_stmt->close();
        }
        $check_stmt->close();

        // 2-2. tag_search 삽입
        $map_stmt = $conn->prepare("INSERT INTO tag_search (tag_pkey, diary_entry_pkey, insert_date) VALUES (?, ?, ?)");
        $map_stmt->bind_param("iis", $tag_pkey, $diary_entry_pkey, $insert_date);
        $map_stmt->execute();
        $map_stmt->close();
    }

    // 3. 저장 완료 → 상세 페이지 이동
    echo "<script>
        if (confirm('정말 저장하시겠습니까?')) {
            location.href = 'diaryDetail.php?entry_id=" . $diary_entry_pkey . "';
        } else {
            history.back();
        }
    </script>";
} else {
    echo "<p style='color:red;'>❌ 저장 실패: " . htmlspecialchars($stmt->error) . "</p>";
    echo "<script>history.back();</script>";
}

$conn->close();
?>
