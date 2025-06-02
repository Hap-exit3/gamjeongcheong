<html> 
    <head><title>친구목록</title></head>
<body>
    감정청<br><br>
    친구가 있나요?<br>
    <form action="/friends.php" method="post">
        <label for="friend">Friends</label>
  <select name="Friends List" id="friend">
    <option value="권도희">권도희</option>
    <?php
    include("friendsRequest.php");
    ?>
      </select>
       <input type="submit" value="Submit"/>
    </form>
</body>
</html>