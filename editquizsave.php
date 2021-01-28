<?php 

// Edit or add a quiz

session_start();
include("inc/dbOpenConn.php");
include("inc/functions.php");

// Below is to not report index error if a querystring or post variable is missing
error_reporting( error_reporting() & ~E_NOTICE );

if ($_SESSION["loggedIn"] != 1) authFail();
if ($_SESSION["permission"] != "ADMIN") authFail();

$id = $_POST["id"];
$title = $_POST["title"];
$description = $_POST["description"];
$numofquestions = $_POST["numofquestions"];
$passperc = $_POST["passperc"];
$subjectId = $_POST["subjectId"];

if (!is_numeric($id)) header("Location: managequizzes.php"); // If ID is not passed as a number then go back.
$rejectStr = "";

// Check user input
if ($title == "") $rejectStr .= "Title is missing or invalid.<br />";
if ($description == "") $rejectStr .= "Description is missing or invalid.<br />";
if ($subjectId == "") $rejectStr .= "Subject is missing or invalid.<br />";
if ($numofquestions == "") $rejectStr .= "Number of Questions is missing or invalid.<br />";
if ($passperc == "") $rejectStr .= "Pass Percentage is missing or invalid.<br />";

// if $rejectStr is not empty then user input is no good, need to reject.
if ($rejectStr != "") rejectForm($rejectStr);

if ($id == -1) { // this is an add new operation

  // Let's see if the title already exists
  $sql = "select title from quiz where title = '" . $title . "'";
  $result = $conn->query($sql);

  if ($result->num_rows != 0) { // already exists!
    $rejectStr = "The quiz title you have entered already exists in the Quizzler system. Please try a different one.<br />";
    rejectForm($rejectStr);
  }

  // If we made it this far the email address is unique and we can create the new account

  // Check connection first just in case
  if ($conn->connect_error) {
      ohNo();
  }

  // Insert new record into quiz table
  $sql = "INSERT INTO quiz (numofquestions, title, description, passperc, userid) ";
  $sql .= "VALUES (" . $numofquestions . ",'" . $title . "','" . $description . "'," . $passperc / 100 . "," . $_SESSION["UserID"] . ")";
  if ($conn->query($sql) === TRUE) {
      // Success, let's get the id of the just-added record so we can pass it on
      $a = $conn->insert_id;
  } else {
      ohNo(); // Bail!
  }

  // Insert new corresponding record into quizsubject table (subject association with this quiz)
  $sql = "INSERT INTO quizsubject (subjectid, quizid) ";
  $sql .= "VALUES (" . $subjectId . "," . $a . ")";
  if ($conn->query($sql) === TRUE) {
      // Success, continue processing
  } else {
      ohNo(); // Bail!
  }

} else { // We are updating an existing subject

  // Check connection first just in case
  if ($conn->connect_error) {
      ohNo();
  }

  $sql = "UPDATE quiz set numofquestions = " . $numofquestions . ", title = '" . $title . "', description = '" . $description . "', passperc = " . $passperc / 100 . ", userid = " . $_SESSION["UserID"];
  $sql .= " WHERE ID = " . $id;
  if ($conn->query($sql) === TRUE) {
      // Do nothing, continue displaying the success page below
  } else {
      ohNo(); // Bail!
  }

  // let's wipe out any existing subject association for this quiz so we can start fresh
  $sql = "DELETE FROM quizsubject WHERE quizid = " . $id;
  if ($conn->query($sql) === TRUE) {
      // Do nothing, continue displaying the success page below
  } else {
      ohNo(); // Bail!
  }

  // Insert new corresponding record into quizsubject table (subject association with this quiz)
  $sql = "INSERT INTO quizsubject (subjectid, quizid) ";
  $sql .= "VALUES (" . $subjectId . "," . $id . ")";
  if ($conn->query($sql) === TRUE) {
      // Success, continue processing
  } else {
      ohNo(); // Bail!
  }

}

$conn->close(); 

if ($id == -1) { // this is an add new operation
 header("Location: managequizzes.php?a=" . $a);
} else {
 header("Location: managequizzes.php?s=1");
}

?>

<script src="js/bootstrap.bundle.min.js"></script>

</body>
</html>
