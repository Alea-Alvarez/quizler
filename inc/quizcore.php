<?php

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
function quizCore() {

?>

<form method="POST" action="<?php echo $_SESSION["resultsPage"] ?>" name="login">
<br />
<h3><?php echo strtoupper($_SESSION["title"]) ?> QUIZ:</h3><?php echo $_SESSION["description"] ?>
<?php echo " This quiz has " . $_SESSION["numofquestions"]. " questions." ?>
<hr>

<?php

// First we need to get the total number of questions in the pool available for this quiz
include("inc/dbOpenConn.php");
$sql = "select id from question where quizid = " . $_SESSION["quizId"] . " ORDER BY id DESC";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$poolSize = $row["id"];

if ($poolSize == 0) { // This quiz has no questions - BAIL!!
    $rejectStr = "SYSTEM CONFIGURATION ERROR - This quiz has no questions! <a href='contactus.php'>Please let us know.</a><br />";
    rejectForm($rejectStr);
  exit();
  die();
}

if ($_SESSION["numofquestions"] > $poolSize) { // More questions requested in test settings than exist in the pool - BAIL!!
  // clean the output buffer
  ob_clean();
    $rejectStr = "SYSTEM CONFIGURATION ERROR - This quiz is set to administer more questions than exist in the question pool. <a href='contactus.php'>Please let us know.</a><br />";
  exit();
  die();
}

// Now that we know the size of the pool we have to randomly select questions from the pool.

// Just prime the var below because each question number must be enclosed by hyphens ( i.e. -#- )
$VerifyNoDupes = "-";

// Select random question numbers from the pool and also check for duplicate questions
for ($Looper = 1; $Looper <= $_SESSION["numofquestions"]; $Looper++) {
  $TempWork = mt_rand(1,$poolSize); // Generate a random question number
  for ($a = 1; $a <= ($Looper - 1); $a++) {
    while (strpos($VerifyNoDupes,"-" . $TempWork . "-") !== false) {
      $TempWork = mt_rand(1,$poolSize);
    }
  }
  $ArrQuestions[$Looper] = $TempWork;
  $VerifyNoDupes .= $ArrQuestions[$Looper] . "-";
}

//Create Session var to hold the order of the questions
$TempOrder="";
for ($Looper = 1; $Looper <= $_SESSION["numofquestions"]; $Looper++)
{
  $TempOrder .= $ArrQuestions[$Looper] . ",";
}

$TempOrder = substr($TempOrder,0,strlen($TempOrder) - 1); // Removes the trailing comma from the operation above

// Store session var
$_SESSION['QOrder'] = $TempOrder;

for ($Looper = 1; $Looper <= $_SESSION["numofquestions"]; $Looper++) {

  $sql2 = "select * from question where quizid = " . $_SESSION["quizId"] . " and ID = " . $ArrQuestions[$Looper];

  $result2 = $conn->query($sql2);
  if ($result2->num_rows == 0) ohNo();

  while($row2 = $result2->fetch_assoc()) {  // Loop through questions

      $QCounter++;
      echo "<B>" . $QCounter . ") ";
      echo  $row2["questiontext"] . "</b>";

      if (trim($row2["option0"])!="") { // There always needs to be "No Response" choice, we keep it hidden.
        echo "<div class='invisible'>";
        echo "<input type='radio' checked value='option0' name='" . $ArrQuestions[$Looper] . "'>" . $row2["option0"] . "</div><table border = '0' cellspacing='5' cellpadding='5'>" . "\r\n";
      }

      if (trim($row2["option1"])!="") { 
        echo "<tr><td>";
        echo "<input type='radio' value='option1' name='" . $ArrQuestions[$Looper] . "'></td><td>" . $row2["option1"] . "</td></tr>" . "\r\n";
      } 

      if (trim($row2["option2"])!="") {
        echo "<tr><td>";
        echo "<input type='radio' value='option2' name='" . $ArrQuestions[$Looper] . "'></td><td>" . $row2["option2"] . "</td></tr>" . "\r\n";
      } 

      if (trim($row2["option3"])!="") {
        echo "<tr><td>";
        echo "<input type='radio' value='option3' name='" . $ArrQuestions[$Looper] . "'></td><td>" . $row2["option3"] . "</td></tr>" . "\r\n";
      } 

      if (trim($row2["option4"])!="") {
        echo "<tr><td>";
        echo "<input type='radio' value='option4' name='" . $ArrQuestions[$Looper] . "'></td><td>" . $row2["option4"] . "</td></tr>" . "\r\n";
      } 
  
      if (trim($row2["option5"])!="") {
        echo "<tr><td>";
        echo "<input type='radio' value='option5' name='" . $ArrQuestions[$Looper] . "'></td><td>" . $row2["option5"] . "</td></tr>" . "\r\n";
      } 

      echo "</table><br />" . "\r\n\r\n";
     
    }

}

?>

<input type="submit" value="Submit Answers" name="Submit" class="btn btn-success"><br />
  <br />
</form>

<?php

}

?>