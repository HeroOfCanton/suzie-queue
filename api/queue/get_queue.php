<?php
// File: get_queue.php

require_once '../../model/auth.php';
require_once '../../model/courses.php';
require_once '../../model/queue.php';
require_once '../errors.php';

// get the session variables
session_start();
header('Content-type: application/json');

if (!isset($_SESSION['username']))
{
  $return = array("authenticated" => False);
  echo json_encode($return);
  die();
}

if (!isset($_POST['course']))
{
  echo json_encode( missing_course() );
  die();
}

$username   = $_SESSION['username'];
$course     = $_POST['course'];
$ta_courses = $_SESSION["ta_courses"];

//For now, these return the same information.
//Later, we may want the TAs to see more,
//or the students to see less.
if (in_array($course, $ta_courses)) //TA
{
  $return = get_queue($course);
}
elseif (in_array($course, get_stud_courses($username))) //Student
{
  $return = get_queue($course);
}else //Not in course
{
  $return = array(
    "authenticated" => True,
    "error" => "Not enrolled in course"
  );
}

if($return == -1) //SQL error
{
  $return = array(
    "authenticated" => True,
    "error" => "Unable to fetch queue state"
  );
}elseif($return == -2) //Nonexistant Course
{
  $return = array(
    "authenticated" => True,
    "error" => "Invalid Course"
  );
}

$return["authenticated"] = True;
echo json_encode($return);
?>
