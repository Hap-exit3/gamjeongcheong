<html> 
    <head><title>친구추가하기</title></head>
<body>
    감정청<br><br>
    친구추가하기

    <form action="friendsList.php" method="get" style="margin: 0;">
            <input type="submit" value="친구 목록 보기">
        </form>
    <br>

    <form action="friendsInsert.php" method="post">
        <input type="hidden" name="users_pkey" value="3"> <br>


        <label>친구 후보:</label><br>
        <select name="friends_pkey">
            <?php
            include("auth.php");
            $conn = new mysqli("localhost", "root", "1206", "gamjeongcheongdb");
            if ($conn->connect_error) die("DB 연결 실패");

            $sql = "SELECT * FROM users WHERE pkey != 3"; // 본인 제외
            $result = $conn->query($sql);

            while ($row = $result->fetch_assoc()) {
                echo "<option value='{$row['pkey']}'>{$row['name']}</option>";
            }

            $conn->close();
            ?>
        </select><br><br>

        <!-- 상태 2는 친구요청 상태로 가정 -->
        <input type="hidden" name="status" value="2">
        <input type="submit" value="친구 요청 보내기">
</body>
</html>