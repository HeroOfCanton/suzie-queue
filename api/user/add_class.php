<?php
// File: add_class.php

require_once '../../model/courses.php';
require_once '../helper_functions.php';

// get the session variables
session_start(); 

user_authenticated();
course_posted();

$username = $_SESSION['username'];
$course = $_POST['course'];

if (!add_stud_course($username, $course))
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
