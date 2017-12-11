<?php
// File: enqueue_student.php

require_once '../../model/auth.php';
require_once '../../model/course.php';
require_once '../../model/queue.php';
require_once '../helper_functions.php';

// get the session variables
session_start();

user_authenticated();

if (!$_POST["course"] || !$_POST["question"] || !$_POST["location"])
{
  $return = array("error" => "Missing course, question, or location");
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

