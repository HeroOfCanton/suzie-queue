<?php
// File: dequeue_student.php

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
  $return = array("error" => "Missing course");
  $return = json_encode($return);
  echo $return;
  die();
}

$username = $_SESSION['username'];
$course   = $_POST['course'];

if(deq_stu($username, $course)){
  $return = array("error" => "Unable to dequeue student");
  $return = json_encode($return);
  echo $return;
  die();
}

$return = array("success" => "Student dequeued");
$return = json_encode($return);
echo $return;
?>

