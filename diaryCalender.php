<?php

<<<<<<< HEAD
=======
include("auth.php");

>>>>>>> ed00c4b1cba9002f0e03c45d8b6e82eda8fe8d95
// DB 연결
$conn = new mysqli("localhost", "root", "1234", "gamjeongcheongdb");
if ($conn->connect_error) {
    die("DB 연결 실패: " . $conn->connect_error);
}

// 현재 월, 연도 가져오기
$year = date('Y');
$month = date('m');

// 현재 월의 일기 데이터 조회
$sql = "SELECT DATE_FORMAT(insert_date, '%Y-%m-%d') AS diary_date, emotions_pkey 
        FROM diary_entry 
        WHERE YEAR(insert_date) = ? AND MONTH(insert_date) = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $year, $month);
$stmt->execute();
$result = $stmt->get_result();

// 날짜별 감정 데이터 저장
$diaryData = [];
while($row = $result->fetch_assoc()) {
    $diaryData[$row['diary_date']] = $row['emotions_pkey'];
}

function getEmotionIcon($emotion) {
    switch($emotion) {
        case 1: return "😊"; // 예: 기쁨
        case 2: return "😢"; // 예: 슬픔
        case 3: return "😠"; // 예: 화남
        case 4: return "🌤️"; // 예: 맑음
        default: return "➕"; // 작성 안됨
    }
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>감정 캘린더</title>
    <style>
        body { font-family: 'Comic Sans MS', cursive; background-color: #fef5f0; }
        h1 { text-align: center; }
        table { margin: 0 auto; border-collapse: collapse; }
        td { width: 80px; height: 80px; text-align: center; vertical-align: middle; border: 1px solid #000; }
        .header { font-weight: bold; }
        .today { background-color: #ffebcd; }
    </style>
</head>
<body>
    <h1>감정 캘린더 🌤️</h1>
    <table>
        <tr class="header">
            <td>일</td><td>월</td><td>화</td><td>수</td><td>목</td><td>금</td><td>토</td>
        </tr>
        <?php
        // 달력 그리기
        $first_day = date('w', strtotime("$year-$month-01")); // 이번 달 1일의 요일
        $total_days = date('t', strtotime("$year-$month-01")); // 이번 달 총 일수

        $day = 1;
        for($week=0; $week<6; $week++) {
            echo "<tr>";
            for($weekday=0; $weekday<7; $weekday++) {
                if($week === 0 && $weekday < $first_day) {
                    echo "<td></td>";
                } elseif($day > $total_days) {
                    echo "<td></td>";
                } else {
                    $date_str = "$year-$month-" . str_pad($day, 2, '0', STR_PAD_LEFT);
                    $emotion = $diaryData[$date_str] ?? 0;
                    $icon = getEmotionIcon($emotion);
                    $link = "diaryRouter.php?date=$date_str";
                    $class = ($date_str === date('Y-m-d')) ? "today" : "";
                    echo "<td class='$class'><a href='$link'>$day<br>$icon</a></td>";
                    $day++;
                }
            }
            echo "</tr>";
            if($day > $total_days) break;
        }
        ?>
    </table>

    <div style="text-align:center; margin-top:20px;">
        <button onclick="window.location.href='diaryCalender.php?month=<?php echo $month-1; ?>'">지난 달 보기</button>
        <button onclick="window.location.href='diaryCalender.php?month=<?php echo $month+1; ?>'">다음 달 보기</button>
    </div>
</body>
</html>
