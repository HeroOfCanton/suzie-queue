<?php
// File: rem_class.php

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

if (!$_POST['course'])
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
$course = $_POST['course'];

if (!rem_stud_course($username, $course))
{
  $return = array(
    "authenticated" => True,
    "Student Course Removed Successfully"
  );
  $return = json_encode($return);
  echo $return;
}
else
{
  $return = array(
    "authenticated" => True,
    "error" => "Unable to Remove Student Course"
  );
  $return = json_encode($return);
  echo $return;
}

?>
