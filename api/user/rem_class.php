<?php
// File: rem_class.php

require_once '../../model/courses.php';

// get the session variables
session_start(); 
header('Content-Type: application/json');

if (!isset($_SESSION['username']))
{
  $return = array("authenticated" => False);
  echo json_encode($return);
  die();
}

if (!isset($_POST['course']))
{
  $return = array(
    "authenticated" => True,
    "error" => "No course specified"
  );
  echo json_encode($return);
  die();
}

$username = $_SESSION['username'];
$course   = $_POST['course'];

if (!rem_stud_course($username, $course))
{
  $return = array(
    "authenticated" => True,
    "success" => "Student Course Removed Successfully"
  );
}
else
{
  $return = array(
    "authenticated" => True,
    "error" => "Unable to Remove Student Course"
  );
}

echo json_encode($return);
?>
