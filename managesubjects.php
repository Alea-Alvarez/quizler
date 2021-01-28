<?php

// Manage Subjects Page

session_start();

include("inc/functions.php");
include("inc/dbOpenConn.php");
include("inc/standardHead.php");

if ($_SESSION["loggedIn"] != 1) authFail();
if ($_SESSION["permission"] != "ADMIN") authFail();

// Below is to not report index error if a querystring variable is missing
error_reporting( error_reporting() & ~E_NOTICE );

if ($_GET["d"] != 1 && $_GET["s"] != 1) {  // Normal page visit, just show body tag
  echo "<body>";
} else { // Record just deleted or saved
   if ($_GET["d"] == 1) {
     echo "<script type=\"text/javascript\">";
     echo " $(window).on('load',function(){";
     echo " $('#myModalDelete').modal('show');";
     echo "setTimeout(function() {";
     echo "$('#myModalDelete').modal('hide');";
     echo "}, 2000);";
     echo "  });";
     echo " </script>";
   } else {
     echo "<script type=\"text/javascript\">";
     echo " $(window).on('load',function(){";
     echo " $('#myModalSave').modal('show');";
     echo "setTimeout(function() {";
     echo "$('#myModalSave').modal('hide');";
     echo "}, 2000);";
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
        </a><p class="mb-2 text-white">Quizler!<br /><b>Manage Subjects</b></p>
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

<h3 class="mt-3">Manage Subjects&nbsp;&nbsp;&nbsp;<button type="button" onclick="window.location.href='editsubject.php?id=-1'" class="btn btn-success btn-sm">&nbsp;&nbsp;Add&nbsp;&nbsp;</a></button></h3>

<table class="table table-striped">
  <thead></thead>
  <tbody>

<?php

$sql = "select id, `desc` from subject order by `desc`";
$result = $conn->query($sql);

while($row = $result->fetch_assoc()) {  // Loop through subjects

  // We need to get the number of quizzes associated with this subject to display to user
  $sql2 = "select subjectid, quizid from quizsubject where subjectid = " . $row['id'];
  $result2 = $conn->query($sql2);
  $assoc = $result2->num_rows;

  echo "<tr><td><b>" . $row["desc"] . "</b><br />";

  if ($assoc > 0 ) {
    echo "<i>Quiz associations: " . $assoc;
  } else { // zero quiz associations
    echo "<i><font color =\"green\">Quiz associations: " . $assoc . "</font>";
  }

  echo "</i></td><td align='right'>";
  echo "<button type=\"button\" class=\"btn btn-success btn-sm\" onclick=\"window.location.href='editsubject.php?id=" . $row["id"] . "'\">&nbsp;&nbsp;Edit&nbsp;&nbsp;</a></button> \n";
  ?>
  <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#message<?php echo $row['id'];?>">Delete</button>
  </td></tr>

  <!-- Modal HTML -->
  <div id="message<?php echo $row['id'];?>" class="modal fade">
	<div class="modal-dialog modal-confirm">
		<div class="modal-content">
			<div class="modal-header">			
				<h4 class="modal-title">Delete Subject?</h4>	
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			</div>
			<div class="modal-body">
				<p>Are you sure you want to delete this subject? <b><font color="red"><u>This will delete any quizzes</u></font></b> and related data associated with this subject! This action cannot be undone.</p>
			</div>
		    <div class="modal-footer">
                    <a href="#" class="btn btn-info" data-dismiss="modal">Cancel</a>
                    <a class="btn btn-danger" href="editsubjectdelete.php?id=<?php echo $row['id'];?>">Delete</a>

		  </div>
		</div>
	</div>
  </div> 

  <?php } ?>

  </tbody>
</table>

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