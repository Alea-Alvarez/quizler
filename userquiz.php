<?php
session_start();

include("inc/functions.php");
include("inc/dbOpenConn.php");
include("inc/standardHead.php");
include("inc/quizCore.php");

if ($_SESSION["loggedIn"] != 1) authFail();

// Below is to not report index error if a querystring variable is missing, which is expected.
error_reporting( error_reporting() & ~E_NOTICE );

// Get quiz ID
$quizId = $_GET["q"];
if (!is_numeric($quizId)) header("Location: userdash.php"); // If quiz ID is not passed as a number then go home.

// Get assignment ID (if any)
$aID = $_GET["id"];
// Save assignment ID to session var
if ($aID > 0) $_SESSION["AssignmentID"] = $aID;

$sql = "select * from quiz where id = " . $quizId;
$result = $conn->query($sql);

if ($result->num_rows == 1) { // load quiz parameters into session vars
  $row = $result->fetch_assoc();
  $_SESSION["resultsPage"] = "userresults.php";
  $_SESSION["reviewPage"] = "userreview.php";
  $_SESSION["quizId"] = $quizId;
  $_SESSION["numofquestions"] = $row["numofquestions"];
  $_SESSION["title"] = $row["title"];
  $_SESSION["description"] = $row["description"];
  $_SESSION["passperc"] = $row["passperc"];
}
else // invalid quiz ID, probably manually entered by user as hack attempt.
{
  // clean the output buffer
  ob_clean();
  echo "<br /><b>Quiz ID not found.</b><br />Please do not attempt to manually enter a test ID in the querystring!!";
  exit();
  die();
}

// Session data loaded, we can continue displaying the page now.

?>

<body>

<!-- Navigation -->
<nav class="navbar navbar-expand-sm bg-success navbar-dark static-top">  
  <div class="container">
    <a class="navbar-brand" href="#">
          <img src="images/logo-small.png" alt="Quizler">
        </a><p class="mb-2 text-white">Quizler!<br /><b>User Quiz</b></p>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
    <div class="collapse navbar-collapse" id="navbarResponsive">
      <ul class="navbar-nav ml-auto">
        <li class="nav-item active">
          <a class="nav-link" href="userdash.php">User Dashboard<span class="sr-only">(current)</span></a>
        </li>
        <li class="nav-item active">
          <a class="nav-link" href="logout.php">Log Out<span class="sr-only">(current)</span></a>
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

<?php quizCore(); ?>
	
<?php include("inc/footer.php"); ?>

</div>
<!-- /.container -->

<script src="js/bootstrap.bundle.min.js"></script>

</body>
</html>
