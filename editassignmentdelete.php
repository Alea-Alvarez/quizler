<?php 

// Delete an assignment

session_start();

include("inc/dbOpenConn.php");
include("inc/functions.php");

// Below is to not report index error if a querystring or post variable is missing
error_reporting( error_reporting() & ~E_NOTICE );

if ($_SESSION["loggedIn"] != 1) authFail();
if ($_SESSION["permission"] != "ADMIN") authFail();

// Get assignment ID passed in querystring to delete
$id = $_GET["id"];
if (!is_numeric($id)) header("Location: manageusers.php"); // If assignment ID is not passed as a number then go back.

// Input seems good, let's verify assignment exists
$sql = "select * from assignment where id = " . $id;
$result = $conn->query($sql);

if ($result->num_rows == 0) { //Assignment does not exist
  $rejectStr = "Assignment not found. Please enter a valid assignment ID or perhaps this assignment was recently deleted?<br />";
  rejectForm($rejectStr);
}

// If we made it this it's okay to delete the assignment

// Check connection first just in case
if ($conn->connect_error) {
    ohNo();
}

$sql = "DELETE FROM ASSIGNMENT WHERE ID = " . $id;
if ($conn->query($sql) === TRUE) {
    // Do nothing, continue displaying the success page below
} else {
    ohNo(); // Bail!
}

$conn->close(); 

header("Location: manageassignments.php?d=1");

?>

<script src="js/bootstrap.bundle.min.js"></script>

</body>
</html>
