<?php
// File: open.php

require_once '../../model/auth.php';
require_once '../../model/courses.php';
require_once '../../model/queue.php';

// get the session variables
session_start();
header('Content-Type: application/json');

if (!$_SESSION['username'])
{
  $return = array("authenticated" => False);
  echo json_encode($return);
  die();
}

if (!$_SESSION['is_admin'])
{
  $return = array(
    "authenticated" => True,
    "error" => "Not authorized to delete courses"
  );
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
$course_name = $_POST['course'];

$res = del_course($course_name);
if ($res)
{
  $return = array(
    "authenticated" => True,
    "error" => "Unable to delete course"
  );
}else
{
  $return = array(
    "authenticated" => True,
    "success" => "Course deleted"
  );
}
echo json_encode($return);
?>
