<?php
// File: close.php

require_once '../../model/auth.php';
require_once '../../model/courses.php';
require_once '../../model/queue.php';

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

$username = $_SESSION['username'];
$course   = $_POST['course'];

// make sure TA is assigned to the course
if (!in_array($username, get_tas($course)))
{
  $return = array(
    "authenticated" => True,
    "error" => "TA not assigned to course"
  );
  echo json_encode($return);
  die();
}

if(close_queue($course) != "closed")
{
  $return = array("error" => "Unable to close queue");
  echo json_encode($return);
  die();
}

$return = array("success" => "Queue closed");
echo json_encode($return);
?>
