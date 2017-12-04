<?php
// File: open.php

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
  $return = array("error" => "No course specified");
  $return = json_encode($return);
  echo $return;
  die();
}

$username = $_SESSION['username'];
$course   = $_POST['course'];

// make sure the TA is assigned to the course
if (!in_array($username, get_tas($course)))
{
  $return = array("error" => "TA not assigned to course");
  $return = json_encode($return);
  echo $return;
  die();
}

if(open_queue($course) != "open"){
  $return = array("error" => "Unable to open course");
  $return = json_encode($return);
  echo $return;
  die();
}

$return = array("success" => "queue open");
$return = json_encode($return);
echo $return;
?>

