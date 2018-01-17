<?php
// File: get_info.php

require_once '../../model/auth.php';

// get the session variables
session_start();
header('Content-Type: application/json');

if (!$_SESSION['username'])
{
  $return = array("authenticated" => False);
  echo json_encode($return);
  die();
}

$username = $_SESSION['username'];
$stud_info = get_info($username);

if ($stud_info == NULL)
{
  $return = array(
    "authenticated" => True,
    "error" => "Unable to Fetch Student Info"
  );
}else
{
  $return = array(
    "authenticated" => True,
    "student_info" => $stud_info
  );
}

echo json_encode($return);
?>
