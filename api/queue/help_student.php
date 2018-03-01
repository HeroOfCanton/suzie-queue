<?php
// File: help_student.php

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
  echo json_encode( not_authorized() );
  die();
}

$res = help_student($username, $student, $course);
if($res == 1)
{
  $return = array(
    "authenticated" => True,
    "error" => "Unable to change TA status"
  );
}elseif($res == 2)
{
  $return = array(
    "authenticated" => True,
    "error" => "Nonexistant Course"
  );
}else
{
  $return = array(
    "authenticated" => True,
    "success" => "TA status changed"
  );
}

echo json_encode($return);
?>
