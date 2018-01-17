<?php
// File: next_student.php

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
