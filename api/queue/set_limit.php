<?php
// File: set_limit.php

require_once '../../model/auth.php';
require_once '../../model/courses.php';
require_once '../../model/queue.php';
require_once '../errors.php';

// get the session variables
session_start();
header('Content-Type: application/json');

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

if (!isset($_POST['time_lim']))
{
  $return = array(
    "authenticated" => True,
    "error" => "No time_lim specified"
  );
  echo json_encode($return);
  die();
}

$username   = $_SESSION['username'];
$course     = $_POST['course'];
$time_lim   = $_POST['time_lim'];
$ta_courses = $_SESSION["ta_courses"];

if (!in_array($course, $ta_courses))
{
  echo json_encode( not_authorized() );
  die();
}

$res = set_time_lim($time_lim, $course);
if ($res)
{
  $return = array(
    "authenticated" => True,
    "error" => "Unable to set time limit"
  );
}else
{
  $return = array(
    "authenticated" => True,
    "success" => "Time limit set"
  );
}
echo json_encode($return);
?>
