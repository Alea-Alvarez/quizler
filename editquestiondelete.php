<?php 

// Delete a question

session_start();

include("inc/dbOpenConn.php");
include("inc/functions.php");

// Below is to not report index error if a querystring or post variable is missing
error_reporting( error_reporting() & ~E_NOTICE );

if ($_SESSION["loggedIn"] != 1) authFail();
if ($_SESSION["permission"] != "ADMIN") authFail();

// Get QuizID and questionID passed in querystring to delete
$questionid = $_GET["id"];
$quizid = $_GET["qid"];
if (!is_numeric($quizid) || !is_numeric($questionid)) header("Location: managequestions.php"); // If IDs are not passed as a number then go back.

// Input seems good, let's verify ID exists
$sql = "select * from question where id = " . $questionid . " and quizid = " . $quizid;
$result = $conn->query($sql);
if ($result->num_rows == 0) { // does not exist
  $rejectStr = "Question not found. Please enter a valid question and quiz ID or perhaps this question was recently deleted?<br />";
  rejectForm($rejectStr);
}

// We need to get the last question number this quiz has in order to move questions around.
// Questions must be moved around because we cannot have gaps in the question numbers.
$sql = "select id from question where quizid = " . $quizid . " order by id desc";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$lastid = $row["id"];

// If we made it this far it's okay to delete the question

// Check connection first just in case
if ($conn->connect_error) {
    ohNo();
}

$sql = "delete from question where id = " . $questionid . " and quizid = " . $quizid;
if ($conn->query($sql) === TRUE) {
    // Do nothing
} else {
    ohNo(); // Bail!
}

// If this wasn't the very last question then we need to change the questionID of the last question to fill in the numerical gap.
if ($questionid != $lastid) {
  $sql = "update question set id = " . $questionid . " where id = " . $lastid . " and quizid = " . $quizid;
  if ($conn->query($sql) === TRUE) {
      // Do nothing
  } else {
      ohNo(); // Bail!
  }
}

$conn->close(); 

header("Location: managequestions.php?d=1");

?>

<script src="js/bootstrap.bundle.min.js"></script>

</body>
</html>
