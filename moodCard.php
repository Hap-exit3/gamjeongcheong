<?php
session_start();

// DB 연결
$conn = new mysqli("localhost", "root", "1234", "gamjeongcheongdb");
if ($conn->connect_error) {
    die("DB 연결 실패: " . $conn->connect_error);
}

// 단계: 카드 선택 or 상세 보기
$selected_card_id = $_POST['mood_card_pkey'] ?? null;
$diary_entry_pkey = $_POST['diary_entry_pkey'] ?? 1; // 임시 테스트 값
?>
<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="UTF-8">
  <title>기분 전환 카드</title>
</head>
<body>
  <h2>🪄 기분 전환 카드</h2>

  <?php if (!$selected_card_id): ?>
    <!--카드 리스트 출력 -->
    <form method="POST">
      <input type="hidden" name="diary_entry_pkey" value="<?= $diary_entry_pkey ?>">
      <?php
      $sql = "SELECT pkey, title FROM mood_card";
      $res = $conn->query($sql);
      while ($row = $res->fetch_assoc()):
      ?>
        <button type="submit" name="mood_card_pkey" value="<?= $row['pkey'] ?>">
          <?= htmlspecialchars($row['title']) ?>
        </button><br><br>
      <?php endwhile; ?>
    </form>

  <?php else: ?>
    <!-- 카드 상세 내용 + 발급 -->
    <?php
    $sql = "SELECT * FROM mood_card WHERE pkey = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $selected_card_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $card = $result->fetch_assoc();
    ?>

    <h3><?= htmlspecialchars($card['title']) ?></h3>
    <p><?= nl2br(htmlspecialchars($card['description'])) ?></p>

    <form action="save_mood_card_log.php" method="POST">
      <input type="hidden" name="diary_entry_pkey" value="<?= $diary_entry_pkey ?>">
      <input type="hidden" name="mood_card_pkey" value="<?= $card['pkey'] ?>">
      <button type="submit">📥 발급받기</button>
    </form>

  <?php endif; ?>
</body>
</html>
