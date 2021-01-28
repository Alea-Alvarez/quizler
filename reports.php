<?php

// Reports Page

session_start();

include("inc/functions.php");
include("inc/dbOpenConn.php");
include("inc/standardHead.php");

if ($_SESSION["loggedIn"] != 1) authFail();
if ($_SESSION["permission"] != "ADMIN") authFail();

// Below is to not report index error if a querystring variable is missing, which is expected.
error_reporting( error_reporting() & ~E_NOTICE );

// Get user ID if one was passed for assignment history
$userId = $_POST["userId"];

?>

<body>

<!-- Navigation -->
<nav class="navbar navbar-expand-sm bg-success navbar-dark static-top">  
  <div class="container">
    <a class="navbar-brand" href="#">
          <img src="images/logo-small.png" alt="Quizler">
        </a><p class="mb-2 text-white">Quizler!<br /><b>Reports</b></p>
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

<h3 class="mt-3">Assignment History</h3>

<!-- User select dropdown -->
<form method="POST" action="reports.php">
<p class="mt-3">If you would like full assignment history for a specific user, make a selection below. Otherwise General Summary Reports are available underneath.<br /><br />
<b>Full Assignment History</b><br />
<select size="1" name="userId" onchange="this.form.submit()";>
<?php
$sql3 = "select * from user where permission='USER' order by lastname, firstname";
$result3 = $conn->query($sql3);
if ($result3->num_rows > 0) {
  echo "<option value='0'>Select a user</option>";
  while($row3 = $result3->fetch_assoc()) { // Loop through all the users
    echo "<option value='" . $row3["id"] . "'" ;
    if ($userId == $row3["id"]) echo " selected";
    echo ">" . $row3["lastname"] . ", " . $row3["firstname"];
    echo "</option> \n";
  }
}
?>
</select>
</p>
</form>

<?php

if (is_numeric($userId) && $userId > 0) { // a user was selected, show assignment history
  $sql = "select assignment.dateassigned, assignment.assignmenttext, assignment.lastdatetaken, assignment.resultperc, assignment.id, assignment.resulttext, quiz.title from assignment inner join quiz on assignment.quizid = quiz.id where assignment.userid = " . $userId . " order by dateassigned desc";
  $result = $conn->query($sql);

  if ($result->num_rows > 0) { // We have assignment history for this user
    echo "<table class=\"table table-striped\"><thead></thead><tbody>";
    while($row = $result->fetch_assoc()) { // Loop through all the assignments

      // Let's see if admin-assigned quiz and tag as such if yes otherwise self-assigned.
      $tempAssigned = $row["assignmenttext"]; 
      if ($tempAssigned == "Admin-Assigned Quiz") {
        $tempAssigned = "Admin";
      } else {
        $tempAssigned = "Self";
      }

      echo  "<tr><td><code>" . $row["title"] . ":</code><i>" . $tempAssigned . " Assigned " . $row["dateassigned"];
      if ($row["resulttext"] == "PASS") {
        echo ", <font color =\"green\">passed</font> on " . $row["lastdatetaken"] . " (" . $row["resultperc"]*100 . "%)<br />";
      } elseif ($row["resulttext"] == "FAIL") {
        echo ", <font color =\"red\">failed</font> on " . $row["lastdatetaken"] . " (" . $row["resultperc"]*100 . "%)<br />";
      } else {
        echo ", not taken yet<br />";
      }  
      echo "</td></tr>";
    } // end while
  } else { // no assignment history
      echo "<i>No assignment history exists for this user</i> \n";
  }
    echo "</tbody></table>";
}

?>

<hr>
<h3 class="mt-3">General Summary Reports</h3>
<br />

<?php

// General Summary Queries

// Most quizzes taken (most active users):
$sql = "select user.id, firstname, lastname, email, COUNT(user.id) as 'quizzes' from user inner join assignment on user.id = assignment.userid where user.permission = 'USER' and resulttext > '' group by user.id order by count(user.id) desc limit 10";
$result = $conn->query($sql);
if ($result->num_rows > 0) { // We have data!
  echo "<table class=\"table table-sm table-striped\"><thead><tr align=\"left\"><th scope=\"col\">Most Active Users</th></tr></thead><tbody>";
  while($row = $result->fetch_assoc()) { // Loop through all the results
      echo  "<tr class=\"table-success\"><td><code>" . $row["firstname"] . " " . $row["lastname"] . ":</code> <i>" . $row["quizzes"] . " quizzes taken ";
       echo "</td></tr>";
  } // end while
} else { // no data
  echo "<i>No data available</i> \n";
}
echo "</tbody></table>";

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

// Best performing users by average percentage score:
$sql = "select user.id, firstname, lastname, email, avg(assignment.resultperc) as 'average' from user inner join assignment on user.id = assignment.userid where user.permission = 'USER' and resultperc <> 0 group by user.id order by avg(assignment.resultperc) desc limit 10";
$result = $conn->query($sql);
if ($result->num_rows > 0) { // We have data!
  echo "<table class=\"table table-sm table-striped\"><thead><tr align=\"left\"><th scope=\"col\">Highest Scoring Users</th></tr></thead><tbody>";
  while($row = $result->fetch_assoc()) { // Loop through all the results
      echo  "<tr class=\"table-success\"><td><code>" . $row["firstname"] . " " . $row["lastname"] . ":</code> <i>" . $row["average"] * 100 . "% average score ";
       echo "</td></tr>";
  } // end while
} else { // no data
  echo "<i>No data available</i> \n";
}
echo "</tbody></table>";

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

// Most popular quizzes
$sql = "select quiz.title, quiz.id, count(quiz.id) as 'taken' from quiz inner join assignment on quiz.id = assignment.quizid where resulttext > '' group by quiz.id order by count(quiz.id) desc limit 10";
$result = $conn->query($sql);
if ($result->num_rows > 0) { // We have data!
  echo "<table class=\"table table-sm table-striped\"><thead><tr align=\"left\"><th scope=\"col\">Most Popular Quizzes</th></tr></thead><tbody>";
  while($row = $result->fetch_assoc()) { // Loop through all the results
      echo  "<tr class=\"table-success\"><td><code>" . $row["title"] . "</code> <i>(" . $row["taken"] . ")</i>";
       echo "</td></tr>";
  } // end while
} else { // no data
  echo "<i>No data available</i> \n";
}
echo "</tbody></table>";

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

// Users with no quiz history (i.e. no quizzes ever taken)
$sql = "select user.id, firstname, lastname, email from user where user.permission = 'USER' and id not in(select userid from assignment where resulttext <> '') order by lastname, firstname limit 10";
$result = $conn->query($sql);
if ($result->num_rows > 0) { // We have data!
  echo "<table class=\"table table-sm table-striped\"><thead><tr align=\"left\"><th scope=\"col\">Inactive Users (no quizzes taken)</th></tr></thead><tbody>";
  while($row = $result->fetch_assoc()) { // Loop through all the results
      echo  "<tr class=\"table-success\"><td><code>" . $row["firstname"] . " " . $row["lastname"] . "</code> <i>(" . $row["email"] . ")</i>";
       echo "</td></tr>";
  } // end while
} else { // no data
  echo "<i>No data available</i> \n";
}
echo "</tbody></table>";

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

// Admin-assigned quizzes not yet taken
$sql = "select user.id, firstname, lastname, email, quiz.title, assignment.dateassigned from user inner join assignment on user.id = assignment.userid inner join quiz on assignment.quizid = quiz.id where user.permission = 'USER' and assignment.assignmenttext = 'Admin-Assigned Quiz' and assignment.resulttext = '' group by user.id order by lastname, firstname limit 10";
$result = $conn->query($sql);
if ($result->num_rows > 0) { // We have data!
  echo "<table class=\"table table-sm table-striped\"><thead><tr align=\"left\"><th scope=\"col\">Admin-assigned Quizzes not yet Taken</th></tr></thead><tbody>";
  while($row = $result->fetch_assoc()) { // Loop through all the results
      echo  "<tr class=\"table-success\"><td><code>" . $row["firstname"] . " " . $row["lastname"] . "</code> <i>(" . $row["title"] . " - assigned " . $row["dateassigned"] . ")</i>";
       echo "</td></tr>";
  } // end while
} else { // no data
  echo "<i>No data available</i> \n";
}
echo "</tbody></table>";

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

//Admin-assigned quizzes recently taken and the score
$sql = "select user.id, firstname, lastname, email, quiz.title, assignment.dateassigned, assignment.resulttext, assignment.resultperc from user inner join assignment on user.id = assignment.userid inner join quiz on assignment.quizid = quiz.id where user.permission = 'USER' and assignment.assignmenttext = 'Admin-Assigned Quiz' and assignment.resulttext <> '' group by user.id order by lastname, firstname limit 10";
$result = $conn->query($sql);
if ($result->num_rows > 0) { // We have data!
  echo "<table class=\"table table-sm table-striped\"><thead><tr align=\"left\"><th scope=\"col\">Admin-assigned Quizzes Recently Taken</th></tr></thead><tbody>";
  while($row = $result->fetch_assoc()) { // Loop through all the results
      echo  "<tr class=\"table-success\"><td><code>" . $row["firstname"] . " " . $row["lastname"] . ":</code> <i>" . $row["title"] . " - assigned " . $row["dateassigned"] . "</i> (" . $row["resulttext"]  . " " . $row["resultperc"] * 100 . "%)";
       echo "</td></tr>";
  } // end while
} else { // no data
  echo "<i>No data available</i> \n";
}
echo "</tbody></table>";

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

?>

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
