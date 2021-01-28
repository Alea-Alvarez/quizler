<?php
session_start();

include("inc/functions.php");
include("inc/standardHead.php");
include("inc/dbOpenConn.php");

// Below is to not report index error if a querystring variable is missing, which is expected.
// error_reporting( error_reporting() & ~E_NOTICE );

if ($_SESSION["loggedIn"] != 1) authFail();

?>

<body>

<!-- Navigation -->
<nav class="navbar navbar-expand-sm bg-success navbar-dark static-top">  
  <div class="container">
    <a class="navbar-brand" href="#">
          <img src="images/logo-small.png" alt="Quizler">
        </a><p class="mb-2 text-white">Quizler!<br /><b>User Dashboard</b></p>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
    <div class="collapse navbar-collapse" id="navbarResponsive">
      <ul class="navbar-nav ml-auto">
        <li class="nav-item active">
          <a class="nav-link" href="javascript: history.go(-1)">Previous Page<span class="sr-only">(current)</span></a>
        </li>
        <li class="nav-item active">
          <a class="nav-link" href="logout.php">Log Out<span class="sr-only">(current)</span></a>
        </li>
        <li class="nav-item active">
          <a class="nav-link" href="contactus.php">Contact Us<span class="sr-only">(current)</span></a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<!-- Page Content -->
<div class="container">

<h3 class="mt-3">Welcome <?php echo $_SESSION["firstName"] ?>!</h3>
<p class="mt-3">You have the following quizzes assigned to you. Click on a quiz title to take the assigned quiz, or click the "Search for Quiz" button to select a different quiz from the menu.</p>

<?php 

$sql = "select assignment.id, assignment.quizid, assignment.dateassigned, quiz.title, quiz.numofquestions from assignment inner join quiz on assignment.quizid = quiz.id where assignment.userid = " . $_SESSION["UserID"] . " and resulttext not in('PASS','FAIL')";
$result = $conn->query($sql);
if ($result->num_rows == 0) {
  echo "<ul><li><i>No quizzes currently assigned to you.</i></li></ul>";
} else {
  echo "<ul>";
  $rowCount = 0;
  while($row = $result->fetch_assoc()) {  // Loop through assigned quizzes
    $rowCount++;
    if ($rowCount > 1) echo "<br />"; // We want a leading <br> element for all except the first quiz displayed to control spacing properly
    echo "<li><a href='userquiz.php?q=" . $row["quizid"] . "&id=" . $row["id"] . "'><b>" . $row["title"] . "</b></a><br />";
   // echo $row["numofquestions"] . " questions - ";
    $new_date = date("m/d/y", strtotime($row["dateassigned"]));
    echo "<i>assigned to you on " . $new_date . "</i></li>";
  }  
  echo "</ul>";
}

?>

<div align="left">
  <button type="button" onclick="window.location.href='quizselect.php'" class="btn btn-success">Search for Quiz</a></button>
  <hr>

  <p class="mt-3"><b>Recent quizzes taken:</b><br /><i>Click on quiz title to take quiz again.</i></p>

  <?php

  $sql = "select assignment.assignmenttext, assignment.resulttext, assignment.resultperc, assignment.id, assignment.quizid, assignment.dateassigned, quiz.title, quiz.numofquestions, assignment.lastdatetaken from assignment inner join quiz on assignment.quizid = quiz.id where assignment.userid = " . $_SESSION["UserID"] . " and resulttext in('PASS','FAIL') order by lastdatetaken desc limit 7";
  $result2 = $conn->query($sql);

  if ($result2->num_rows == 0) {
    echo "<ul><li><i>No recent quizzes taken.</i></li></ul>";
  } else {
    echo "<ul>";
    while($row2 = $result2->fetch_assoc()) {  // Loop through recent quizzes
      $new_date = date("m/d/y", strtotime($row2["lastdatetaken"]));

      // Let's see if admin-assigned quiz and tag as such if yes otherwise self-assigned.
      $tempAssigned = $row2["assignmenttext"]; 
      if ($tempAssigned == "Admin-Assigned Quiz") {
        $tempAssigned = " <i>(Admin-Assigned)</i>";
      } else {
        $tempAssigned = " <i>(Self-Assigned)</i>";
      }

      echo "<li><a href='userquiz.php?q=" . $row2["quizid"] . "&id=" . $row2["id"] ."'><b>" . $row2["title"] . "</b></a>" . $tempAssigned . "<br />";
      echo "<i>" . $new_date . " - ";
      if ($row2["resulttext"] == "FAIL") { echo "<font color='red'>"; } else { echo "<font color='green'>"; }
      echo "Score " . $row2["resultperc"] * 100 . "% (" . $row2["resulttext"] . ")</i></font></li><br />";
    }  
    echo "</ul>";
  }

  $conn->close(); 

  ?>

  <?php include("inc/footer.php"); ?>

</div>


</div>
<!-- /.container -->

<script src="js/bootstrap.bundle.min.js"></script>

</body>
</html>