<?php
// File: dequeue_student.php

require_once '../../model/auth.php';
require_once '../../model/courses.php';
require_once '../../model/queue.php';
require_once '../errors.php';

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
  echo json_encode( missing_course() );
  die();
}

$username   = $_SESSION['username'];
$course     = $_POST['course'];
$ta_courses = $_SESSION["ta_courses"];

//Since this enpoint is used for students to
//remove themselves, and TAs to remove students,
//we check if the request came from a TA
if (in_array($course, $ta_courses)){
  if (!$_POST['username'])
  {
    $return = array(
      "authenticated" => True,
      "error" => "TA didn't specify student to remove"
    );
    echo json_encode($return);
    die();
  }
  $username = $_POST['username']; // Set to dequeue another student
}

$res = deq_stu($username, $course);
if($res)
{
  $return = array(
    "authenticated" => True,
    "error" => "Unable to dequeue student"
  );
}else{
  $return = array(
    "authenticated" => True,  
    "success" => "Student dequeued"
  );
}
echo json_encode($return);
?>

