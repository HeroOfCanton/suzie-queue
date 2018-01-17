<?php
// File: add_class.php

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

if (!add_stud_course($username, $course))
{
  $return = array(
    "authenticated" => True,
    "message" => "Student Course Added Successfully"
  );
}
else
{
  $return = array(
    "authenticated" => True,
    "error" => "Unable to Add Student Course"
  );
}

header('Content-Type: application/json');
echo json_encode($return);
?>
