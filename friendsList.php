<html> 
    <head><title>친구목록</title></head>
<body>
    감정청<br><br>
    내 친구목록<br>
    <form action="/friends.php" method="post">
        <label for="friend">Friends</label>
  <select name="Friends List" id="friend">
    <?php
    include("friendsRequest.php");
    ?>
    </select>
    <input type="submit" value="친구 상세보기">
    </form>
</body>
</html>