<?php
// File: helper_functions.php

require_once '../model/courses.php';

function user_authenticated()
{
  if (!$_SESSION['username'])
  {
    $return = array("authenticated" => False);
    $return = json_encode($return);
    echo $return;
    die();
  }
}

function course_posted()
{
  if (!$_POST['course'])
  {
    $return = array(
      "authenticated" => True,
      "error" => "No course specified"
    );
    $return = json_encode($return);
    echo $return;
    die();
  }
}

function ta_assigned_to_course()
{
  if (!in_array($username, get_tas($course)))
  {
    $return = array(
      "authenticated" => True,
      "error" => "TA not assigned to course"
    );
    $return = json_encode($return);
    die();
  }
}
?>
