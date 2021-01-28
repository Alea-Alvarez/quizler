<?php

// Edit/Add Subject Page

session_start();

include("inc/functions.php");
include("inc/dbOpenConn.php");
include("inc/standardHead.php");

if ($_SESSION["loggedIn"] != 1) authFail();
if ($_SESSION["permission"] != "ADMIN") authFail();

// Below is to not report index error if a querystring variable is missing, which is expected.
error_reporting( error_reporting() & ~E_NOTICE );

// Get ID
$id = $_GET["id"];
if (!is_numeric($id)) header("Location: managesubjects.php"); // If ID is not passed as a number then go home.
if ($id == -1) { $pageTitle = "Add Subject"; } else { $pageTitle = "Edit Subject"; }

if ($id != -1) { // Editing an existing subject so let's load up some variables
  $sql = "select id, `desc` from subject where id = " . $id;
  $result = $conn->query($sql);
  if ($result->num_rows == 0) ohNo();
  $row = $result->fetch_assoc();
  $desc = $row["desc"];
}

$conn->close(); 

?>

<body>

<!-- Navigation -->
<nav class="navbar navbar-expand-sm bg-success navbar-dark static-top">  
  <div class="container">
    <a class="navbar-brand" href="#">
          <img src="images/logo-small.png" alt="Quizler">
        </a><p class="mb-2 text-white">Quizler!<br /><b>Edit Subject</b></p>
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
<form method="POST" action="editsubjectsave.php">

<input type="hidden" id="id" name="id" value="<?php echo $id ?>">

<p class="mt-3"><b>Subject Description<br>
</b><input type="text" required name="desc" size="30" value ="<?php echo $desc ?>"></p>

<input type="submit" class="btn btn-success" value="&nbsp;Save&nbsp;" name="S1"">
&nbsp;&nbsp;<button type="button" onclick="window.location.href='managesubjects.php'" class="btn btn-warning">Cancel</a></button>
</p>	
</form>

<?php include("inc/footer.php"); ?>

</div>
<!-- /.container -->

<script src="js/bootstrap.bundle.min.js"></script>

</body>
</html>
