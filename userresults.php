<?php
session_start();

include("inc/functions.php");
include("inc/standardHead.php");
include("inc/dbOpenConn.php");

if ($_SESSION["loggedIn"] != 1) authFail();

// Below is to not report index error if a querystring variable is missing, which is expected.
error_reporting( error_reporting() & ~E_NOTICE );

// Load up some vars
$PassPercent = $_SESSION["passperc"];
$QuizID = $_SESSION["quizId"];
$NumOfQuestions = $_SESSION["numofquestions"];
$QOrder = $_SESSION['QOrder'];

// Just check some values and if not present redirect to user dashboard
if ($PassPercent == "" || $QOrder == "") header("Location: userdash.php");

// Split the contents of QOrder (the order of the questions given) into an array
$ArrQuestions=explode(",",$QOrder);
$Correct = 0;

for ($Looper=0; $Looper<=$NumOfQuestions-1; $Looper++) { // 0 to $NumOfQuestions - 1 is because the explode function starts at element 0

  include("inc/dbOpenConn.php");
  $sql = "select * from question where quizid = " . $QuizID . " and id = " . $ArrQuestions[$Looper];
  $result = $conn->query($sql);
  $row = $result->fetch_assoc();

  $TotalQuestions++;

  if (strtoupper($_POST[$ArrQuestions[$Looper]]) == strtoupper($row["answerkey"]))
  {
    // Correct
    $Correct++;
  }
  else
  {
    // Wrong
    if (strtoupper($_POST[$ArrQuestions[$Looper]]) != "OPTION0") { // User provided a response 
      $WrongString .= "<B>" . $TotalQuestions . ") " . $row["questiontext"] . "<br /></b><font color =\"orange\">" . $row["missedcomment"] . "</font><br /><br />";
    } else { // User did not provide a reponse
      $WrongString .= "<B>" . $TotalQuestions . ") " . $row["questiontext"] . "<br /></b><font color =\"red\">You did not provide a response to this question.</font><br /><br />";
    }

    // need to get the actual correct answer for AnswerString
    if (strtoupper($row["answerkey"])=="OPTION0") $TheAnswer=$row["option0"];
    if (strtoupper($row["answerkey"])=="OPTION1") $TheAnswer=$row["option1"];
    if (strtoupper($row["answerkey"])=="OPTION2") $TheAnswer=$row["option2"];
    if (strtoupper($row["answerkey"])=="OPTION3") $TheAnswer=$row["option3"];
    if (strtoupper($row["answerkey"])=="OPTION4") $TheAnswer=$row["option4"];
    if (strtoupper($row["answerkey"])=="OPTION5") $TheAnswer=$row["option5"];
    $AnswerString .= "<B>" . $row["questiontext"] . "<br /><br /></b><font color =\"orange\">Correct answer: " . $TheAnswer . "</font><br /><br />";

  }

} // End of for loop


// Did they pass the quiz?
if ($Correct / $TotalQuestions < $PassPercent)
{
  $PassedQuiz = false;
  $TextforQuery = "FAIL";
}
  else
{
  $PassedQuiz = true;
  $TextforQuery = "PASS";
}

// Now to update the assignment table with the results!!

// Let's see if we have an assignment ID, if we do we need to update that specific record
if ($_SESSION["AssignmentID"] > 0) {
  $sql = "UPDATE assignment SET lastdatetaken = '" . date("Y-m-d") . "', resultperc = " . round($Correct / $TotalQuestions, 2) . ", resulttext = '" . $TextforQuery . "' WHERE id = " . $_SESSION["AssignmentID"];
  if ($conn->query($sql) === TRUE) {
      // Do nothing, update was successful
  } else {
      ohNo(); // Something went wrong
  }
  $conn->close(); 

} else  { // No assignment ID

  // We need to see if user has already attempted this quiz today, and update the record if that is the case
  $sql = "select id, quizid, userid, dateassigned from assignment where userid = " . $_SESSION["UserID"] . " and quizid = " . $QuizID . " and dateassigned = '" . date("Y-m-d") . "'";
  $result2 = $conn->query($sql);
  if ($result2->num_rows != 0) { // Record exists, need to update!
    $row2 = $result2->fetch_assoc();
    $sql = "UPDATE assignment SET lastdatetaken = '" . date("Y-m-d") . "', resultperc = " . round($Correct / $TotalQuestions, 2) . ", resulttext = '" . $TextforQuery . "' WHERE id = " . $row2["id"];
    if ($conn->query($sql) === TRUE) {
        // Do nothing, update was successful
    } else {
        ohNo(); // Something went wrong
    }
    $conn->close(); 

  } else { // No record exists, let's insert a new record!

    $sql = "INSERT INTO assignment (assignmenttext,recorddatecreated,userid,quizid,dateassigned,lastdatetaken,resulttext,resultperc)";
    $sql .= "VALUES ('Self-Assigned Quiz','" . date("Y-m-d") . "'," . $_SESSION["UserID"] . "," . $QuizID . ",'" . date("Y-m-d") . "','" . date("Y-m-d") . "','" . $TextforQuery . "'," . round($Correct / $TotalQuestions, 2) .")";
    if ($conn->query($sql) === TRUE) {
        // Do nothing, insert was successful
    } else {
        ohNo(); // Something went wrong!
    }
    $conn->close(); 

  }

}

// Reset assignmentID session var
$_SESSION["AssignmentID"] = 0;

?>

<!-- Navigation -->
<nav class="navbar navbar-expand-sm bg-success navbar-dark static-top">  
  <div class="container">
    <a class="navbar-brand" href="#">
          <img src="images/logo-small.png" alt="Quizler">
        </a><p class="mb-2 text-white">Quizler!<br /><b>User Quiz</b></p>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
    <div class="collapse navbar-collapse" id="navbarResponsive">
      <ul class="navbar-nav ml-auto">
        <li class="nav-item active">
          <a class="nav-link" href="userdash.php">User Dashboard<span class="sr-only">(current)</span></a>
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

<?php if ($PassedQuiz == False) { // Failed the quiz ?>

  <h3 class="mt-3"><?php echo strtoupper($_SESSION["title"]) ?> RESULTS: <font color="RED">FAIL</font></h3> 
  Sorry <?php echo $_SESSION["firstName"] ?>, you did not pass the quiz. <b>You answered <?php echo $Correct ?> out of <?php echo $TotalQuestions ?> correctly, which is <?php echo round(($Correct / $TotalQuestions) * 100) ?>%</b> and you needed a score of <?php echo $PassPercent * 100 ?>% or better to pass the <?php echo $_SESSION["title"] ?> quiz. Please review the questions you missed below and <a href="javascript: history.go(-1)">take the quiz again</a>.<br /><br />
  <B>PLEASE REVIEW THE FOLLOWING QUESTIONS:</b><HR><p align="left">
  <?php echo $WrongString ?>

<?php } else { // Passed the quiz ?>

  <h3 class="mt-3"><?php echo strtoupper($_SESSION["title"]) ?> QUIZ RESULTS: <font color="GREEN">PASS</font></h3> 
  <p>Congratulations <?php echo $_SESSION["firstName"] ?>, <b>you answered <?php echo $Correct ?> out of <?php echo $TotalQuestions ?> correctly for a score of <?php echo round(($Correct / $TotalQuestions) * 100) ?>%.</b> The minimum required to pass the quiz was <?php echo $PassPercent * 100 ?>%.</p>

  <?php if (trim($WrongString)!="") { ?>
  <b>NOTE:</b> Although you passed the quiz, you did miss some questions. <a href="<?php echo $_SESSION["reviewPage"] ?>" target="_top">Please click here to review the questions you missed</a>.<br /><br />
  <?php $_SESSION["review"] = $AnswerString ?>
  <?php } ?>

<?php } ?>

<button type="button" onclick="window.location.href='userdash.php'" class="btn btn-success">Back to Dashboard</button>&nbsp;&nbsp;&nbsp;<button type="button" onclick="window.location.href='javascript: history.go(-1)'" class="btn btn-success">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Retake quiz&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</button>
<br /><br />

<?php include("inc/footer.php"); ?>

</div>
<!-- /.container -->

<script src="js/bootstrap.bundle.min.js"></script>
</body></html>