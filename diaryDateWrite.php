<?php
include("auth.php");

// DB 연결
$conn = new mysqli("localhost", "root", "1234", "gamjeongcheongdb");
if ($conn->connect_error) {
    die("DB 연결 실패: " . $conn->connect_error);
}

// // 쿠키에서 user_id 가져오기 (예: 로그인 시 쿠키에 저장해둠)
// $user_id = $_COOKIE['user_id'] ?? null;
// if (!$user_id) {
//     die("로그인이 필요합니다."); // user_id 없으면 오류 출력
// }

// 날짜 가져오기 (기본은 오늘)
$date = $_GET['date'] ?? date('Y-m-d');

// 해당 날짜의 일기 존재 여부 확인
$sql = "SELECT * FROM diary_entry WHERE users_pkey=? AND DATE(insert_date)=? AND is_deleted=0";
$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $user_id, $date);
$stmt->execute();
$result = $stmt->get_result();
$entry = $result->fetch_assoc();

// 기존 데이터 불러오기
$weather_id = $entry['weathers_pkey'] ?? '';
$emotion_id = $entry['emotions_pkey'] ?? '';
$content = $entry['contents'] ?? '';
$edit_mode = $entry ? 1 : 0;
?>

<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="UTF-8">
  <title>감정청 - <?= htmlspecialchars($date) ?> 일기 <?= $edit_mode ? '수정' : '작성' ?></title>
</head>
<body>
  <h2><?= htmlspecialchars($date) ?> 일기 <?= $edit_mode ? '수정' : '작성' ?></h2>

  <form action="save_diary_date.php" method="POST">
    <input type="hidden" name="date" value="<?= htmlspecialchars($date) ?>">
    <input type="hidden" name="edit_mode" value="<?= $edit_mode ?>">
    <input type="hidden" name="user_id" value="<?= $user_id ?>">

    <label>오늘의 날씨:</label><br>
    <?php
    $weather_sql = "SELECT pkey, description, emoji FROM weathers";
    $weather_result = $conn->query($weather_sql);
    while ($row = $weather_result->fetch_assoc()):
    ?>
      <label>
        <input type="radio" name="weather_id" value="<?= $row['pkey'] ?>" <?= $row['pkey']==$weather_id ? 'checked' : '' ?>>
        <?= htmlspecialchars($row['emoji']) ?> <?= htmlspecialchars($row['description']) ?>
      </label><br>
    <?php endwhile; ?>

    <br><label>오늘의 감정:</label><br>
    <?php
    $emotion_sql = "SELECT pkey, description, emoji FROM emotions";
    $emotion_result = $conn->query($emotion_sql);
    while ($row = $emotion_result->fetch_assoc()):
    ?>
      <label>
        <input type="radio" name="emotion_id" value="<?= $row['pkey'] ?>" <?= $row['pkey']==$emotion_id ? 'checked' : '' ?>>
        <?= htmlspecialchars($row['emoji']) ?> <?= htmlspecialchars($row['description']) ?>
      </label><br>
    <?php endwhile; ?>

    <br><label>해시태그 (쉼표로 구분)</label><br>
    <input type="text" name="hashtags" placeholder="#휴식, #혼자만의시간"><br><br>

    <label>오늘의 내용:</label><br>
    <textarea name="content" rows="8" cols="60" placeholder="오늘 하루는 어땠나요?"><?= htmlspecialchars($content) ?></textarea><br><br>

    <button type="submit"><?= $edit_mode ? '수정하기' : '저장하기' ?></button>
  </form>
</body>
</html>
