<?php
include("auth.php"); 


// DB 연결
$conn = new mysqli("localhost", "root", "1234", "gamjeongcheongdb");
if ($conn->connect_error) {
    die("DB 연결 실패: " . $conn->connect_error);
}

// GET으로 entry_id 받아오기
$entry_id = $_GET['entry_id'] ?? null;
if (!$entry_id) {
    echo " 일기 ID가 없습니다.";
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
    $contents = htmlspecialchars($row['contents']);
    $date = date("Y년 m월 d일 (D)", strtotime($row['entry_date']));  
    $weather = htmlspecialchars($row['weather']);
    $emotion = htmlspecialchars($row['emotion']);
} else {
    echo " 해당 일기를 찾을 수 없습니다.";
    exit;
}


// 해시태그 가져오기
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

// 감정별 이모지 맵
$emojiMap = [
    '기쁨' => '😊',
    '슬픔' => '😢',
    '화남' => '😠',
    '그냥 그렇다' => '😐',
];
$emotionIcon = $emojiMap[$emotion] ?? '🙂';
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>일기 예보 상세</title>
    <style>
        body {
            font-family: sans-serif;
            background-color: #fffaf5;
            padding: 20px;
        }
        main {
            max-width: 700px;
            margin: auto;
        }
        .box {
            border: 2px solid #333;
            padding: 15px;
            margin-bottom: 20px;
            background-color: #ffffff;
        }
        .keyword-box {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }
        .keyword-box span {
            border: 1px solid #888;
            padding: 5px 10px;
            border-radius: 5px;
            background-color: #f2f2f2;
        }
        .button-area {
            text-align: center;
            margin-top: 30px;
        }
        .button-area a {
            margin: 0 10px;
            text-decoration: none;
            color: #444;
            font-weight: bold;
        }
        .button-area a.delete {
            color: red;
        }
        .mood-card-box {
            border: 2px dashed #bbb;
            padding: 20px;
            text-align: center;
            margin-top: 30px;
            background-color: #fcfcfc;
        }
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
                    <a href="tagSearch.php?tag=<?= urlencode($tagName) ?>">
                        <span>#<?= htmlspecialchars($tagName) ?></span>
                    </a>
                <?php endforeach; ?>
            <?php else: ?>
                <p>해시태그 없음</p>
            <?php endif; ?>
        </div>
    </section>

    <section class="box">
        <h3>메모</h3>
        <p><?= $contents !== '' ? nl2br($contents) : '작성된 메모가 없습니다.' ?></p>
    </section>

    <section class="mood-card-box">
        <p>오늘은 기분이 <strong><?= $emotion ?></strong>네요.</p>
        <p><strong>기분 전환 카드를 발급하시겠습니까?</strong></p>
        <!--  여기에 mood card 컴포넌트 삽입 예정 -->
        <button onclick="alert('👉 카드 발급 센터로 연결 예정')">카드 발급 센터 바로가기</button>
    </section>

    <footer class="button-area">
        <a href="editDiary.php?entry_id=<?= $entry_id ?>">수정하기</a>
        <a href="deleteDiary.php?entry_id=<?= $entry_id ?>" class="delete" onclick="return confirm('정말 삭제하시겠습니까?')">삭제하기</a>
    </footer>
</main>
</body>
</html>
