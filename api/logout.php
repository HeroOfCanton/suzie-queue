<?php
// File: logout.php

// get the session variables
session_start();
header('Content-Type: application/json');

//Clear session variables
$_SESSION = array();

$return = array(
    "authenticated" => False,
    "success" => "User logged out"
);
echo json_encode($return);
session_destroy();
?>
