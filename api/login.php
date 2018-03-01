<?php
// File: login.php

require_once '../model/auth.php';
require_once '../model/courses.php';
require_once './errors.php';

session_start();
$_SESSION = array();
header('Content-Type: application/json');

if(!$_POST['username'] || !$_POST['password'])
{
  echo json_encode( invalid_auth() );
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
  echo json_encode( ldap_issue() );
  die();
}

$is_admin = is_admin($username);
if(is_null($is_admin))
{
  echo json_encode( ldap_issue() );
  die();
}

$ta_courses = get_ta_courses($username);
if(is_null($ta_courses))
{
  echo json_encode( ldap_issue() );
  die();
}

$_SESSION["ta_courses"] = $ta_courses;
$_SESSION["username"]   = $username;
$_SESSION["is_admin"]   = $is_admin;
$info["authenticated"]  = TRUE;

echo json_encode($info);
?>
