<?php
// File: errors.php

//Error codes returned by the model
$err_codes = [
  "-1" => "Generic SQL error",
  "-2" => "Course does not exist",
  "-3" => "Queue is closed for this course",
  "-4" => "TA not on duty",
];

function return_JSON_error($err_code){
  $return = array(
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
