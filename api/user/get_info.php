<?php
// File: get_info.php

require_once '../../model/auth.php';

// get the session variables
session_start();

// return authenticated False if user isn't authenticated
if (!$_SESSION["username"])
{
  $return = array("authenticated" => False);
  $return = json_encode($return);
  echo $return;
  die();
}

$username = $_SESSION['username'];
$stud_info = get_info($username);

if ($stud_info == NULL)
{
  $return = array(
    "authenticated" => True,
    "error" => "Unable to Fetch Student Info"
  );
  $return = json_encode($return);
  echo $return;
  die();
}

$return = array(
  "authenticated" => True,
  "student_info" => $stud_info
);

$return = json_encode($return);
echo $return;

?>

