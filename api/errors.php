<?php
// File: errors.php

//Error codes returned by the model
function return_JSON_error($err_code){
  $err_codes = array(
    -1 => "Generic SQL error",
    -2 => "Course does not exist",
    -3 => "Queue is closed for this course",
    -4 => "TA not on duty",
  );
  return array(
    "authenticated" => True,
    "error" => $err_codes[$err_code]
  );
}


function invalid_auth(){
  return  array(
    "authenticated" => False,
    "error" => "No username and/or password supplied"
  );
}

function not_authenticated(){
  return array("authenticated" => False);
}

function not_authorized(){
  return array(
    "authenticated" => True,
    "error" => "Not authorized"
  );
}

function missing_info(){
  return array(
    "authenticated" => True,
    "error" => "Missing required info"
  );
}

function missing_course(){
  return array(
    "authenticated" => True,
    "error" => "No course specified"
  );
}

function missing_student(){
  return array(
    "authenticated" => True,
    "error" => "No student specified"
  );
}

function missing_time(){
  return array(
    "authenticated" => True,
    "error" => "No time_lim specified"
  );
}

function missing_announcement(){
  return array(
    "authenticated" => True,
    "error" => "No annoucement given"
  );
}

function ldap_issue(){
  return array(
    "authenticated" => True,
    "error" => "Unable to Retrieve Info from LDAP"
  );
}

function course_list_error(){
  return array(
    "authenticated" => True,
    "error" => "Unable to Fetch All Courses"
  );
}

?>