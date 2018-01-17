<?php
// File: rem_class.php

require_once '../../model/courses.php';

// get the session variables
session_start(); 

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
  header('Content-Type: application/json');
  echo json_encode($return);
  die();
}

$username = $_SESSION['username'];
$course = $_POST['course'];

if (!rem_stud_course($username, $course))
{
  $return = array(
    "authenticated" => True,
    "message" => "Student Course Removed Successfully"
  );
}
else
{
  $return = array(
    "authenticated" => True,
    "error" => "Unable to Remove Student Course"
  );
}

header('Content-Type: application/json');
echo json_encode($return);
?>
