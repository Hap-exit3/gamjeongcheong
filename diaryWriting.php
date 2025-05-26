<?php
session_start();

// DB 연결
$conn = new mysqli("localhost", "root", "1234", "gamjeongcheongdb");
if ($conn->connect_error) {
    die("DB 연결 실패: " . $conn->connect_error);
}

// 단계 처리
$step = $_POST['step'] ?? 'step1';
$weather_id = $_POST['weather_id'] ?? null;
?>
<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="UTF-8">
  <title>감정청 - 일기 작성</title>
</head>
<body>

<?php if ($step === 'step1'): ?>
  <h2>오늘의 날씨는 어떤가요?</h2>

  <form method="POST">
    <input type="hidden" name="step" value="step2">

    <?php
    $weather_sql = "SELECT pkey, description, emoji FROM weathers";
    $weather_result = $conn->query($weather_sql);
    while ($row = $weather_result->fetch_assoc()):
    ?>
      <label>
        <input type="radio" name="weather_id" value="<?= $row['pkey'] ?>">
        <?= htmlspecialchars($row['emoji']) ?> <?= htmlspecialchars($row['description']) ?>
      </label><br>
    <?php endwhile; ?>

    <br>
    <button type="submit">일기 작성하기</button>
  </form>

<?php elseif ($step === 'step2'): ?>
  <h2>감정과 오늘의 일기</h2>

  <form action="save_diary.php" method="POST">
    <input type="hidden" name="weather_id" value="<?= htmlspecialchars($weather_id) ?>">
    <input type="hidden" name="user_id" value="1">

    <label>오늘의 감정은?</label><br>
    <?php
    $emotion_sql = "SELECT pkey, description, emoji FROM emotions";
    $emotion_result = $conn->query($emotion_sql);
    while ($row = $emotion_result->fetch_assoc()):
    ?>
      <label>
        <input type="radio" name="emotion_id" value="<?= $row['pkey'] ?>">
        <?= htmlspecialchars($row['emoji']) ?> <?= htmlspecialchars($row['description']) ?>
      </label><br>
    <?php endwhile; ?>

    <br>
    <label>해시태그 (쉼표로 구분)</label><br>
    <input type="text" name="hashtags" placeholder="#휴식, #혼자만의시간"><br><br>

    <label>오늘의 내용</label><br>
    <textarea name="content" rows="8" cols="60" placeholder="오늘 하루는 어땠나요?"></textarea><br><br>

    <button type="submit">저장</button>
  </form>

<?php endif; ?>

</body>
</html>
