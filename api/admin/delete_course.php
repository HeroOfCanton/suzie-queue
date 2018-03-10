<?php
// File: delete_course.php

require_once '../../model/auth.php';
require_once '../../model/courses.php';
require_once '../../model/queue.php';
require_once '../errors.php';

// get the session variables
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['username']))
{
  echo json_encode( not_authenticated() );
  die();
}

if (!isset($_SESSION['is_admin']))
{
  echo json_encode( not_authorized() );
  die();
}

if (!isset($_POST['course']))
{
  echo json_encode( missing_info() );
  die(); 
}
$course_name = $_POST['course'];

$res = del_course($course_name);
if ($res)
{
  $return = return_JSON_error($res);
}else
{
  $return = array(
    "authenticated" => True,
    "success" => "Course deleted"
  );
}
echo json_encode($return);
?>
