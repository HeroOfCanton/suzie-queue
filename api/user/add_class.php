<?php
// File: add_class.php

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

$acc_code = NULL;
if (isset($_POST['acc_code']))
{
  $acc_code = $_POST['acc_code'];
}

$username = $_SESSION['username'];
$course   = $_POST['course'];

$ret = add_stud_course($username, $course, $acc_code);
if ($ret == 0)
{
  $return = array(
    "authenticated" => True,
    "success" => "Student Course Added Successfully"
  );
}
elseif ($ret == 1)
{
  $return = array(
    "authenticated" => True,
    "error" => "Unable to Add Student Course"
  );
}
elseif ($ret == 2)
{
  $return = array(
    "authenticated" => True,
    "error" => "Already registered as TA for course"
  );
}
elseif ($ret == 3)
{
  $return = array(
    "authenticated" => True,
    "error" => "Invalid Access Code"
  );
}


echo json_encode($return);
?>
