<?php

// Manage Quizzes Page

session_start();

include("inc/functions.php");
include("inc/dbOpenConn.php");
include("inc/standardHead.php");

if ($_SESSION["loggedIn"] != 1) authFail();
if ($_SESSION["permission"] != "ADMIN") authFail();

// Below is to not report index error if a querystring variable is missing
error_reporting( error_reporting() & ~E_NOTICE );

if ($_GET["d"] != 1 && $_GET["s"] != 1 && $_GET["a"] == 0) {  // Normal page visit, just show body tag
  // do nothing
} else { // Record just deleted or saved
   if ($_GET["d"] == 1) { // Deleted
     echo "<script type=\"text/javascript\">";
     echo " $(window).on('load',function(){";
     echo " $('#myModalDelete').modal('show');";
     echo "setTimeout(function() {";
     echo "$('#myModalDelete').modal('hide');";
     echo "}, 2000);";
     echo "  });";
     echo " </script>";
   } elseif ($_GET["s"] == 1) {  // Saved
     echo "<script type=\"text/javascript\">";
     echo " $(window).on('load',function(){";
     echo " $('#myModalSave').modal('show');";
     echo "setTimeout(function() {";
     echo "$('#myModalSave').modal('hide');";
     echo "}, 2000);";
     echo "  });";
     echo " </script>";
   } else { // Added new record
     $a = $_GET["a"]; // get the ID of the newly-created record that was just passed to this page
     echo "<script type=\"text/javascript\">";
     echo " $(window).on('load',function(){";
     echo " $('#myModalAdd').modal('show');";
     echo "  });";
     echo " </script>";
   }
}
?>

<body>

<!-- Navigation -->
<nav class="navbar navbar-expand-sm bg-success navbar-dark static-top">  
  <div class="container">
    <a class="navbar-brand" href="#">
          <img src="images/logo-small.png" alt="Quizler">
        </a><p class="mb-2 text-white">Quizler!<br /><b>Manage Quizzes</b></p>
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

<h3 class="mt-3">Manage Quizzes&nbsp;&nbsp;&nbsp;<button type="button" onclick="window.location.href='editquiz.php?id=-1'" class="btn btn-success btn-sm">&nbsp;&nbsp;Add&nbsp;&nbsp;</a></button></h3>

<table class="table table-striped">
  <thead></thead>
  <tbody>

<?php

$sql = "select id, numofquestions, title, description, passperc from quiz order by title";
$result = $conn->query($sql);

while($row = $result->fetch_assoc()) {  // Fetch quizzes

  // We need to get a question count for this quiz to make sure it has enough questions in the pool (i.e. in the question table)
  $sql2 = "select quizid from question where quizid = " . $row['id'];
  $result2 = $conn->query($sql2);
  $questionCount = $result2->num_rows;

  echo "<tr><td><b>" . $row["title"] . "</b><br />";
  if ($questionCount != 0) {
    echo "<i><a href=\"managequestions.php?id=" . $row["id"] . "\">Questions in pool:  <span class=\"badge badge-success badge-pill\">" . $questionCount . "</span></a><br />";
  } else {
    echo "<i><a href=\"managequestions.php?id=" . $row["id"] . "\">Questions in pool:  <span class=\"badge badge-danger badge-pill\">" . $questionCount . "</span></a><br />";

  }

  // Need to figure out quiz status
  $quizStatus = "";
  if ($questionCount < $row['numofquestions']) $quizStatus .= "<a href=\"managequestions.php?id=" . $row["id"] . "\">Add questions</a> or <a href=\"editquiz.php?id=" . $row["id"] . "\">edit quiz settings</a> ";
  if ($row['passperc'] <= 0) $quizStatus .= "Pass percentage needs to be greater than zero!";

  if ($quizStatus == "") { // Quiz Status Okay!
    // echo "<font color = \"green\">Quiz status: OK";
  } else {  // Something is not right with this quiz
    echo "<font color = \"red\"><b>Not ready</b></font> - " . $quizStatus . "</font>";
  }

  echo "</i></td><td align='right'>";
  echo "<button type=\"button\" class=\"btn btn-success btn-sm\" onclick=\"window.location.href='editquiz.php?id=" . $row["id"] . "'\">&nbsp;&nbsp;Edit&nbsp;&nbsp;</a></button> \n";
  ?>
  <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#message<?php echo $row['id'];?>">Delete</button>
  </td></tr>

  <!-- Modal HTML -->
  <div id="message<?php echo $row['id'];?>" class="modal fade">
	<div class="modal-dialog modal-confirm">
		<div class="modal-content">
			<div class="modal-header">			
				<h4 class="modal-title">Delete Quiz?</h4>	
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			</div>
			<div class="modal-body">
				<p>Are you sure you want to delete this quiz? <b><font color="red"><u>This will delete any questions</u></font></b> and related data associated with this quiz! This action cannot be undone.</p>
			</div>
		    <div class="modal-footer">
                    <a href="#" class="btn btn-info" data-dismiss="modal">Cancel</a>
                    <a class="btn btn-danger" href="editquizdelete.php?id=<?php echo $row['id'];?>">Delete</a>

		  </div>
		</div>
	</div>
  </div>   

  <?php } ?>

  </tbody>
</table>

<!-- Modal HTML for a record that was just added -->
<div id="myModalAdd" class="modal fade">
  <div class="modal-dialog modal-confirm">
    <div class="modal-content">
		<div class="modal-header">			
			<h4 class="modal-title">New Quiz Added</h4>	
                               <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		</div>
		<div class="modal-body">
			<p>This quiz will not function until questions are added, would you like to do that now?</p>
		</div>
	    <div class="modal-footer">
                   <a href="managequizzes.php" class="btn btn-info" data-dismiss="modal">Not Now</a>
                   <a class="btn btn-success" href="managequestions.php?id=<?php echo $a;?>">Yes, Add Questions</a>
		  </div>
	</div>
  </div>
</div>  

<!-- Small modal delete-->
<div id="myModalDelete" class="modal fade bd-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><b>Record deleted!</b></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Small modal save-->
<div id="myModalSave" class="modal fade bd-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><b>Record saved!</b></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
    </div>
  </div>
</div>

<?php include("inc/footer.php"); ?>

</div>
<!-- /.container -->

<script src="js/bootstrap.bundle.min.js"></script>

</body>
</html>