<?php

session_start();
header("Expires: " . gmdate("D, d M Y H:i:s", time() + (-1*60)) . " GMT");

// Below is to not report index error if a querystring variable is missing
error_reporting( error_reporting() & ~E_NOTICE );

include("inc/standardHead.php");

if ($_GET["lo"] != 1 && $_GET["em"] != 1)
{
// Normal page visit, just show body tag
?>
  <body>
 <?php }
  else
{
// user just logged off, show log off messagebox  ?>
  <body onload='showpopup()'>
  <center>
  <div id="popup_box">
    <input type="button" id="cancel_button" value="x">
  
    <?php if ($_GET["lo"] == 1) {
      echo "<p id=\"info_text\">You have been logged out.";
    } else {
      echo "<p id=\"info_text\">Your message was sent.";
    }
?>


  </div>
</center>
 <?php } ?>

<!-- Navigation -->
<nav class="navbar navbar-expand-sm bg-success navbar-dark static-top">  
  <div class="container">
    <a class="navbar-brand" href="#">
          <img src="images/logo-small.png" alt="Quizler">
        </a><p class="mb-2 text-white">Quizler!<br /><b>Home</b></p>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
    <div class="collapse navbar-collapse" id="navbarResponsive">
      <ul class="navbar-nav ml-auto">
        <li class="nav-item active">
          <a class="nav-link" href="login.php">Log in<span class="sr-only">(current)</span></a>
        </li>
        <li class="nav-item active">
          <a class="nav-link" href="guesthome.php">Guest<span class="sr-only">(current)</span></a>
        </li>
        <li class="nav-item active">
          <a class="nav-link" href="newaccount.php">Register<span class="sr-only">(current)</span></a>
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

  <table width="90%">
    <tr>
      <td><h1 class="mt-3">Quiz time!</h1></td>
      <td align="right"><button type="button" onclick="window.location.href='login.php'" class="btn btn-success">&nbsp;Log In&nbsp;</a></button></td>
    </tr>
  <table>

  <p>Welcome to Quizler. Quizler is an easy way to take online quizzes to test 
  your knowledge, challenge yourself or just have fun! Please begin by logging in. If you do not have an 
  account click on the &quot;Register Account&quot; button below - It's free!</p>

  <div align="left">

<button type="button" onclick="window.location.href='guesthome.php'" class="btn btn-warning">&nbsp;Guest&nbsp;&nbsp;</a></button>&nbsp;&nbsp;&nbsp;&nbsp;
<button type="button" onclick="window.location.href='NewAccount.php'" class="btn btn-warning">Register</a></button>

<br /><br />
<?php include("inc/footer.php"); ?>

</div>
</form>

</div>
<!-- /.container -->

<script src="js/bootstrap.bundle.min.js"></script>

</body>
</html>

