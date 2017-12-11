<?php
// File: all_classes.php

require_once '../../model/courses.php';
require_once '../helper_functions.php';

// get the session variables
//session_start();

// return authenticated False if user isn't authenticated
//if (!$_SESSION["username"])
//{
//  $return = array("authenticated" => False);
//  $return = json_encode($return);
//  echo $return;
//  die();
//}

// SHOULD BE ABLE TO CALL THIS INSTEAD OF ABOVE IF STATEMENT
// user_authenticated();

//$username = $_SESSION['username'];

$all_courses = get_avail_courses();

if ($all_courses == NULL)
{
  $return = array(
    "authenticated" => True,
    "error" => "Unable to Fetch All Courses"
  );
  $return = json_encode($return);
  echo $return;
  die();
}

$return = array(
  "authenticated" => True,
  "all_courses" => $all_courses
);

$return = json_encode($return);
echo $return;

?>

