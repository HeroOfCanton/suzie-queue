<?php
// File: get_info.php

require_once '../../model/auth.php';

// get the session variables
session_start();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== "GET"){
  http_response_code(405);
  echo json_encode( invalid_method() );
  die();
}

if (!isset($_SESSION['username']))
{
  $return = array("authenticated" => False);
  echo json_encode($return);
  die();
}

$username  = $_SESSION['username'];
$stud_info = get_info($username);

if (is_null($stud_info))
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
