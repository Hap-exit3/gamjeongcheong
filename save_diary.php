<?php
<?php include("auth.php"); ?>


// DB 연결
$conn = new mysqli("localhost", "root", "1206", "gamjeongcheongdb");
if ($conn->connect_error) {
    die("DB 연결 실패: " . $conn->connect_error);
}

// POST 데이터 수신
$users_pkey     = $_POST['user_id'] ?? null;
$weathers_pkey  = $_POST['weather_id'] ?? null;
$emotions_pkey  = $_POST['emotion_id'] ?? null;
$contents       = $_POST['content'] ?? null;
$hashtags       = $_POST['hashtags'] ?? '';
$insert_date    = date("Y-m-d H:i:s");

// 디버깅 출력
echo "<h2>🔍 POST 데이터 확인</h2><pre>";
print_r($_POST);
echo "</pre>";

// 필수 항목 누락 검사
$missing = [];
if (!$users_pkey) $missing[] = 'users_pkey';
if (!$weathers_pkey) $missing[] = 'weathers_pkey';
if (!$emotions_pkey) $missing[] = 'emotions_pkey';
if (!$contents) $missing[] = 'contents';

if (!empty($missing)) {
    echo "<p style='color:red;'>❌ 필수 항목 누락: " . implode(', ', $missing) . "</p>";
    exit;
}

// 1. diary_entry INSERT
$diary_sql = "INSERT INTO diary_entry (users_pkey, weathers_pkey, emotions_pkey, contents, insert_date, update_date, is_deleted)
              VALUES (?, ?, ?, ?, ?, ?, 0)";
$stmt = $conn->prepare($diary_sql);
$stmt->bind_param("iiisss", $users_pkey, $weathers_pkey, $emotions_pkey, $contents, $insert_date, $insert_date); // ← 수정된 부분!
$stmt->execute();


if ($stmt->affected_rows > 0) {
    $diary_entry_pkey = $conn->insert_id;

    // 2. 해시태그 삽입
    $tags = array_filter(array_map('trim', explode(',', $hashtags)));
    foreach ($tags as $tag) {
        if ($tag === '') continue;

        // 2-1. tag 테이블에 있는지 확인
        $check_stmt = $conn->prepare("SELECT pkey FROM tag WHERE name = ?");
        $check_stmt->bind_param("s", $tag);
        $check_stmt->execute();
        $check_stmt->store_result();

        if ($check_stmt->num_rows > 0) {
            $check_stmt->bind_result($tag_pkey);
            $check_stmt->fetch();
        } else {
            // 없으면 삽입 후 pkey 가져오기
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

    // 저장 성공 후 확인창 → 상세 페이지 이동
    echo "<script>
      if (confirm('정말 저장하시겠습니까?')) {
        location.href = 'diaryDetail.php?entry_id=" . $diary_entry_pkey . "';
      } else {
        history.back();
      }
    </script>";
} else {
    echo "<script>alert('❌ 저장 실패!'); history.back();</script>";
}

$conn->close();
?>
