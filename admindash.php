<?php
session_start();

// Admin Dashboard Page

include("inc/functions.php");
include("inc/standardHead.php");

if ($_SESSION["loggedIn"] != 1) authFail();
if ($_SESSION["permission"] != "ADMIN") authFail();

?>

<body>

<!-- Navigation -->
<nav class="navbar navbar-expand-sm bg-success navbar-dark static-top">  
  <div class="container">
    <a class="navbar-brand" href="#">
          <img src="images/logo-small.png" alt="Quizler">
        </a><p class="mb-2 text-white">Quizler!<br /><b>Admin Dashboard</b></p>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
    <div class="collapse navbar-collapse" id="navbarResponsive">
      <ul class="navbar-nav ml-auto">
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

  <h3 class="mt-3">Admin menu</h3>

  <p class="mt-3">Please select the area you would like to manage below.</p>

  <div align="left">
  <table cellpadding = "7" width="95%">
  <tr><td><button type="button" onclick="window.location.href='manageusers.php'" class="btn btn-success btn-block">Users</a></button></td></tr>
  <tr><td><button type="button" onclick="window.location.href='manageassignments.php'" class="btn btn-success btn-block">Assignments</a></button></td></tr>
  <tr><td><button type="button" onclick="window.location.href='ManageSubjects.php'" class="btn btn-success btn-block">Subjects</a></button></td></tr>
  <tr><td><button type="button" onclick="window.location.href='managequizzes.php'" class="btn btn-success btn-block">Quizzes</a></button></td></tr>
  <tr><td><button type="button" onclick="window.location.href='managequestions.php'" class="btn btn-success btn-block">Questions</a></button></td></tr>
  <tr><td><button type="button" onclick="window.location.href='reports.php'" class="btn btn-success btn-block">Reports</a></button></td></tr>
  </table>

    <?php include("inc/footer.php"); ?>
  </div>


</div>
<!-- /.container -->

<script src="js/bootstrap.bundle.min.js"></script>

</body>
</html>
