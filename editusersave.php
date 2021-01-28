<?php 

// Edit or add a user

session_start();
include("inc/dbOpenConn.php");
include("inc/functions.php");

// Below is to not report index error if a querystring or post variable is missing
error_reporting( error_reporting() & ~E_NOTICE );

if ($_SESSION["loggedIn"] != 1) authFail();
if ($_SESSION["permission"] != "ADMIN") authFail();

$firstName = $_POST["firstName"];
$firstName = trim($firstName);
$lastName = $_POST["lastName"];
$lastName = trim($lastName);
$email = $_POST["email"];
$email = trim($email);
$password = $_POST["password"];
$userType = $_POST["userType"];
$id = $_POST["id"];
if (!is_numeric($id)) header("Location: manageusers.php"); // If user ID is not passed as a number then go back.
$rejectStr = "";

// Check user input
if ($firstName == "") $rejectStr .= "First name is missing or invalid.<br />";
if ($lastName == "") $rejectStr .= "Last name is missing or invalid.<br />";
if ($email == "") $rejectStr .= "Email address is missing or invalid.<br />";
if ($password == "") $rejectStr .= "Password is missing or invalid.<br />";

// if $rejectStr is not empty then user input is no good, need to reject.
if ($rejectStr != "") rejectForm($rejectStr);

if ($id == -1) { // this is an add new user operation

  // Let's see if the email address already exists
  $sql = "select * from user where email = '" . $email . "'";
  $result = $conn->query($sql);

  if ($result->num_rows != 0) { //Email address already exists!
    $rejectStr = "The email address you have entered already exists in the Quizzler system. Please try a different email.<br />";
    rejectForm($rejectStr);
  }

  // If we made it this far the email address is unique and we can create the new account

  // Check connection first just in case
  if ($conn->connect_error) {
      ohNo();
  }

  $sql = "INSERT INTO user (firstname, lastname, email, password, permission)";
  $sql .= "VALUES ('" . $firstName . "','" . $lastName . "','" . $email . "','" . $password . "','" . $userType . "')";

  if ($conn->query($sql) === TRUE) {
      // Do nothing, continue displaying the success page below
  } else {
      ohNo(); // Bail!
  }

} else { // We are updating an existing user

  // Let's see if the email address already exists assigned to another user
  $sql = "select * from user where email = '" . $email . "' and id <> " . $id;
  $result = $conn->query($sql);

  if ($result->num_rows != 0) { //Email address already exists for another user!
    $rejectStr = "The email address you have entered already exists for another user in the Quizzler system. Please try a different email.<br />";
    rejectForm($rejectStr);
  }

  // If we made it this far the email address is unique and we can edit the user account

  // Check connection first just in case
  if ($conn->connect_error) {
      ohNo();
  }

  $sql = "UPDATE user set firstname = '" . $firstName . "', lastname = '" . $lastName . "', email = '" . $email . "', password = '" . $password . "', permission = '" . $userType . "' ";
  $sql .= "WHERE ID = " . $id;

  if ($conn->query($sql) === TRUE) {
      // Do nothing, continue displaying the success page below
  } else {
      ohNo(); // Bail!
  }

}

$conn->close(); 

header("Location: manageusers.php?s=1");

?>

<script src="js/bootstrap.bundle.min.js"></script>

</body>
</html>
