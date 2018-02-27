<?php
// File: login.php

require_once '../model/auth.php';
require_once '../model/courses.php';

session_start();
$_SESSION = array();
header('Content-Type: application/json');

if(!$_POST['username'] || !$_POST['password'])
{
  $return = array(
    "authenticated" => False,
    "error" => "No username and/or password"
  );
  echo json_encode($return);
  die();
}

$username = $_POST['username'];
$password = $_POST['password'];

if(!auth($username, $password))
{
  $return = array(
    "authenticated" => False,
    "error" => "Username and/or password is incorrect"
  );
  echo json_encode($return);
  die();
}

$info = get_info($username);

if(is_null($info))
{
  $return = array(
    "authenticated" => True,
    "error" => "Unable to Retrieve Info"
  );
  echo json_encode($return);
  die();
}

$is_admin = is_admin($username);
if(is_null($is_admin))
{
  $return = array(
    "authenticated" => True,
    "error" => "Unable to Retrieve group status"
  );
  echo json_encode($return);
  die();
}

$ta_courses = get_ta_courses($username);

if(is_null($ta_courses))
{
  $return = array(
      "authenticated" => True,
      "error" => "Unable to Retrieve group status"
  );
  echo json_encode($return);
  die();
}

$_SESSION["ta_courses"] = $ta_courses;
$_SESSION["username"]   = $username;
$_SESSION["is_admin"]   = $is_admin;
$info["authenticated"]  = TRUE;

echo json_encode($info);
?>
