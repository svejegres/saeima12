<?php include('app/backend-logic.php'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <!-- Page title -->
  <title>Latvian politicians from 12th Saeima</title>

  <!-- Meta tags -->
  <meta charset="UTF-8">
  <meta name="viewport"
        content="width=device-width, height=device-height, initial-scale=1.0, minimum-scale=1.0"/>

  <meta name="description"
        content="List of Latvian politicians from 12th Saeima."/>

  <!-- Icon -->
  <link rel="icon"
        href="assets/images/favicon.ico">

  <!-- Styles -->
  <link rel="stylesheet"
        href="assets/css/main.css" async>

  <!-- Javascript -->
  <script src="https://code.jquery.com/jquery-3.4.1.min.js"
          integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
          crossorigin="anonymous">
  </script>
  <script src="assets/js/main.js" defer></script>
</head>
<body>
  <div id="content-wrapper">
    <div id="search-bar">
      <input type="text" placeholder="Search by name . . ." value="<?php if($_SESSION["search_keyword"]) echo $_SESSION["search_keyword"];?>">
    </div>
    <ul id="politicians-list">
      <?php echo $politicians; ?>
    </ul>
    <div id="pagination">
      <?php echo "$prevlink Page $page of $pages pages $nextlink"; ?>
    </div>
  </div>
</body>
</html>