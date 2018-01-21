<?php
// File: dequeue_student.php

require_once '../../model/auth.php';
require_once '../../model/courses.php';
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

//Since this enpoint is used for students to
//remove themselves, and TAs to remove students,
//we check if the request came from a TA
if (in_array($username, get_tas($course)))
{
  if (!$_POST['username'])
  {
    $return = array(
      "authenticated" => True,
      "error" => "TA didn't specify student to remove"
    );
    echo json_encode($return);
    die();
  }
  $username = $_POST['username'];
}

if(deq_stu($username, $course))
{
  $return = array("error" => "Unable to dequeue student");
  echo json_encode($return);
  die();
}

$return = array("success" => "Student dequeued");
echo json_encode($return);
?>

