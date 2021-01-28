<?php 

// Delete a quiz

session_start();

include("inc/dbOpenConn.php");
include("inc/functions.php");

// Below is to not report index error if a querystring or post variable is missing
error_reporting( error_reporting() & ~E_NOTICE );

if ($_SESSION["loggedIn"] != 1) authFail();
if ($_SESSION["permission"] != "ADMIN") authFail();

// Get quiz ID passed in querystring to delete
$id = $_GET["id"];
if (!is_numeric($id)) header("Location: managequizzes.php"); // If ID is not passed as a number then go back.

// Input seems good, let's verify ID exists
$sql = "select * from quiz where id = " . $id;
$result = $conn->query($sql);

if ($result->num_rows == 0) { // does not exist
  $rejectStr = "Quiz not found. Please enter a valid quiz ID or perhaps this quiz was recently deleted?<br />";
  rejectForm($rejectStr);
}

// If we made it this it's okay to delete the user

// Check connection first just in case
if ($conn->connect_error) {
    ohNo();
}

$sql = "DELETE FROM QUIZ WHERE ID = " . $id;
if ($conn->query($sql) === TRUE) {
    // Do nothing, continue displaying the success page below
} else {
    ohNo(); // Bail!
}

$conn->close(); 

header("Location: managequizzes.php?d=1");

?>

<script src="js/bootstrap.bundle.min.js"></script>

</body>
</html>
