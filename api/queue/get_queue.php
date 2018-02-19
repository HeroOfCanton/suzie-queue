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

$username   = $_SESSION['username'];
$course     = $_POST['course'];
$ta_courses = $_SESSION["ta_courses"];

//For now, these return the same information.
//Later, we may want the TAs to see more,
//or the students to see less.
if (in_array($course, $ta_courses))
{
  $queue = get_queue($course);
}
else
{
  $queue = get_queue($course);
}

if(is_null($queue))
{
  $return = array(
    "authenticated" => True,
    "error" => "Unable to fetch queue state"
  );
  echo json_encode($return);
}

echo json_encode($queue, JSON_PRETTY_PRINT);
?>
