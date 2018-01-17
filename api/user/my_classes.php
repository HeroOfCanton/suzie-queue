<?php
//File: my_classes.php 

require_once '../../model/courses.php';

// get the session variables
session_start(); 
header('Content-Type: application/json');

if (!$_SESSION['username'])
{
  $return = array("authenticated" => False);
  echo json_encode($return);
  die();
}

$username = $_SESSION['username'];
$stud_courses = get_stud_courses($username);
$ta_courses   = get_ta_courses($username);

if (stud_courses == NULL || ta_courses == NULL)
{
  $return = array(
    "authenticated" => True,
    "error" => "Unable to Fetch Classes"
  );
}else
{
  $return = array(
    "authenticated" => True,
    "student_courses" => $stud_courses,
    "ta_courses"      => $ta_courses
  );
}

echo json_encode($return);
?>
