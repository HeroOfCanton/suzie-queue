<?php
// File: add_announcement.php

require_once '../../model/auth.php';
require_once '../../model/courses.php';
require_once '../../model/queue.php';
require_once '../errors.php';

// get the session variables
session_start();
header('Content-type: application/json');

if (!$_SESSION['username'])
{
  echo json_encode( not_authenticated() );
  die();
}

if (!$_POST['course'])
{
  echo json_encode( missing_course() );
  die();
}

if (!$_POST['announcement'])
{
  echo json_encode( missing_announcement() );
  die();
}

$username     = $_SESSION['username'];
$course       = $_POST['course'];
$announcement = $_POST['announcement'];
$ta_courses   = $_SESSION["ta_courses"];

if (!in_array($course, $ta_courses))
{
  echo json_encode( not_authorized() );
  die();
}

$res = add_announcement($course, $announcement);
if($res < 0)
{
  $return = return_JSON_error($res);
}else
{
  $return = array(
    "authenticated" => True,
    "success" => "Announcement set"
  );
}

echo json_encode($return);
?>
