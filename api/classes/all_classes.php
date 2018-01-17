<?php
// File: all_classes.php

require_once '../../model/courses.php';

// get the session variables
session_start();
header('Content-type: application/json');

// return authenticated False if user isn't authenticated
if (!$_SESSION["username"])
{
  $return = array("authenticated" => False);
  echo json_encode($return);
  die();
}

$username = $_SESSION['username'];
$all_courses = get_avail_courses();

if ($all_courses == NULL)
{
  $return = array(
    "authenticated" => True,
    "error" => "Unable to Fetch All Courses"
  );
}else
{
  $return = array(
    "authenticated" => True,
    "all_courses" => $all_courses
  );
}

echo json_encode($return);
?>
