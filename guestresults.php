<?php
session_start();

include("inc/functions.php");
include("inc/standardHead.php");

// Below is to not report index error if a querystring variable is missing, which is expected.
error_reporting( error_reporting() & ~E_NOTICE );

// Load up some vars
$PassPercent = $_SESSION["passperc"];
$QuizID = $_SESSION["quizId"];
$NumOfQuestions = $_SESSION["numofquestions"];
$QOrder = $_SESSION['QOrder'];

// Just check some values and if not present redirect to guest home page.
if ($PassPercent == "" || $QOrder == "") header("Location: guesthome.php");

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

  $conn->close(); 

} // End of for loop


// Did they pass the quiz?
if ($Correct / $TotalQuestions < $PassPercent)
{
  $PassedQuiz = false;
}
  else
{
  $PassedQuiz = true;
}

?>

<!-- Navigation -->
<nav class="navbar navbar-expand-sm bg-success navbar-dark static-top">  
  <div class="container">
    <a class="navbar-brand" href="#">
          <img src="images/logo-small.png" alt="Quizler">
        </a><p class="mb-2 text-white">Quizler!<br /><b>Guest</b></p>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
    <div class="collapse navbar-collapse" id="navbarResponsive">
      <ul class="navbar-nav ml-auto">
        <li class="nav-item active">
          <a class="nav-link" href="index.php">Home<span class="sr-only">(current)</span></a>
        </li>
       <li class="nav-item active">
          <a class="nav-link" href="login.php">Log in<span class="sr-only">(current)</span></a>
        </li>
        <li class="nav-item active">
          <a class="nav-link" href="newaccount.php">Register<span class="sr-only">(current)</span></a>
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
  Sorry, you did not pass the quiz. <b>You answered <?php echo $Correct ?> out of <?php echo $TotalQuestions ?> correctly, which is <?php echo ($Correct / $TotalQuestions)*100 ?>%</b> and you needed a score of <?php echo $PassPercent * 100 ?>% or better to pass the <?php echo $_SESSION["title"] ?> quiz. Please review the questions you missed below and <a href="javascript: history.go(-1)">take the quiz again</a>.<br /><br />
  <B>PLEASE REVIEW THE FOLLOWING QUESTIONS:</b><HR><p align="left">
  <?php echo $WrongString ?>

<?php } else { // Passed the quiz ?>

  <h3 class="mt-3"><?php echo strtoupper($_SESSION["title"]) ?> QUIZ RESULTS: <font color="GREEN">PASS</font></h3> 
  <p>Congratulations, <b>you answered <?php echo $Correct ?> out of <?php echo $TotalQuestions ?> correctly for a score of <?php echo ($Correct / $TotalQuestions)*100 ?>%.</b> The minimum required to pass the quiz was <?php echo $PassPercent * 100 ?>%.</p>

  <?php if (trim($WrongString)!="") { ?>
  <b>NOTE:</b> Although you passed the quiz, you did miss some questions. <a href="<?php echo $_SESSION["reviewPage"] ?>" target="_top">Please click here to review the questions you missed</a>.<br /><br />
  <?php $_SESSION["review"] = $AnswerString ?>
  <?php } ?>

<?php } ?>

<button type="button" onclick="window.location.href='guesthome.php'" class="btn btn-success">Select another quiz</button>&nbsp;&nbsp;&nbsp;<button type="button" onclick="window.location.href='javascript: history.go(-1)'" class="btn btn-success">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Retake quiz&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</button>
<br /><br />

<?php include("inc/footer.php"); ?>

</div>
<!-- /.container -->

<script src="js/bootstrap.bundle.min.js"></script>
</body></html>