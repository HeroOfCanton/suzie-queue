<?php
// File: enqueue_ta.php

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

if(enq_ta($username, $course))
{
  $return = array("error" => "Unable to enqueue TA");
  echo json_encode($return);
  die();
}

$return = array("success" => "TA enqueued");
echo json_encode($return);
?>

