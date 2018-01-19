<?php
// File: enqueue_ta.php

require_once '../../model/auth.php';
require_once '../../model/courses.php';
require_once '../../model/queue.php';

// get the session variables
session_start();
header('Content-type: application/json');

// return authenticated False if user isn't authenticated
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

if (!in_array($username, get_tas($course)))
{
  $return = array(
    "authenticated" => True,
    "error" => "TA not assigned to course"
  );
  echo json_encode($return);
  die();
}

$queue = get_queue($course);

if($queue == NULL)
{
  $return = array("error" => "Unable to fetch queue state");
  echo json_encode($return);
}

echo json_encode($queue, JSON_PRETTY_PRINT);
?>
