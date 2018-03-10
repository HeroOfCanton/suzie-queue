<?php
// File: create_course.php

require_once '../../model/auth.php';
require_once '../../model/courses.php';
require_once '../../model/queue.php';
require_once '../errors.php';

// get the session variables
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['username']))
{
  echo json_encode( not_authenticated() );
  die();
}

if (!isset($_SESSION['is_admin']))
{
  echo json_encode( not_authorized() );
  die();
}

if (!isset($_POST['course_name']) || !isset($_POST['depart_prefix']) || !isset($_POST['course_num']) || 
    !isset($_POST['description']) || !isset($_POST['ldap_group'])    || !isset($_POST['professor']))
{
  echo json_encode( missing_info() );
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
if ($res < 0)
{
  $return = return_JSON_error($res);
}else
{
  $return = array(
    "authenticated" => True,
    "success" => "Course created/updated"
  );
}
echo json_encode($return);
?>
