<?php
// File: rem_class.php

require_once '../../model/courses.php';
require_once '../helper_functions.php';

// get the session variables
session_start(); 

user_authenticated();
course_posted();

$username = $_SESSION['username'];
$course = $_POST['course'];

if (!rem_stud_course($username, $course))
{
  $return = array(
    "authenticated" => True,
    "Student Course Removed Successfully"
  );
}
else
{
  $return = array(
    "authenticated" => True,
    "error" => "Unable to Remove Student Course"
  );
}

$return = json_encode($return);
echo $return;
?>
