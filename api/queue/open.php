<?php
// File: open.php

require_once '../../model/auth.php';
require_once '../../model/course.php';
require_once '../../model/queue.php';
require_once '../helper_functions.php';

// get the session variables
session_start();
header('Content-Type: application/json');

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

if (!in_array($username, get_tas($course)))
{
  $return = array(
    "authenticated" => True,
    "error" => "TA not assigned to course"
  );
  echo json_encode($return);
  die();
}

if (open_queue($course) != "open")
{
  $return = array("error" => "Unable to open queue");
}else
{
  $return = array("success" => "Queue opened");
}
echo json_encode($return);
?>
