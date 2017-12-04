<?php
// File: enqueue_ta.php

require_once '../../model/auth.php';
require_once '../../model/course.php';
require_once '../../model/queue.php';

// get the session variables
session_start();

// return authenticated False if user isn't authenticated
if (!$_SESSION["username"])
{
  $return = array("authenticated" => False);
  $return = json_encode($return);
  echo $return;
  die();
}

if (!$_POST["course"])
{
  $return = array("error" => "Missing course");
  $return = json_encode($return);
  echo $return;
  die();
}

$username = $_SESSION['username'];
$course   = $_POST['course'];

// make sure TA is assigned to the course
if (!in_array($username, get_tas($course)))
{
  $return = array("error" => "TA not assigned to course");
  $return = json_encode($return);
  echo $return;
  die();
}

if(help_next_student($username, $course))
{
  $return = array("error" => "Unable to change TA status");
  $return = json_encode($return);
  echo $return;
  die();
}

$return = array("success" => "TA status changed");
$return = json_encode($return);
echo $return;
?>

