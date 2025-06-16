<html> 
    <head><title>친구추가하기</title></head>
<body>
    감정청<br><br>
    친구추가하기

    <form action="friendsList.php" method="get" style="margin: 0;">
            <input type="submit" value="유저 목록 보기">
        </form>
    <br>

    <form action="friendsInsert.php" method="post">
        


        <label>친구 후보:</label><br>
        <?php
        $users_pkey = $_COOKIE['users_pkey'] ?? null;
            ?>
        <select name="friends_pkey">
            <?php
            
            include("auth.php");
            $conn = new mysqli("localhost", "root", "1206", "gamjeongcheongdb");
            if ($conn->connect_error) die("DB 연결 실패");

            $sql = "SELECT pkey, name FROM users Where pkey != $users_pkey;";//본인제외외
            $result = $conn->query($sql);

            while ($row = $result->fetch_assoc()) {
                echo "<option value='{$row['pkey']}'>{$row['name']}</option>";
            }

            $conn->close();
            ?>
        </select><br><br>
        <input type="hidden" name="users_pkey" value="<?php echo $users_pkey; ?>"> <br>
        <input type="submit" value="친구 요청 보내기">
</body>
</html>