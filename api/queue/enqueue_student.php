<?php
// File: enqueue_student.php

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

if (!$_POST["course"] || !$_POST["question"] || !$_POST["location"])
{
  $return = array("error" => "Missing course/question/location");
  $return = json_encode($return);
  echo $return;
  die();
}

$username = $_SESSION['username'];
$course   = $_POST['course'];
$question = $_POST['question'];
$location = $_POST['location'];

if(enq_stu($username, $course, $question, $location))
{
  $return = array("error" => "Unable to enqueue student");
  $return = json_encode($return);
  echo $return;
  die();
}

$return = array("success" => "Student enqueued");
$return = json_encode($return);
echo $return;
?>

