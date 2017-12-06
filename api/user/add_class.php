<?php
// File: add_class.php

require_once '../../model/courses.php';

// get the session variables
session_start(); 

// return authenticated False if user isn't authenticated
if (!$_SESSION["username"])
{
  $return = array("authenticated" => False);
  $return = json_encode($return);
  echo $return;
  die();	
}

if (!$_POST['class'])
{
  $return = array(
    "authenticated" => True,
    "error" => "No Class Specified"
  );
  $return = json_encode($return);
  echo $return;
  die();
}

$username = $_SESSION['username'];
$class = $_POST['class'];

if (!add_stud_course($username, $class))
{
  $return = array(
    "authenticated" => True,
    "Student Course Added Successfully"
  );
}
else
{
  $return = array(
    "authenticated" => True,
    "error" => "Unable to Add Student Course"
  );
}

$return = json_encode($return);
echo $return;
?>
