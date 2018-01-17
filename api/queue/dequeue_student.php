<?php
// File: dequeue_student.php

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

if(deq_stu($username, $course))
{
  $return = array("error" => "Unable to dequeue student");
  $return = json_encode($return);
  echo $return;
  die();
}

$return = array("success" => "Student dequeued");
$return = json_encode($return);
echo $return;
?>

