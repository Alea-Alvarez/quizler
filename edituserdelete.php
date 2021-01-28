<?php 

// Delete a user

session_start();

include("inc/dbOpenConn.php");
include("inc/functions.php");

// Below is to not report index error if a querystring or post variable is missing
error_reporting( error_reporting() & ~E_NOTICE );

if ($_SESSION["loggedIn"] != 1) authFail();
if ($_SESSION["permission"] != "ADMIN") authFail();

// Get user ID passed in querystring to delete
$id = $_GET["id"];
if (!is_numeric($id)) header("Location: manageusers.php"); // If user ID is not passed as a number then go back.

// Make sure user is not trying to delete themselves!
if ($_SESSION["UserID"] == $id) $rejectStr = "You cannot delete yourself!<br />";
if ($rejectStr != "") rejectForm($rejectStr);

// Input seems good, let's verify user exists
$sql = "select * from user where id = " . $id;
$result = $conn->query($sql);

if ($result->num_rows == 0) { //User does not exist
  $rejectStr = "User not found. Please enter a valid user ID or perhaps this user was recently deleted?<br />";
  rejectForm($rejectStr);
}

// If we made it this it's okay to delete the user

// Check connection first just in case
if ($conn->connect_error) {
    ohNo();
}

$sql = "DELETE FROM USER WHERE ID = " . $id;
if ($conn->query($sql) === TRUE) {
    // Do nothing, continue displaying the success page below
} else {
    ohNo(); // Bail!
}

$conn->close(); 

header("Location: manageusers.php?d=1");

?>

<script src="js/bootstrap.bundle.min.js"></script>

</body>
</html>
