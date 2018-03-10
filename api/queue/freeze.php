<?php
// File: freeze.php

require_once '../../model/auth.php';
require_once '../../model/courses.php';
require_once '../../model/queue.php';
require_once '../errors.php';

// get the session variables
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['username']))
{
  $return = array("authenticated" => False);
  echo json_encode($return);
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

$res = freeze_queue($course);
if ($res != "frozen")
{
  $return = array(
    "authenticated" => True,
    "error" => "Unable to freeze queue"
  );
}else
{
  $return = array(
    "authenticated" => True,
    "success" => "Queue frozen"
  );
}
echo json_encode($return);
?>
