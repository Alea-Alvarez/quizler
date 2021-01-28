<?php 

// Validates the user login and directs user to the
// correct dashboard (ADMIN or USER)

session_start();
include("inc/dbOpenConn.php");
include("inc/functions.php");

// Below is to not report index error if a querystring variable is missing, which is expected.
error_reporting( error_reporting() & ~E_NOTICE );

// If we are already logged in let's just direct to the correct dashboard now
if ($_SESSION["loggedIn"] == 1) {
  if ($_SESSION["permission"] == "ADMIN") {
    header("Location: admindash.php");
  }
  elseif ($_SESSION["permission"] == "USER") 
  {
    header("Location: userdash.php");
  }
}

// If we made it this far user is not logged in, let's try to validate the user
$email = $_POST["email"];
$email = trim(htmlspecialchars($email)); // Prevent XSS 
mysqli_real_escape_string($conn, $email); // Prevent SQL Injection

$password = $_POST["password"];
$password = htmlspecialchars($password); // Prevent XSS 
mysqli_real_escape_string($conn, $password); // Prevent SQL Injection

if ($email == "" || $password == "") authFail();

$sql = "select * from user where email = '" . $email . "' and password = '" . $password . "'";
$result = $conn->query($sql);

if ($result->num_rows == 0) Authfail();
if ($result->num_rows > 1) ohNo();

// If we made it this far we have authenticated the login!!

// Load user session variables
$row = $result->fetch_assoc();
$_SESSION["loggedIn"] = 1;
$_SESSION["firstName"] = $row["firstname"];
$_SESSION["lastName"] = $row["lastname"];
$_SESSION["email"] = $row["email"];
$_SESSION["permission"] = $row["permission"];
$_SESSION["UserID"] = $row["id"];

$conn->close(); 

// Now to direct the user to ADMIN or USER dashboard
if ($_SESSION["permission"] == "ADMIN") {
  header("Location: admindash.php");
}
elseif ($_SESSION["permission"] == "USER") 
{
  header("Location: userdash.php");
}
else
{
  Authfail(); // Just in case something went wrong and execution winds up here.
}

?>
