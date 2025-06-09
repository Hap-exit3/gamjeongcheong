<?php
include("auth.php");
$conn = new mysqli("localhost", "root", "1234", "gamjeongcheongdb");
if ($conn->connect_error) {
    die("DB 연결 실패: " . $conn->connect_error);
}

$tag = $_GET['tag'] ?? '';
if (!$tag) {
    echo "❌ 태그가 없습니다.";
    exit;
}

// 태그 pkey 가져오기
$stmt = $conn->prepare("SELECT pkey FROM tag WHERE name = ?");
$stmt->bind_param("s", $tag);
$stmt->execute();
$stmt->bind_result($tag_pkey);
$stmt->fetch();
$stmt->close();

if (!$tag_pkey) {
    echo "<p>해당 태그에 해당하는 일기가 없습니다.</p>";
    exit;
}

// 일기 조회
$sql = "SELECT d.pkey, d.insert_date, d.contents, w.description AS weather
        FROM diary_entry d
        JOIN tag_search ts ON d.pkey = ts.diary_entry_pkey
        JOIN weathers w ON d.weathers_pkey = w.pkey
        WHERE ts.tag_pkey = ?
        ORDER BY d.insert_date DESC";
$list_stmt = $conn->prepare($sql);
$list_stmt->bind_param("i", $tag_pkey);
$list_stmt->execute();
$result = $list_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="UTF-8">
  <title>해시태그 검색 결과</title>
  <style>
    body {
      font-family: sans-serif;
      background-color: #fffaf5;
      padding: 30px;
      max-width: 700px;
      margin: auto;
    }
    h2 {
      margin-bottom: 20px;
    }
    .diary-card {
      border: 2px solid #ddd;
      border-radius: 10px;
      padding: 15px;
      margin-bottom: 20px;
      background-color: #ffffff;
      transition: 0.2s;
    }
    .diary-card:hover {
      background-color: #f0f0f0;
      border-color: #aaa;
    }
    .diary-card a {
      text-decoration: none;
      color: inherit;
      display: block;
      cursor: pointer;
    }
    .diary-card p {
      margin: 5px 0;
    }
  </style>
</head>
<body>
  <h2>🔍 해시태그 검색 결과: #<?= htmlspecialchars($tag) ?></h2>

  <?php while ($row = $result->fetch_assoc()): 
    $id = $row['pkey'];
    $date = date("Y년 m월 d일", strtotime($row['insert_date']));
    $weather = htmlspecialchars($row['weather']);
    $content = strip_tags($row['contents']);
    $preview = htmlspecialchars(substr($content, 0, 50));
    if (strlen($content) > 50) $preview .= "...";
  ?>
    <div class="diary-card">
      <a href="diaryDetail.php?entry_id=<?= $id ?>">
        <p>📅 <strong><?= $date ?></strong></p>
        <p>🌤 날씨: <?= $weather ?></p>
        <p>📝 내용: <?= $preview ?></p>
      </a>
    </div>
  <?php endwhile; ?>

</body>
</html>

<?php
$list_stmt->close();
$conn->close();
?>
