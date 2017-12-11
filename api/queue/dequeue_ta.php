<?php
// File: dequeue_ta.php

require_once '../../model/auth.php';
require_once '../../model/courses.php';
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

if(deq_ta($username, $course))
{
  $return = array("error" => "Unable to dequeue TA");
  $return = json_encode($return);
  echo $return;
  die();
}

$return = array("success" => "TA dequeued");
$return = json_encode($return);
echo $return;
?>

