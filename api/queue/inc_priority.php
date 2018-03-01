<?php
// File: inc_priority.php

require_once '../../model/auth.php';
require_once '../../model/courses.php';
require_once '../../model/queue.php';
require_once '../errors.php';

// get the session variables
session_start();
header('Content-type: application/json');

if (!$_SESSION['username'])
{
  $return = array("authenticated" => False);
  echo json_encode($return);
  die();
}

if (!$_POST['course'])
{
  $return = array(
    "authenticated" => True,
    "error" => "No course specified"
  );
  echo json_encode($return);
  die();
}

if (!$_POST['student'])
{
  $return = array(
    "authenticated" => True,
    "error" => "No student specified"
  );
  echo json_encode($return);
  die();
}

$username   = $_SESSION['username'];
$course     = $_POST['course'];
$student    = $_POST['student'];
$ta_courses = $_SESSION["ta_courses"];

if (!in_array($course, $ta_courses))
{
  $return = array(
    "authenticated" => True,
    "error" => "TA not assigned to course"
  );
  echo json_encode($return);
  die();
}

$res = increase_stud_priority($student, $course);
if($res < 0)
{
  $return = array(
    "authenticated" => True,
    "error" => "Unable to change student priority"
  );
}else
{
  $return = array(
    "authenticated" => True,
    "success" => "Student priority increased"
  );
}
echo json_encode($return);
?>
