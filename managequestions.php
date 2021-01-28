<?php

// Manage Questions Page

session_start();

include("inc/functions.php");
include("inc/dbOpenConn.php");
include("inc/standardHead.php");

if ($_SESSION["loggedIn"] != 1) authFail();
if ($_SESSION["permission"] != "ADMIN") authFail();

// Below is to not report index error if a querystring variable is missing, which is expected.
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
   } 
}

// Get quiz ID (either from querystring or POST)
$quizId = $_GET["id"]; // Check if there is something in querystring
if ($quizId ==0) $quizId = $_POST["quizId"]; // If nothing in querystring check form submission

?>

<body>

<!-- Navigation -->
<nav class="navbar navbar-expand-sm bg-success navbar-dark static-top">  
  <div class="container">
    <a class="navbar-brand" href="#">
          <img src="images/logo-small.png" alt="Quizler">
        </a><p class="mb-2 text-white">Quizler!<br /><b>Manage Questions</b></p>
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

<h3 class="mt-3">Manage Questions</h3>

<!-- Quiz select dropdown -->
<form method="POST" action="managequestions.php">
<b>Quiz Selected</b><br>
<select size="1" name="quizId" onchange="this.form.submit()";>
<?php
$sql3 = "select * from quiz order by title";
$result3 = $conn->query($sql3);
if ($result3->num_rows > 0) {
  while($row3 = $result3->fetch_assoc()) { // Loop through all the quizzes
    if ($firstId == 0) $firstId = $row3["id"]; // Get the ID of the first quiz in alpha order to display in case a QuizID was not passed to the page.
    echo "<option value='" . $row3["id"] . "'" ;
    if ($quizId == $row3["id"]) echo " selected";
    echo ">" . $row3["title"];
    echo "</option> \n";
  }
}
?>
</select>
</form>

<!-- Small modal delete-->
<div id="myModalDelete" class="modal fade bd-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><b>Question deleted!</b></h5>
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
        <h5 class="modal-title"><b>Question saved!</b></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Accordian to display the questions starts here -->
<div class="accordion" id="theAccordion">

<?php

if ($quizId == 0) $quizId = $firstId; // If no quiz selected arbitrarily go with the first quiz in alpha order.

$sql = "select * from question where quizid = " . $quizId . " order by id";
$result = $conn->query($sql);

while($row = $result->fetch_assoc()) {  // Loop through questions - title of each accordian cel
    $accordianCounter++;
    $questionId = $row["id"];
?>
  <div class="card">
    <div class="card-header" id="heading<?php echo $accordianCounter; ?>">
      <h6 class="mb-0">
        <a class="text-primary" data-toggle="collapse" data-target="#collapse<?php echo $accordianCounter; ?>" aria-expanded="true" aria-controls="collapse<?php echo $accordianCounter; ?>">
          <?php echo $row["questiontext"]; ?>
        </a>
      </h6>
    </div>
    <div id="collapse<?php echo $accordianCounter; ?>" class="collapse" aria-labelledby="heading<?php echo $accordianCounter; ?>" data-parent="#theAccordion">
      <div class="card-body">
 	   <form method="POST" action="editquestionsave.php">
             <input type="hidden" id="quizid" name="quizid" value="<?php echo $quizId ?>">
             <input type="hidden" id="questionid" name="questionid" value="<?php echo $questionId ?>">
                <?php
		$sql2 = "select * from question where quizid = " . $quizId . " and id = " . $questionId;
		$result2 = $conn->query($sql2);
		if ($result2->num_rows == 1) {
                  $row2 = $result2->fetch_assoc();
                  $answerkey = $row2["answerkey"]; // Need this for the select html element below
                  ?>
                  <b>Question<font color="red">*</font><br />
                  </b><input type="text" name="questiontext" required size="30" value ="<?php echo $row2["questiontext"] ?>">

                  <p class="mt-3"><b>Option 1<font color="red">*</font><br />
                  </b><input type="text" name="option1" required size="30" value ="<?php echo $row2["option1"] ?>"></p>

                  <p class="mt-3"><b>Option 2<font color="red">*</font><br />
                  </b><input type="text" name="option2" required size="30" value ="<?php echo $row2["option2"] ?>"></p>

                  <p class="mt-3"><b>Option 3<br />
                  </b><input type="text" name="option3" size="30" value ="<?php echo $row2["option3"] ?>"></p>

                  <p class="mt-3"><b>Option 4<br />
                  </b><input type="text" name="option4" size="30" value ="<?php echo $row2["option4"] ?>"></p>

                  <p class="mt-3"><b>Option 5<br />
                  </b><input type="text" name="option5" size="30" value ="<?php echo $row2["option5"] ?>"></p>

                  <p class="mt-3"><b>Response text if incorrect<br />
                  </b><input type="text" name="missedcomment" size="30" value ="<?php echo $row2["missedcomment"] ?>"></p>

                  <b>Correct Answer<font color="red">*</font><br /></b>
                  <select size="1" name="answerkey">
                    <option value="option1" <?php if ($answerkey == "option1") echo "selected"; ?>>Option 1</option>
                    <option value="option2" <?php if ($answerkey == "option2") echo "selected"; ?>>Option 2</option>
                    <option value="option3" <?php if ($answerkey == "option3") echo "selected"; ?>>Option 3</option>
                    <option value="option4" <?php if ($answerkey == "option4") echo "selected"; ?>>Option 4</option>
                    <option value="option5" <?php if ($answerkey == "option5") echo "selected"; ?>>Option 5</option>
                  </select>

                  <br /><br /><input type="submit" class="btn btn-success" value="&nbsp;Save&nbsp;&nbsp;" name="S1">&nbsp;&nbsp;
                  <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#message<?php echo $questionId;?>">Delete</button></form>

                  <!-- Modal HTML -->
                  <div id="message<?php echo $questionId;?>" class="modal fade">
	                <div class="modal-dialog modal-confirm">
		                <div class="modal-content">
			                <div class="modal-header">			
				                <h4 class="modal-title">Delete Question?</h4>	
                                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			                </div>
			                <div class="modal-body">
				                <p>Are you sure you want to delete this question? This action cannot be undone.</p>
			                </div>
		                    <div class="modal-footer">
                                    <a href="#" class="btn btn-info" data-dismiss="modal">Cancel</a>
                                    <a class="btn btn-danger" href="editquestiondelete.php?id=<?php echo $questionId;?>&qid=<?php echo $quizId;?>">Delete</a>
		                  </div>
		                </div>
	                </div>
                   </div> 

                  <?php

		} else {
    		  echo "<i>Error: Problem retrieving question</i>";
		}
		?>
      </div>
    </div>
   </div>
 <?php
 } // end of while/loop looping through all the existing questions for this quiz in the database
  
// Add question option if less than 25 questions in pool
if ($accordianCounter < 25) {
  ?>
  <div class="card">
    <div class="card-header" id="heading<?php echo $x; ?>">
      <h6 class="mb-0">
        <a class="text-primary" data-toggle="collapse" data-target="#collapse<?php echo $x; ?>" aria-expanded="true" aria-controls="collapse<?php echo $x; ?>">
          <?php echo "<i>*** Add Question ***</i>"; ?>
        </a>
      </h6>
    </div>
    <div id="collapse<?php echo $x; ?>" class="collapse" aria-labelledby="heading<?php echo $x; ?>" data-parent="#theAccordion">
     <div class="card-body">
 	   <form method="POST" action="editquestionsave.php">
             <input type="hidden" id="quizid" name="quizid" value="<?php echo $quizId ?>">
             <input type="hidden" id="questionid" name="questionid" value="-1">

                  <b>Question<font color="red">*</font><br />
                  </b><input type="text" name="questiontext" required size="30">

                  <p class="mt-3"><b>Option 1<font color="red">*</font><br />
                  </b><input type="text" name="option1" required size="30"></p>

                  <p class="mt-3"><b>Option 2<font color="red">*</font><br />
                  </b><input type="text" name="option2" required size="30"></p>

                  <p class="mt-3"><b>Option 3<br />
                  </b><input type="text" name="option3" size="30"></p>

                  <p class="mt-3"><b>Option 4<br />
                  </b><input type="text" name="option4" size="30"></p>

                  <p class="mt-3"><b>Option 5<br />
                  </b><input type="text" name="option5" size="30"></p>

                  <p class="mt-3"><b>Response text if incorrect<br />
                  </b><input type="text" name="missedcomment" size="30"></p>

                  <b>Correct Answer<font color="red">*</font><br /></b>
                  <select size="1" name="answerkey">
                    <option value="option1" selected>Option 1</option>
                    <option value="option2">Option 2</option>
                    <option value="option3">Option 3</option>
                    <option value="option4">Option 4</option>
                    <option value="option5">Option 5</option>
                  </select>
                  <?php
                  echo "<br /><br /><input type=\"submit\" class=\"btn btn-success\" value=\"&nbsp;Save&nbsp;\" name=\"S1\"></form>";
   		  ?>
      </div>

    </div>
   </div>
  <?php
} // end if statement if accordiancounter < 25

?>
</div>
<!-- End of accordian code -->

</p>	

<?php 
$conn->close(); 
include("inc/footer.php");
?>

</div>
<!-- /.container -->

<script src="js/bootstrap.bundle.min.js"></script>

</body>
</html>
