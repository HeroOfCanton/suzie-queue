<?php
// File: release_ta.php

require_once '../../model/auth.php';
require_once '../../model/courses.php';
require_once '../../model/queue.php';
require_once '../errors.php';

// get the session variables
session_start();
header('Content-type: application/json');

if (!isset($_SESSION['username']))
{
  echo json_encode( not_authenticated() );
  die();
}

if (!isset($_POST['course']))
{
  echo json_encode( missing_course() );
  die();
}

$username   = $_SESSION['username'];
$course     = $_POST['course'];
$ta_courses = $_SESSION["ta_courses"];

if (!in_array($course, $ta_courses))
{
  echo json_encode( not_authorized() );
  die();
}

$res = free_ta($username, $course);
if($res)
{
  $return = return_JSON_error($res);
}else
{
  $return = array(
    "authenticated" => True,
    "success" => "TA status changed"
  );
}
echo json_encode($return);
?>
