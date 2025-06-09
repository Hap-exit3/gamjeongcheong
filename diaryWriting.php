<?php
include("auth.php"); 


// DB 연결
$conn = new mysqli("localhost", "root", "1234", "gamjeongcheongdb");
if ($conn->connect_error) {
    die("DB 연결 실패: " . $conn->connect_error);
}

// 현재 단계 처리
$step = $_POST['step'] ?? 'step1';
$weather_id = $_POST['weather_id'] ?? null;
?>
<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="UTF-8">
  <title>감정청 - 일기 작성</title>
  <style>
    body {
      font-family: sans-serif;
      background-color: #fffdf8;
      padding: 30px;
      max-width: 600px;
      margin: auto;
    }
    h2 {
      margin-bottom: 20px;
    }
    .box {
      border: 2px solid #333;
      padding: 15px;
      margin-bottom: 20px;
      background-color: #ffffff;
    }
    .option-group {
      display: flex;
      flex-direction: column;
      gap: 10px;
    }
    label {
      display: flex;
      align-items: center;
      gap: 10px;
      font-size: 18px;
    }
    input[type="radio"] {
      transform: scale(1.2);
    }
    input[type="text"],
    textarea {
      width: 100%;
      padding: 10px;
      font-size: 16px;
      box-sizing: border-box;
      resize: none;             /* ❌ 크기 수동 조절 막기 */
      overflow-y: auto;
    }
    button {
      padding: 10px 20px;
      font-size: 16px;
      margin-top: 20px;
    }
    .button-area {
      text-align: center;
    }
  </style>
</head>
<body>

<?php if ($step === 'step1'): ?>
  <h2>☁️ 오늘의 날씨는 어떤가요?</h2>
  <form method="POST" action="">
    <input type="hidden" name="step" value="step2">

    <div class="option-group">
      <?php
      $weather_sql = "SELECT pkey, description, emoji FROM weathers";
      $weather_result = $conn->query($weather_sql);
      if ($weather_result->num_rows > 0):
        while ($row = $weather_result->fetch_assoc()):
          $pkey = htmlspecialchars($row['pkey']);
          $desc = htmlspecialchars($row['description']);
          $emoji = htmlspecialchars($row['emoji']);
          $checked = ($weather_id == $pkey) ? 'checked' : '';
      ?>
        <label>
          <input type="radio" name="weather_id" value="<?= $pkey ?>" required <?= $checked ?>>
          <?= $emoji ?> <?= $desc ?>
        </label>
      <?php endwhile; else: ?>
        <p>등록된 날씨가 없습니다.</p>
      <?php endif; ?>
    </div>

    <div class="button-area">
      <button type="submit">일기 작성하기</button>
    </div>
  </form>

<?php elseif ($step === 'step2'): ?>
  <?php
  // 날짜 계산
  $today = date("Y년 m월 d일 (D)");

  // 날씨 이모지+텍스트 추출
  $weather_text = "날씨 정보 없음";
  if ($weather_id) {
    $weather_stmt = $conn->prepare("SELECT emoji, description FROM weathers WHERE pkey = ?");
    $weather_stmt->bind_param("i", $weather_id);
    $weather_stmt->execute();
    $weather_result = $weather_stmt->get_result();
    if ($weather_row = $weather_result->fetch_assoc()) {
      $weather_text = htmlspecialchars($weather_row['emoji']) . ' ' . htmlspecialchars($weather_row['description']);
    }
  }
  ?>

  <h2>📖 감정과 오늘의 일기</h2>
  <form action="save_diary.php" method="POST">
    <input type="hidden" name="weather_id" value="<?= htmlspecialchars($weather_id) ?>">
    <input type="hidden" name="user_id" value="1">

    <!-- 날짜 + 날씨 -->
    <div class="box">
      <p><strong>📅 날짜:</strong> <?= $today ?></p>
      <p><strong>🌤 날씨:</strong> <?= $weather_text ?></p>
    </div>

    <!-- 감정 선택 -->
    <div class="box">
      <label>오늘의 감정은?</label>
      <div class="option-group">
        <?php
        $emotion_sql = "SELECT pkey, description, emoji FROM emotions";
        $emotion_result = $conn->query($emotion_sql);
        if ($emotion_result->num_rows > 0):
          while ($row = $emotion_result->fetch_assoc()):
            $pkey = htmlspecialchars($row['pkey']);
            $desc = htmlspecialchars($row['description']);
            $emoji = htmlspecialchars($row['emoji']);
        ?>
          <label>
            <input type="radio" name="emotion_id" value="<?= $pkey ?>" required>
            <?= $emoji ?> <?= $desc ?>
          </label>
        <?php endwhile; else: ?>
          <p>등록된 감정이 없습니다.</p>
        <?php endif; ?>
      </div>
    </div>

    <!-- 해시태그 -->
    <div class="box">
      <label>키워드</label>
      <div id="hashtag-wrapper" style="display: flex; flex-wrap: wrap; gap: 8px;"></div>
      <button type="button" id="add-hashtag-btn">+ 태그 추가</button>
    </div>


    <!-- 메모 -->
    <div class="box">
      <label>메모</label>
      <textarea name="content" rows="6" placeholder="오늘 하루는 어땠나요?" required></textarea>
    </div>

    <!-- 저장 버튼 -->
    <div class="button-area">
      <button type="submit">저장</button>
    </div>
  </form>

<script>
  const wrapper = document.getElementById('hashtag-wrapper');
  const addBtn = document.getElementById('add-hashtag-btn');

  function createTagInput() {
    const input = document.createElement('input');
    input.type = 'text';
    input.placeholder = '#태그입력';
    input.style.padding = '5px';
    input.style.fontSize = '14px';
    input.style.minWidth = '80px';

    input.addEventListener('keydown', function (e) {
      if (e.key === 'Enter' && input.value.trim() !== '') {
        e.preventDefault();

        const tagText = input.value.trim().replace(/^#/, '');
        if (!tagText) return;

        // 보여지는 태그 span
        const tagSpan = document.createElement('span');
        tagSpan.textContent = '#' + tagText;
        tagSpan.style.padding = '5px 10px';
        tagSpan.style.backgroundColor = '#eee';
        tagSpan.style.borderRadius = '20px';
        tagSpan.style.fontSize = '14px';

        // 실제 넘겨줄 hidden input
        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = 'hashtags[]';
        hiddenInput.value = tagText;

        wrapper.replaceChild(tagSpan, input);
        wrapper.appendChild(hiddenInput);

        // 다시 +버튼 보이게
        addBtn.style.display = 'inline-block';
      }
    });

    wrapper.appendChild(input);
    input.focus();
    addBtn.style.display = 'none';
  }

  addBtn.addEventListener('click', createTagInput);
</script>



  <!-- ✨ textarea 자동 높이 확장 스크립트 -->
  <script>
    const textarea = document.querySelector('textarea');
    textarea.addEventListener('input', () => {
      textarea.style.height = 'auto';
      textarea.style.height = textarea.scrollHeight + 'px';
    });
  </script>

<?php endif; ?>

</body>
</html>
