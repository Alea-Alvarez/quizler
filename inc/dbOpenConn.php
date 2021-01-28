<?php

// Create connection
// server, user, password, database
$conn = new mysqli("localhost", "root", "", "quizsystem");

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
  exit();
}

?> 