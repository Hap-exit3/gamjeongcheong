<?php
// DB 연결
$conn = new mysqli("localhost", "root", "1234", "gamjeongcheongdb");
if ($conn->connect_error) {
    die("DB 연결 실패: " . $conn->connect_error);
}

// ✅ 최근 5일치 감정 데이터 (서로 다른 날짜 기준)
$recentQuery = "
    SELECT day, AVG(emotions_pkey) AS emotion
    FROM (
        SELECT DISTINCT DATE(insert_date) AS day
        FROM diary_entry
        WHERE users_pkey = 1
        ORDER BY day DESC
        LIMIT 5
    ) AS latest_days
    JOIN diary_entry ON DATE(diary_entry.insert_date) = latest_days.day
    WHERE users_pkey = 1
    GROUP BY day
    ORDER BY day ASC
";



$recentResult = $conn->query($recentQuery);
$recentDataPoints = [];
while ($row = $recentResult->fetch_assoc()) {
    $recentDataPoints[] = [
        "x" => $row['day'],
        "y" => round($row['emotion'], 2)
    ];
}
$recentDataPoints = array_reverse($recentDataPoints); // 날짜 오름차순

// ✅ 요일별 감정 평균 데이터
$weekdayQuery = "
    SELECT 
        DAYOFWEEK(insert_date) AS weekday, 
        AVG(emotions_pkey) AS avg_emotion 
    FROM diary_entry 
    WHERE users_pkey = 1 
    GROUP BY weekday
";
$weekdayResult = $conn->query($weekdayQuery);

// 요일 이름 매핑 (1=일, 2=월, ..., 7=토)
$weekdayNames = ['일', '월', '화', '수', '목', '금', '토'];
$weekdayEmotions = array_fill(0, 7, null);
while ($row = $weekdayResult->fetch_assoc()) {
    $index = (int)$row['weekday'] - 1;
    $weekdayEmotions[$index] = round($row['avg_emotion'], 2);
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>감정청 통계</title>
    <!-- ✅ Chart.js 및 시간 축 어댑터 추가 -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/luxon@3"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-luxon@1"></script>
</head>
<body>
    <h2>📈 최근 감정 그래프</h2>
    <canvas id="recentChart"></canvas>

    <h2>📊 요일별 감정 그래프</h2>
    <canvas id="weekdayChart"></canvas>

    <script>
        // ✅ 최근 감정 데이터 (x: 날짜, y: 감정)
        const recentData = <?= json_encode($recentDataPoints) ?>;

        new Chart(document.getElementById('recentChart'), {
            type: 'line',
            data: {
                datasets: [{
                    label: '감정 단계',
                    data: recentData,
                    borderColor: 'black',
                    backgroundColor: 'lightgray',
                    tension: 0.3
                }]
            },
            options: {
                scales: {
                    x: {
                        type: 'time',
                        time: {
                            unit: 'day',
                            tooltipFormat: 'yyyy-MM-dd',
                            displayFormats: {
                                day: 'yyyy-MM-dd'
                            }
                        },
                        title: {
                            display: true,
                            text: '날짜'
                        }
                    },
                    y: {
                        min: 1,
                        max: 3,
                        ticks: { stepSize: 1 },
                        title: {
                            display: true,
                            text: '감정 단계'
                        }
                    }
                }
            }
        });

        // ✅ 요일별 감정 데이터
        const weekdayLabels = <?= json_encode(array_values($weekdayNames)) ?>;
        const weekdayData = <?= json_encode(array_values($weekdayEmotions)) ?>;

        new Chart(document.getElementById('weekdayChart'), {
            type: 'bar',
            data: {
                labels: weekdayLabels,
                datasets: [{
                    label: '평균 감정',
                    data: weekdayData,
                    backgroundColor: 'skyblue'
                }]
            },
            options: {
                scales: {
                    y: {
                        min: 1,
                        max: 3,
                        ticks: { stepSize: 0.5 },
                        title: {
                            display: true,
                            text: '감정 단계'
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>
