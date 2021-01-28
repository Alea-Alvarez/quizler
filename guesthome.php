<?php

// Quiz selection page for guests

session_start();

include("inc/functions.php");
include("inc/dbOpenConn.php");
include("inc/standardHead.php");

$accordianCounter = 0;
$subjectId = 0;

?>

<body>

<!-- Navigation -->
<nav class="navbar navbar-expand-sm bg-success navbar-dark static-top">  
  <div class="container">
    <a class="navbar-brand" href="#">
          <img src="images/logo-small.png" alt="Quizler">
        </a><p class="mb-2 text-white">Quizler!<br /><b>Guest</b></p>
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

  <h3 class="mt-3">Welcome Guest!</h3>
	<p class="mt-3">Please select a subject from the list below to expand the available quizzes:</p>

<!-- Accordian to display the subjects and quizzes starts here -->
<div class="accordion" id="theAccordion">

<?php

$sql = "select id,`desc` from subject order by `desc`";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {  // Loop through subjects - title of each accordian cel
    $accordianCounter++;
    $subjectId = $row["id"];
?>
  <div class="card">
    <div class="card-header" id="heading<?php echo $accordianCounter; ?>">
      <h5 class="mb-0">
        <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapse<?php echo $accordianCounter; ?>" aria-expanded="true" aria-controls="collapse<?php echo $accordianCounter; ?>">
          <?php echo $row["desc"]; ?>
        </button>
      </h5>
    </div>
    <div id="collapse<?php echo $accordianCounter; ?>" class="collapse" aria-labelledby="heading<?php echo $accordianCounter; ?>" data-parent="#theAccordion">
      <div class="card-body">
 		<?php
		$sql2 = "select quizid, title, numofquestions, description from quizsubject inner join quiz on quizid = id where subjectid = " . $subjectId;
		$result2 = $conn->query($sql2);
		if ($result2->num_rows > 0) {
                  echo "<ul>";
   		  while($row2 = $result2->fetch_assoc()) { // Loop through the quizzes in each subject
 		    echo "<li><a href='guestquiz.php?q=" . $row2["quizid"] . "'><b>" . $row2["title"] . "</b></a><br /><i>";
                    echo $row2["description"] . " This quiz has ";
                    echo $row2["numofquestions"] . " questions</i></li><br />";
		  }
		} else {
    		echo "<i><ul><li>No quizzes in this category</li></ul></i>";
		}
                echo "</ul>";
		?>
      </div>
    </div>
  </div>
<?php
    }
} else {
    echo "0 results";
}

?>
</div>
<!-- End of accordian code -->

<p>&nbsp;</p>
<?php include("inc/footer.php"); ?>

</div>
<!-- /.container -->

<script src="js/bootstrap.bundle.min.js"></script>

</body>
</html>
