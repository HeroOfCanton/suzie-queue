<?php
// File: login.php

require_once '../model/auth.php';
require_once '../model/courses.php';

session_start();
header('Content-Type: application/json');

if(!$_POST['username'] || !$_POST['password'])
{
  $return = array("error" => "No username and/or password");
  echo json_encode($return);
  die();
}

$username = $_POST['username'];
$password = $_POST['password'];

if(!auth($username, $password))
{
  $return = array("authenticated" => False);
  echo json_encode($return);
  die();
}

$info = get_info($username);

if($info == NULL)
{
  $return = array("error" => "Unable to Retrieve Info");
  echo json_encode($return);
  die();
}

$_SESSION["username"] = $username;
$info["authenticated"] = TRUE;

echo json_encode($info);
?>
