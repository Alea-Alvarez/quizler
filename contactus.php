<?php
session_start();

include("inc/standardHead.php");

?>

<body>

<!-- Navigation -->
<nav class="navbar navbar-expand-sm bg-success navbar-dark static-top">  
  <div class="container">
    <a class="navbar-brand" href="#">
          <img src="images/logo-small.png" alt="Quizler">
        </a><p class="mb-2 text-white">Quizler!<br /><b>Contact Us</b></p>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
    <div class="collapse navbar-collapse" id="navbarResponsive">
      <ul class="navbar-nav ml-auto">
        <li class="nav-item active">
          <a class="nav-link" href="javascript: history.go(-1)">Previous Page<span class="sr-only">(current)</span></a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<!-- Page Content -->
<div class="container">

  <h3 class="mt-3">Contact us</h3>
  <form method="GET" action="index.php">
  <table border="0" cellspacing="0" bordercolor="#000080" id="table1" cellpadding="15">
 
      <tr>
        <td width="225" bgcolor="#FFFFCC" align="left">
                <b><font face="Verdana" size="2">First Name</b><br/><input type="text" name="firstName" size="28" maxlength="100" style="border: 1 solid #000000" required></font><br />
		<b><font face="Verdana" size="2">Last Name</b><br/><input type="text" maxlength="100" name="lastName" size="28" style="border: 1 solid #000000" required></font><br />
                <b><font face="Verdana" size="2">Email Address</b><br/><input type="email" name="email" size="28" maxlength="100" style="border: 1 solid #000000" required></font><br />
		<b><font face="Verdana" size="2">Your Message</b><br/><textarea rows="4" cols="28" required></textarea></font><br />
        <td>
		<font face="Verdana" size="2"><input type="submit" class="btn btn-success btn-small" value="Submit" name="B1"></font></td>
      </tr>
    </table>
 <input type="hidden" id="em" name="em" value="1">
</form>
<div align="left">

<?php include("inc/footer.php"); ?>

</div>

</div>
<!-- /.container -->

<script src="js/bootstrap.bundle.min.js"></script>

</body>
</html>
