<?php
// File: get_info.php

require_once '../../model/auth.php';
require_once '../errors.php';

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
  http_response_code(401);
  echo json_encode( not_authenticated() );
  die();
}

$username  = $_SESSION['username'];
$stud_info = get_info($username);

if (is_null($stud_info))
{
  $return = ldap_issue();
  http_response_code(500);
}else
{
  $return = array(
    "authenticated" => True,
    "student_info" => $stud_info
  );
  http_response_code(200);
}

echo json_encode($return);
?>
