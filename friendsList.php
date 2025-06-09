<html> 
    <head><title>친구목록</title></head>
<body>
    감정청<br><br>
    내 친구목록<br>
    <form action="/friends.php" method="get">
        <label for="friends_pkey">Friends</label>
         <select name="friends_pkey" id="friends_pkey">
             <?php
                include("friendsRequest.php");
             ?>
        </select>
        <input type="submit" value="친구 상세보기">
    </form>
</body>
</html>