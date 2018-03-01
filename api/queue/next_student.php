<?php
// File: next_student.php

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
  echo json_encode( missing_course() );
  die();
}

$username   = $_SESSION['username'];
$course     = $_POST['course'];
$ta_courses = $_SESSION["ta_courses"];

if (!in_array($course, $ta_courses))
{
  echo json_encode( not_authorized() );
  die();
}

$res = help_next_student($username, $course);
if($res)
{
  $return = array(
    "authenticated" => True,
    "error" => "Unable to change TA status"
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
