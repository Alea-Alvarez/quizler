<?php 

session_start();
include("inc/dbOpenConn.php");
include("inc/functions.php");

// Below is to not report index error if a querystring or post variable is missing
error_reporting( error_reporting() & ~E_NOTICE );

// In case somehow a user is visiting this page while logged in let's just log them out
if ($_SESSION["loggedIn"] == 1) header("Location: logout.php");

// Load up the form variables
$firstName = $_POST["firstName"];
$firstName = trim(htmlspecialchars($firstName)); // Prevent XSS 
mysqli_real_escape_string($conn, $firstName); // Prevent SQL Injection
$lastName = $_POST["lastName"];
$lastName = trim(htmlspecialchars($lastName)); // Prevent XSS 
mysqli_real_escape_string($conn, $lastName); // Prevent SQL Injection
$email = $_POST["email"];
$email = trim(htmlspecialchars($email)); // Prevent XSS 
mysqli_real_escape_string($conn, $email); // Prevent SQL Injection
$password = $_POST["password"];
$password = htmlspecialchars($password); // Prevent XSS 
mysqli_real_escape_string($conn, $password); // Prevent SQL Injection
$passwordVerify = $_POST["passwordVerify"];
$passwordVerify = htmlspecialchars($passwordVerify); // Prevent XSS 
mysqli_real_escape_string($conn, $passwordVerify); // Prevent SQL Injection

$rejectStr = "";

// Check user input
if ($firstName == "") $rejectStr .= "First name is missing or invalid.<br />";
if ($lastName == "") $rejectStr .= "Last name is missing or invalid.<br />";
if ($email == "") $rejectStr .= "Email address is missing or invalid.<br />";
if ($password == "" || $passwordVerify == "") $rejectStr .= "Both password and verify password must be entered.<br />";
if ($password != $passwordVerify) $rejectStr .= "Password entries do not match.<br />";
if (strlen($password) < 6) $rejectStr .= "Password must be at least six characters.<br />";

// if $rejectStr is not empty then user input is no good, need to reject.
if ($rejectStr != "") rejectForm($rejectStr);

// Input seems good, let's see if the email address already exists
$sql = "select * from user where email = '" . $email . "'";
$result = $conn->query($sql);

if ($result->num_rows != 0) { //Email address already exists!
  $rejectStr = "The email address you have entered already exists in the Quizzler system. Please provide a different email.<br />";
  rejectForm($rejectStr);
}

// If we made it this far the email address is unique and we can create the new account

// Check connection first just in case
if ($conn->connect_error) {
    ohNo();
}

$sql = "INSERT INTO user (firstname, lastname, email, password, permission)";
$sql .= "VALUES ('" . $firstName . "','" . $lastName . "','" . $email . "','" . $password . "','USER')";

if ($conn->query($sql) === TRUE) {
    // Do nothing, continue displaying the success page below
} else {
    ohNo(); // Bail!
}

$conn->close(); 

include("inc/standardHead.php");

?>

<body>

<!-- Navigation -->
<nav class="navbar navbar-expand-sm bg-success navbar-dark static-top">  
  <div class="container">
    <a class="navbar-brand" href="#">
          <img src="images/logo-small.png" alt="Quizler">
        </a><p class="mb-2 text-white">Quizler!<br /><b>New Account</b></p>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
    <div class="collapse navbar-collapse" id="navbarResponsive">
      <ul class="navbar-nav ml-auto">
        <li class="nav-item active">
          <a class="nav-link" href="index.php">Home<span class="sr-only">(current)</span></a>
        </li>
        <li class="nav-item active">
          <a class="nav-link" href="login.php">Log in<span class="sr-only">(current)</span></a>
        </li>
        <li class="nav-item active">
          <a class="nav-link" href="contactus.php">Contact Us<span class="sr-only">(current)</span></a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<!-- Page Content -->
<div class="container">

  <h3 class="mt-3 text-success">Success!</h3>
  <p>Your user account has been created. You may now <a href="login.php">log in to your new account</a>!</p>

  <div align="left">
 
<br />

<?php include("inc/footer.php"); ?>

</div>


</div>
<!-- /.container -->

<script src="js/bootstrap.bundle.min.js"></script>

</body>
</html>
