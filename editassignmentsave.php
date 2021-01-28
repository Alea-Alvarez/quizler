<?php 

// Edit or add an assignment

session_start();
include("inc/dbOpenConn.php");
include("inc/functions.php");

// Below is to not report index error if a querystring or post variable is missing
error_reporting( error_reporting() & ~E_NOTICE );

if ($_SESSION["loggedIn"] != 1) authFail();
if ($_SESSION["permission"] != "ADMIN") authFail();

$userId = $_POST["userId"];
$quizId = $_POST["quizId"];

$id = $_POST["id"];
if (!is_numeric($id)) header("Location: manageassignments.php"); // If assignment ID is not passed as a number then go back.
$rejectStr = "";

if ($id == -1) { // this is an add new assignment operation

  // Let's see if the assignment already exists assigned today
  $sql = "select * from assignment where userid = " . $userId . " and quizid = " . $quizId . " and dateassigned = '" . date("Y-m-d") . "'";
  $result = $conn->query($sql);

  if ($result->num_rows != 0) { // Duplicate assignment for today already exists!
    $rejectStr = "The exact assignment you have entered already exists in the Quizzler system for today. Please delete the original assignment and recreate if you want the user to retake the quiz.<br />";
    rejectForm($rejectStr);
  }

  // If we made it this far we can create the new assignment

  // Check connection first just in case
  if ($conn->connect_error) {
      ohNo();
  }

  $sql = "INSERT INTO assignment (assignmenttext, recorddatecreated, userid, quizid, dateassigned) ";
  $sql .= "VALUES ('Admin-Assigned Quiz','" . date("Y-m-d") . "'," . $userId . "," . $quizId . ",'" . date("Y-m-d") . "')";

  if ($conn->query($sql) === TRUE) {
      // Do nothing, continue processing
  } else {
      ohNo(); // Bail!
  }

} else { // We are updating an existing assignment

  // Check connection first just in case
  if ($conn->connect_error) {
      ohNo();
  }

  $sql = "UPDATE assignment set userid = " . $userId . ", quizid = " . $quizId . ", resulttext = '', resultperc = 0, assignment.assignmenttext = 'Admin-Assigned Quiz'";
  $sql .= " WHERE ID = " . $id;

  if ($conn->query($sql) === TRUE) {
      // Do nothing, continue displaying the success page below
  } else {
      ohNo(); // Bail!
  }

}

$conn->close(); 

header("Location: manageassignments.php?s=1");

?>

<script src="js/bootstrap.bundle.min.js"></script>

</body>
</html>
