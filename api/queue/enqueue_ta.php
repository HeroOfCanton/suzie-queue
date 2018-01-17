<?php
// File: enqueue_ta.php

require_once '../../model/auth.php';
require_once '../../model/course.php';
require_once '../../model/queue.php';
require_once '../helper_functions.php';

// get the session variables
session_start();

user_authenticated();
course_posted();

$username = $_SESSION['username'];
$course   = $_POST['course'];

// make sure TA is assigned to the course
ta_assigned_to_course();

if(enq_ta($username, $course))
{
  $return = array("error" => "Unable to enqueue TA");
  $return = json_encode($return);
  echo $return;
  die();
}

$return = array("success" => "TA enqueued");
$return = json_encode($return);
echo $return;
?>

