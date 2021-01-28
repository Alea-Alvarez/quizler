<?php 

// Edit or add a question

session_start();
include("inc/dbOpenConn.php");
include("inc/functions.php");

// Below is to not report index error if a querystring or post variable is missing
error_reporting( error_reporting() & ~E_NOTICE );

if ($_SESSION["loggedIn"] != 1) authFail();
if ($_SESSION["permission"] != "ADMIN") authFail();

// Load in variables from form submission
$quizid = $_POST["quizid"];
$questionid = $_POST["questionid"];
$questiontext = $_POST["questiontext"];
$questiontext = trim($questiontext);
$option1 = $_POST["option1"];
$option1 = trim($option1);
$option2 = $_POST["option2"];
$option2 = trim($option2);
$option3 = $_POST["option3"];
$option3 = trim($option3);
$option4 = $_POST["option4"];
$option4 = trim($option4);
$option5 = $_POST["option5"];
$option5 = trim($option5);
$missedcomment = $_POST["missedcomment"];
$missedcomment = trim($missedcomment);
$answerkey = $_POST["answerkey"];

if (!is_numeric($quizid) || !is_numeric($questionid)) header("Location: managequestions.php"); // If quiz ID or question ID is not passed as a number then go back.
$rejectStr = "";

// Check user input
if ($questiontext == "") $rejectStr .= "Question is missing or invalid.<br />";
if ($option1 == "") $rejectStr .= "Option 1 is missing or invalid.<br />";
if ($option2 == "") $rejectStr .= "Option 2 is missing or invalid.<br />";
if ($answerkey == "option1" && $option1 == "") $rejectStr .= "You have selected Option 1 as correct answer, but Option 1 is blank!<br />";
if ($answerkey == "option2" && $option2 == "") $rejectStr .= "You have selected Option 2 as correct answer, but Option 2 is blank!<br />";
if ($answerkey == "option3" && $option3 == "") $rejectStr .= "You have selected Option 3 as correct answer, but Option 3 is blank!<br />";
if ($answerkey == "option4" && $option4 == "") $rejectStr .= "You have selected Option 4 as correct answer, but Option 4 is blank!<br />";
if ($answerkey == "option5" && $option5 == "") $rejectStr .= "You have selected Option 5 as correct answer, but Option 5 is blank!<br />";

// if $rejectStr is not empty then user input is no good, need to reject.
if ($rejectStr != "") rejectForm($rejectStr);

if ($questionid == -1) { // this is an add new question operation

  // We need to get the highest question number for this quiz so we can assign a higher number
  $sql = "select id from question where quizid = " . $quizid . " order by id desc";
  $result = $conn->query($sql);
  $row = $result->fetch_assoc();
  $lastid = $row["id"];
  $lastid++;  // add 1 to last id to get new question number

  // Check connection first just in case
  if ($conn->connect_error) {
      ohNo();
  }

  $sql = "INSERT INTO question (quizid, id, questiontext, option0, option1, option2, option3, option4, option5, missedcomment, answerkey) ";
  $sql .= "VALUES (". $quizid . "," . $lastid . ",'" . $questiontext . "','No Response','" . $option1 . "','" . $option2 . "','" . $option3 . "','" . $option4 . "','" . $option5 . "','" . $missedcomment . "','" . $answerkey . "')";

  if ($conn->query($sql) === TRUE) {
      // Do nothing, continue displaying the success page below
  } else {
      ohNo(); // Bail!
  }

} else { // We are updating an existing question

  // Check connection first just in case
  if ($conn->connect_error) {
      ohNo();
  }

  $sql = "UPDATE question set questiontext = '" . $questiontext . "', option1 = '" . $option1 . "', option2 = '" . $option2 . "', option3 = '" . $option3 . "', option4 = '" . $option4 . "', option5 = '" . $option5 . "', missedcomment = '" . $missedcomment . "', answerkey = '" . $answerkey . "' ";
  $sql .= "WHERE quizid = " . $quizid . " and id = " . $questionid;

  if ($conn->query($sql) === TRUE) {
      // Do nothing, continue displaying the success page below
  } else {
      ohNo(); // Bail!
  }

}

$conn->close(); 

header("Location: managequestions.php?s=1");

?>

<script src="js/bootstrap.bundle.min.js"></script>

</body>
</html>
