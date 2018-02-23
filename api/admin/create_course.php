<?php
// File: open.php

require_once '../../model/auth.php';
require_once '../../model/courses.php';
require_once '../../model/queue.php';

// get the session variables
session_start();
header('Content-Type: application/json');

if (!$_SESSION['username'])
{
  $return = array("authenticated" => False);
  echo json_encode($return);
  die();
}

if (!$_SESSION['is_admin'])
{
  $return = array(
    "authenticated" => True,
    "error" => "Not authorized to create courses"
  );
  echo json_encode($return);
  die();
}

if (!$_POST['course_name'] || !$_POST['depart_prefix'] || !$_POST['course_num'] || 
    !$_POST['description'] || !$_POST['ldap_group']    || !$_POST['professor'])
{
  $return = array(
    "authenticated" => True,
    "error" => "Missing required info"
  );
  echo json_encode($return);
  die();
}

$course_name   = $_POST['course_name'];
$depart_prefix = $_POST['depart_prefix'];
$course_num    = $_POST['course_num'];
$description   = $_POST['description'];
$ldap_group    = $_POST['ldap_group'];
$professor     = $_POST['professor'];
if ($_POST['acc_code'])
{
  $acc_code    = $_POST['acc_code'];
}else{
  $acc_code    = null;
}

$res = new_course($course_name, $depart_prefix, $course_num, $description, $ldap_group, $professor, $acc_code);
if ($res)
{
  $return = array(
    "authenticated" => True,
    "error" => "Unable to create course"
  );
}else
{
  $return = array(
    "authenticated" => True,
    "success" => "Course created/updated"
  );
}
echo json_encode($return);
?>
