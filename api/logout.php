<?php
// get the session variables
session_start();
header('Content-Type: application/json');

//Clear session variables
$_SESSION = array();

$return = array("authenticated" => False);
echo json_encode($return);
session_destroy();
?>
