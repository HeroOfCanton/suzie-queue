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

?>
