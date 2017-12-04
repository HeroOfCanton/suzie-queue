<?php
require_once '../model/auth.php';

session_start();
redirectToHTTPS();

require_once '../view/login_view.php'

if(!$_POST['username'] || !$_POST['password']){
  $return = array("error" => "No username and/or password");
  $return = json_encode($return);
  echo $return; 
  die();
}

$username = $_POST['username'];
$password = $_POST['password'];

if(!auth($username, $password)){
  $return = array("authenticated" => False);
  $return = json_encode($return);
  echo $return;
  die();
}

$info = get_info($username);
if($info == NULL){
  $return = array("error" => "Unable to Retrieve Info");
  $return = json_encode($return);
  echo $return;
  die();
}

$_SESSION["username"] = $username;
$_SESSION["authed"]   = TRUE;

$user_json = json_encode($info);
echo $user_json;
?>
