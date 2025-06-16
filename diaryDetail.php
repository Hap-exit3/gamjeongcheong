<?php
include("auth.php");

// DB 연결
$conn = new mysqli("localhost", "root", "1234", "gamjeongcheongdb");
if ($conn->connect_error) {
    die("DB 연결 실패: " . $conn->connect_error);
}

$entry_id = $_GET['entry_id'] ?? null;
if (!$entry_id) {
    echo " 일기 ID가 없습니다.";
    exit;
}

// 수정 완료 처리
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newContent = $_POST['updated_content'] ?? '';
    $stmt = $conn->prepare("UPDATE diary_entry SET contents = ? WHERE pkey = ?");
    $stmt->bind_param("si", $newContent, $entry_id);
    $stmt->execute();
    echo "<script>alert('수정이 완료되었습니다.'); location.href='diaryDetail.php?entry_id=$entry_id';</script>";
    exit;
}

// 일기 상세 정보 조회
$sql = "SELECT d.contents, d.entry_date, 
               w.description AS weather, 
               e.description AS emotion
        FROM diary_entry d
        JOIN weathers w ON d.weathers_pkey = w.pkey
        JOIN emotions e ON d.emotions_pkey = e.pkey
        WHERE d.pkey = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $entry_id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $contents = $row['contents'];
    $date = date("Y년 m월 d일 (D)", strtotime($row['entry_date']));
    $weather = htmlspecialchars($row['weather']);
    $emotion = htmlspecialchars($row['emotion']);
} else {
    echo " 해당 일기를 찾을 수 없습니다.";
    exit;
}

// 해시태그
$tag_sql = "SELECT t.name FROM tag_search ts
            JOIN tag t ON ts.tag_pkey = t.pkey
            WHERE ts.diary_entry_pkey = ?";
$tag_stmt = $conn->prepare($tag_sql);
$tag_stmt->bind_param("i", $entry_id);
$tag_stmt->execute();
$tag_result = $tag_stmt->get_result();
$tags = [];
while ($tag_row = $tag_result->fetch_assoc()) {
    $tags[] = '#' . htmlspecialchars($tag_row['name']);
}

// 감정 이모지
$emojiMap = ['기쁨' => '😊', '슬픔' => '😢', '화남' => '😠', '그냥 그렇다' => '😐'];
$emotionIcon = $emojiMap[$emotion] ?? '🙂';

// 🎵 감정에 따라 음악 URL 가져오기
$music_url = '';
$music_sql = "SELECT music_url FROM emotion_music_map WHERE emotions_pkey = ? LIMIT 1";
$music_stmt = $conn->prepare($music_sql);
$music_stmt->bind_param("i", $emotion_id);
$music_stmt->execute();
$music_result = $music_stmt->get_result();
if ($music_row = $music_result->fetch_assoc()) {
    $music_url = $music_row['music_url'];
}


// 수정 모드 여부
$edit_mode = isset($_GET['edit']) && $_GET['edit'] === '1';
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>일기 예보 상세</title>
    <style>
        body { font-family: sans-serif; background-color: #fffaf5; padding: 20px; }
        main { max-width: 700px; margin: auto; }
        .box { border: 2px solid #333; padding: 15px; margin-bottom: 20px; background-color: #ffffff; }
        .keyword-box { display: flex; flex-wrap: wrap; gap: 8px; }
        .keyword-box span { border: 1px solid #888; padding: 5px 10px; border-radius: 5px; background-color: #f2f2f2; }
        .button-area { text-align: center; margin-top: 30px; }
        .button-area a, .button-area button { margin: 0 10px; text-decoration: none; color: #444; font-weight: bold; }
        .button-area a.delete { color: red; }
        .mood-card-box { border: 2px dashed #bbb; padding: 20px; text-align: center; margin-top: 30px; background-color: #fcfcfc; }
        textarea { width: 97%; height: 200px; padding: 10px; font-size: 16px; resize: none; }
    </style>
</head>
<body>
<main>
    <h2>📖 일기 예보 상세</h2>

    <section class="box">
        <p><strong>📅 날짜:</strong> <?= $date ?></p>
        <p><strong>🌤 날씨:</strong> <?= $weather ?></p>
        <p><strong><?= $emotionIcon ?> 감정:</strong> <?= $emotion ?></p>
    </section>

    <section class="box">
        <h3>키워드</h3>
        <div class="keyword-box">
            <?php if (count($tags) > 0): ?>
                <?php foreach ($tags as $tag): ?>
                    <?php $tagName = ltrim($tag, '#'); ?>
                    <a href="tagSearch.php?tag=<?= urlencode($tagName) ?>"><span>#<?= htmlspecialchars($tagName) ?></span></a>
                <?php endforeach; ?>
            <?php else: ?>
                <p>해시태그 없음</p>
            <?php endif; ?>
        </div>
    </section>

    <section class="box">
        <h3><?= $edit_mode ? '메모 수정 중...' : '메모' ?></h3>
        <?php if ($edit_mode): ?>
            <form method="POST" action="">
                <textarea name="updated_content"><?= htmlspecialchars($contents) ?></textarea>
                <div class="button-area">
                    <button type="submit">수정 완료하기</button>
                    <a href="diaryDetail.php?entry_id=<?= $entry_id ?>">취소</a>
                </div>
            </form>
        <?php else: ?>
            <p><?= $contents !== '' ? nl2br(htmlspecialchars($contents)) : '작성된 메모가 없습니다.' ?></p>
        <?php endif; ?>
    </section>

<section class="mood-card-box">
    <p>오늘은 기분이 <strong><?= $emotion ?></strong>네요.</p>
    <p><strong>기분 전환 카드를 발급하시겠습니까?</strong></p>

    <form action="moodCard.php" method="POST">
        <input type="hidden" name="diary_entry_pkey" value="<?= $entry_id ?>">
        <button type="submit">📥 카드 발급 센터 바로가기</button>
    </form>
</section>


    <?php if (!$edit_mode): ?>
    <footer class="button-area">
        <a href="diaryDetail.php?entry_id=<?= $entry_id ?>&edit=1">수정하기</a>
        <a href="#" class="delete" onclick="event.preventDefault(); if(confirm('정말 삭제하시겠습니까?')) confirmDelete(<?= $entry_id ?>)">삭제하기</a>


    </footer>
    <?php endif; ?>
</main>

<!-- 음악 자동 재생 -->
<?php if ($music_url): ?>
<audio autoplay loop>
    <source src="<?= htmlspecialchars($music_url) ?>" type="audio/mpeg">
    브라우저가 오디오를 지원하지 않습니다.
</audio>
<?php endif; ?>

<!-- 삭제 -->
<script>
function confirmDelete(entryId) {
    if (confirm("정말 삭제하시겠습니까?")) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = 'diaryEmpty.php';

        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'entry_id';
        input.value = entryId;
        form.appendChild(input);

        document.body.appendChild(form);
        form.submit();
    }
}
</script>

</body>
</html>

