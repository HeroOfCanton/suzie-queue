<?php
// File: dequeue_student.php

require_once '../../model/auth.php';
require_once '../../model/course.php';
require_once '../../model/queue.php';

// get the session variables
session_start();
header('Content-type: application/json');

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

if(deq_stu($username, $course))
{
  $return = array("error" => "Unable to dequeue student");
  echo json_encode($return);
  die();
}

$return = array("success" => "Student dequeued");
echo json_encode($return);
?>

