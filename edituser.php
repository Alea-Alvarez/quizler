<?php

// Edit/Add User Page

session_start();

include("inc/functions.php");
include("inc/dbOpenConn.php");
include("inc/standardHead.php");

if ($_SESSION["loggedIn"] != 1) authFail();
if ($_SESSION["permission"] != "ADMIN") authFail();

// Below is to not report index error if a querystring variable is missing, which is expected.
error_reporting( error_reporting() & ~E_NOTICE );

// Get user ID
$id = $_GET["id"];
if (!is_numeric($id)) header("Location: manageusers.php"); // If ID is not passed as a number then go home.
if ($id == -1) { $pageTitle = "Add User"; } else { $pageTitle = "Edit User"; }

if ($id != -1) { // Editing an existing user so let's load up some variables
  $sql = "select * from user where id = " . $id;
  $result = $conn->query($sql);
  if ($result->num_rows == 0) ohNo();
  $row = $result->fetch_assoc();
  $firstName = $row["firstname"];
  $lastName = $row["lastname"];
  $email = $row["email"];
  $password = $row["password"];
  $userType = $row["permission"];
}

$conn->close(); 

?>

<body>

<!-- Navigation -->
<nav class="navbar navbar-expand-sm bg-success navbar-dark static-top">  
  <div class="container">
    <a class="navbar-brand" href="#">
          <img src="images/logo-small.png" alt="Quizler">
        </a><p class="mb-2 text-white">Quizler!<br /><b>Edit User</b></p>
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
<form method="POST" action="editusersave.php">

<input type="hidden" id="id" name="id" value="<?php echo $id ?>">

<p class="mt-3"><b>First Name<br>
</b><input type="text" name="firstName" required size="30" maxlength="100" value ="<?php echo $firstName ?>"></p>

<p class="mt-3"><b>Last Name<br>
</b><input type="text" name="lastName" required size="30" maxlength="100" value ="<?php echo $lastName ?>"></p>

<p class="mt-3"><b>Email<br>
</b><input type="email" name="email" required size="30" maxlength="100" value ="<?php echo $email ?>"></p>

<p class="mt-3"><b>Password<br>
</b><input type="text" name="password" required size="30" maxlength="100" value ="<?php echo $password ?>"></p>

<b>Type</b><br>
<select size="1" name="userType">
  <option value="USER" <?php if ($userType == "USER") echo "selected"; ?>>User</option>
  <option value="ADMIN" <?php if ($userType == "ADMIN") echo "selected"; ?>>Admin</option>
</select><br /><br /><input type="submit" class="btn btn-success" value="&nbsp;Save&nbsp;" name="S1"">
&nbsp;&nbsp;<button type="button" onclick="window.location.href='manageusers.php'" class="btn btn-warning">Cancel</a></button>
</p>	
</form>

<?php include("inc/footer.php"); ?>

</div>
<!-- /.container -->

<script src="js/bootstrap.bundle.min.js"></script>

</body>
</html>
