<?php
session_start();

// DB 연결
$conn = new mysqli("localhost", "root", "1234", "gamjeongcheongdb");
if ($conn->connect_error) {
    die("DB 연결 실패: " . $conn->connect_error);
}

// POST 데이터 수신
$users_pkey     = $_POST['users_pkey'] ?? null;
$weathers_pkey  = $_POST['weathers_pkey'] ?? null;
$emotions_pkey  = $_POST['emotions_pkey'] ?? null;
$contents       = $_POST['contents'] ?? null;
$hashtags       = $_POST['hashtags'] ?? '';
$insert_date    = date("Y-m-d H:i:s");

//  디버깅 출력
echo "<h2>🔍 POST 데이터 확인</h2><pre>";
print_r($_POST);
echo "</pre>";

//  누락된 값 검사
$missing = [];
if (!$users_pkey) $missing[] = 'users_pkey';
if (!$weathers_pkey) $missing[] = 'weathers_pkey';
if (!$emotions_pkey) $missing[] = 'emotions_pkey';
if (!$contents) $missing[] = 'contents';

if (!empty($missing)) {
    echo "<p style='color:red;'>❌ 필수 항목 누락: " . implode(', ', $missing) . "</p>";
    exit;
}

// INSERT INTO diary_entry
$diary_sql = "INSERT INTO diary_entry (users_pkey, weathers_pkey, emotions_pkey, contents, insert_date)
              VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($diary_sql);
$stmt->bind_param("iiiis", $users_pkey, $weathers_pkey, $emotions_pkey, $contents, $insert_date);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    $diary_entry_pkey = $conn->insert_id;

    // 해시태그 처리
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
                        VALUES (?, ?, ?)";
            $map_stmt = $conn->prepare($map_sql);
            $map_stmt->bind_param("iis", $tag_pkey, $diary_entry_pkey, $insert_date);
            $map_stmt->execute();
        }
    }

    echo "<script>alert(' 일기 저장 성공!'); location.href='moodCard.php';</script>";
} else {
    echo "<script>alert('❌ 저장 실패!'); history.back();</script>";
}

$conn->close();
?>
