<?php include("auth.php"); ?>
<html> 
    <head><title>친구목록</title></head>
<body>
    감정청<br><br>
    감정청 유저목록<br>
    <form action="/friends.php" method="post">
        <label for="friends_pkey">Friends</label>
         <select name="friends_pkey" id="friends_pkey">
             <?php
                include("friendsRequest.php");
             ?>
        </select>
        <input type="submit" value="팔로잉 상세보기">
        <br><br>
        <a href="friendsAdd.php">팔로잉 하기</a>
        <br>
        <a href="friendsIncommingRequest.php">팔로워 확인하기</a>
    </form>
</body>
</html>