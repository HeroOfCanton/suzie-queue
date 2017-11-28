<?php
require_once '../../model/courses.php';

// get the session variables
session_start(); 

// return authentication False if user isn't authenticated
if (!$_SESSION["username"])
{
  $return = array("authenticated" => False);
  $return = json_encode($return);
  echo $return;
  die();	
}

if (!$_POST['class'])
{
  $return = array("error" => "No Class Specified");
  $return = json_encode($return);
  echo $return;
  die();
}

$username = $_SESSION['username'];
$class = $_POST['class'];

if (rem_stud_course($username, $class) != NULL)
{
  $return = array("Success");
  $return = json_encode($return);
  echo $return;
}
else
{
  $return = array("error" => "Unable to Add Course");
  $return = json_encode($return);
  echo $return;
}
?>
