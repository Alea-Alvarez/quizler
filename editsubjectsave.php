<?php 

// Edit or add a subject

session_start();
include("inc/dbOpenConn.php");
include("inc/functions.php");

// Below is to not report index error if a querystring or post variable is missing
error_reporting( error_reporting() & ~E_NOTICE );

if ($_SESSION["loggedIn"] != 1) authFail();
if ($_SESSION["permission"] != "ADMIN") authFail();

$desc = $_POST["desc"];
$id = $_POST["id"];
if (!is_numeric($id)) header("Location: managesubjects.php"); // If ID is not passed as a number then go back.
$rejectStr = "";

// Check user input
if ($desc == "") $rejectStr .= "Subject description is missing or invalid.<br />";

// if $rejectStr is not empty then user input is no good, need to reject.
if ($rejectStr != "") rejectForm($rejectStr);

if ($id == -1) { // this is an add new operation

  // Let's see if the description already exists
  $sql = "select * from subject where `desc` = '" . $desc . "'";
  $result = $conn->query($sql);

  if ($result->num_rows != 0) { // already exists!
    $rejectStr = "The subject description you have entered already exists in the Quizzler system. Please try a different one.<br />";
    rejectForm($rejectStr);
  }

  // If we made it this far the email address is unique and we can create the new account

  // Check connection first just in case
  if ($conn->connect_error) {
      ohNo();
  }

  $sql = "INSERT INTO subject (`desc`)";
  $sql .= "VALUES ('" . $desc. "')";

  if ($conn->query($sql) === TRUE) {
      // Do nothing, continue displaying the success page below
  } else {
      ohNo(); // Bail!
  }

} else { // We are updating an existing subject

  // Check connection first just in case
  if ($conn->connect_error) {
      ohNo();
  }

  $sql = "UPDATE subject set `desc` = '" . $desc . "' ";
  $sql .= "WHERE ID = " . $id;

  if ($conn->query($sql) === TRUE) {
      // Do nothing, continue displaying the success page below
  } else {
      ohNo(); // Bail!
  }

}

$conn->close(); 

header("Location: managesubjects.php?s=1");

?>

<script src="js/bootstrap.bundle.min.js"></script>

</body>
</html>
