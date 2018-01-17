<?php
// File: enqueue_ta.php

require_once '../../model/auth.php';
require_once '../../model/course.php';
require_once '../../model/queue.php';
require_once '../helper_functions.php';

// get the session variables
session_start();

// return authenticated False if user isn't authenticated
user_authenticated();
course_posted();

$username = $_SESSION['username'];
$course   = $_POST['course'];

$isTA = FALSE;
if (in_array($username, get_tas($course)))
{
  $isTA = TRUE;
}

$queue = get_queue($course);

if($queue == NULL)
{
  $return = array("error" => "Unable to fetch queue state");
  $return = json_encode($return);
  echo $return;
}

$return = json_encode($queue, JSON_PRETTY_PRINT);
echo $return;
?>
