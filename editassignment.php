<?php

// Edit/Add Assignment Page

session_start();

include("inc/functions.php");
include("inc/dbOpenConn.php");
include("inc/standardHead.php");

if ($_SESSION["loggedIn"] != 1) authFail();
if ($_SESSION["permission"] != "ADMIN") authFail();

// Below is to not report index error if a querystring variable is missing, which is expected.
error_reporting( error_reporting() & ~E_NOTICE );

// Get assignment ID
$id = $_GET["id"];
if (!is_numeric($id)) header("Location: manageassignments.php"); // If ID is not passed as a number then go home.
if ($id == -1) { $pageTitle = "Add Assignment"; } else { $pageTitle = "Edit Assignment"; }

if ($id != -1) { // Editing an existing assignment so let's load up some variables
  $sql = "select * from assignment where id = " . $id;
  $result = $conn->query($sql);
  if ($result->num_rows == 0) ohNo();
  $row = $result->fetch_assoc();
  $userId = $row["userid"];
  $quizId = $row["quizid"];
}

?>

<body>

<!-- Navigation -->
<nav class="navbar navbar-expand-sm bg-success navbar-dark static-top">  
  <div class="container">
    <a class="navbar-brand" href="#">
          <img src="images/logo-small.png" alt="Quizler">
        </a><p class="mb-2 text-white">Quizler!<br /><b>Edit Assignment</b></p>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
    <div class="collapse navbar-collapse" id="navbarResponsive">
      <ul class="navbar-nav ml-auto">
        <li class="nav-item active">
          <a class="nav-link" href="admindash.php">Admin Dashboard<span class="sr-only">(current)</span></a>
        </li>
        <li class="nav-item active">
          <a class="nav-link" href="javascript: history.go(-1)">Previous Page<span class="sr-only">(current)</span></a>
        </li>
        <li class="nav-item active">
          <a class="nav-link" href="logout.php">Log Out<span class="sr-only">(current)</span></a>
        </li>

      </ul>
    </div>
  </div>
</nav>

<!-- Page Content -->
<div class="container">

<h3 class="mt-3"><?php echo $pageTitle ?></h3>
<form method="POST" action="editassignmentsave.php">

<input type="hidden" id="id" name="id" value="<?php echo $id ?>">

<b>User Name</b><br>
<select size="1" name="userId">
<?php
$sql2 = "select * from user where permission = 'USER' order by lastname, firstname";
$result2 = $conn->query($sql2);
if ($result2->num_rows > 0) {
  while($row2 = $result2->fetch_assoc()) { // Loop through all the non-admin users
    echo "<option value='" . $row2["id"] . "'";
    if ($userId == $row2["id"]) echo " selected";
    echo ">" . $row2["lastname"] . ", " . $row2["firstname"];
    echo "</option>";
  }
}
?>

</select><br /><br /><b>Quiz Title</b><br>
<select size="1" name="quizId">
<?php
$sql3 = "select * from quiz order by title";
$result3 = $conn->query($sql3);
if ($result3->num_rows > 0) {
  while($row3 = $result3->fetch_assoc()) { // Loop through all the quizzes
    echo "<option value='" . $row3["id"] . "'";
    if ($quizId == $row3["id"]) echo " selected";
    echo ">" . $row3["title"];
    echo "</option>";
  }
}

$conn->close(); 
?>

</select>

<br /><br /><input type="submit" class="btn btn-success" value="&nbsp;Save&nbsp;" name="S1"">
&nbsp;&nbsp;<button type="button" onclick="window.location.href='manageassignments.php'" class="btn btn-warning">Cancel</a></button>
</p>	
</form>

<?php include("inc/footer.php"); ?>

</div>
<!-- /.container -->

<script src="js/bootstrap.bundle.min.js"></script>

</body>
</html>
