<?php
// File: set_limit.php

require_once '../../model/auth.php';
require_once '../../model/courses.php';
require_once '../../model/queue.php';
require_once '../errors.php';

// get the session variables
session_start();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== "POST"){
  http_response_code(405);
  echo json_encode( invalid_method() );
  die();
}

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

if (!isset($_POST['time_lim']) || !is_numeric($_POST['time_lim']))
{
  echo json_encode( missing_time() );
  die();
}

$username   = $_SESSION['username'];
$course     = $_POST['course'];
$time_lim   = $_POST['time_lim'];
$ta_courses = $_SESSION["ta_courses"];

if (!in_array($course, $ta_courses))
{
  echo json_encode( not_authorized() );
  die();
}

$res = set_time_lim($time_lim, $course);
if ($res)
{
  $return = return_JSON_error($res);
}else
{
  $return = array(
    "authenticated" => True,
    "success" => "Time limit set"
  );
}
echo json_encode($return);
?>
