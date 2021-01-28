<?php
session_start();

include("inc/functions.php");
include("inc/standardHead.php");

if ($_SESSION["loggedIn"] != 1) authFail();

// Below is to not report index error if a querystring variable is missing, which is expected.
error_reporting( error_reporting() & ~E_NOTICE );

?>

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

<p class="mt-3"><b>Below are the questions you missed. Please review them and <A HREF="javascript:window.print()">print this page</a> if you would like a hard copy.</b></p>
<?php echo strtoupper($_SESSION["review"]) ?>

<button type="button" onclick="window.location.href='userdash.php'" class="btn btn-success">Back to Dashboard</button>
<br /><br /><br /><br />

</div>
<!-- /.container -->

<script src="js/bootstrap.bundle.min.js"></script>
</body></html>

