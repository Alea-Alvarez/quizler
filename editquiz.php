<?php

// Edit/Add Quiz Page

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
if (!is_numeric($id)) header("Location: managequizzes.php"); // If ID is not passed as a number then go home.
if ($id == -1) { $pageTitle = "Add Quiz"; } else { $pageTitle = "Edit Quiz"; }

if ($id != -1) { // Editing an existing quiz so let's load up some variables
  $sql = "select quizsubject.subjectid, id, numofquestions, title, description, passperc from quiz left join quizsubject on quiz.id = quizsubject.quizid where quiz.id = " . $id;
  $result = $conn->query($sql);
  if ($result->num_rows == 0) ohNo();
  $row = $result->fetch_assoc();
  $numofquestions = $row["numofquestions"];
  $title = $row["title"];
  $description = $row["description"];
  $passperc = $row["passperc"];
  $subjectId = $row["subjectid"];

  // We need to get a question count for this quiz to make sure we don't exceed the number of questions
  $sql2 = "select quizid from question where quizid = " . $id;
  $result2 = $conn->query($sql2);
  $questionCount = $result2->num_rows;
  if ($result2->num_rows == 0) $questionCount = 20; // If still zero due to new addition allow admin to specifiy up to 20 questions

} else { // Adding a new quiz, let's specify some defaults

  $numofquestions = 5;
  $passperc = .70;
  $questionCount = 20;

}

?>

<body>

<!-- Navigation -->
<nav class="navbar navbar-expand-sm bg-success navbar-dark static-top">  
  <div class="container">
    <a class="navbar-brand" href="#">
          <img src="images/logo-small.png" alt="Quizler">
        </a><p class="mb-2 text-white">Quizler!<br /><b>Edit Quiz</b></p>
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
<form method="POST" action="editquizsave.php">

<input type="hidden" id="id" name="id" value="<?php echo $id ?>">

<p class="mt-3"><b>Title</b><br>
<input type="text" required name="title" size="30" maxlength="100" value ="<?php echo $title ?>"></p>

<p class="mt-3"><b>Description</b><br>
<input type="text" required name="description" size="30" maxlength="500" value ="<?php echo $description ?>"></p>

<p class="mt-3"><b>Subject</b><br>
<select size="1" name="subjectId">
<?php
$sql3 = "select * from subject order by `desc`";
$result3 = $conn->query($sql3);
if ($result3->num_rows > 0) {
  while($row3 = $result3->fetch_assoc()) { // Loop through all the subjects
    echo "<option value='" . $row3["id"] . "'";
    if ($subjectId == $row3["id"]) echo " selected";
    echo ">" . $row3["desc"];
    echo "</option>";
  }
}
?>
</select></p>

<p class="mt-3"><b>Number of Questions</b><br />
<select size="1" name="numofquestions">
<?php
for ($x = 1; $x <= $questionCount; $x++) {
  echo "<option value='" . $x . "'";
  if ($x == $numofquestions) echo " selected";
  echo ">" . $x . "</option>";

} 
?>
</select>

<p class="mt-3"><b>Pass Percentage</b><br />
<select size="1" name="passperc">
<?php
for ($x = 1; $x <= 100; $x++) {
  echo "<option value='" . $x . "'";
  if ($x == ($passperc * 100)) echo " selected";
  echo ">" . $x . "%</option>";

} 

$conn->close(); 

?>
</select>

</p>

<input type="submit" class="btn btn-success" value="&nbsp;Save&nbsp;" name="S1"">
&nbsp;&nbsp;<button type="button" onclick="window.location.href='managequizzes.php'" class="btn btn-warning">Cancel</a></button>
</p>	
</form>

<?php include("inc/footer.php"); ?>

</div>
<!-- /.container -->

<script src="js/bootstrap.bundle.min.js"></script>

</body>
</html>
