<?php
// File: get_info.php

require_once '../../model/auth.php';
require_once '../helper_functions.php';

// get the session variables
session_start();

user_authenticated();

$username = $_SESSION['username'];
$stud_info = get_info($username);

if ($stud_info == NULL)
{
  $return = array(
    "authenticated" => True,
    "error" => "Unable to Fetch Student Info"
  );
  $return = json_encode($return);
  echo $return;
  die();
}

$return = array(
  "authenticated" => True,
  "student_info" => $stud_info
);

$return = json_encode($return);
echo $return;
?>
