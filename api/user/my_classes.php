<?php
require_once '../../model/courses.php';
// get the session variables
session_start(); 

// return authentication False if user isn't authenticated
if (!$_SESSION["username"])
{
  $return = array("authenticated" => False);
  $return = json_encode($return);
  echo $return;
  die();	
}

$username = $_SESSION['username'];

$stud_courses = get_stud_courses($username);
$ta_courses   = get_ta_courses($username);

if (stud_courses == NULL || ta_courses == NULL)
{
  $return = array("error" => "Unable to Fetch Classes");
  $return = json_encode($return);
  echo $return;
  die();
}

$return = array(
  "student_courses" => $stud_courses,
  "ta_courses"      => $ta_coursesa,
);

$return = json_encode($return);
echo $return;

?>
