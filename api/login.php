<?php
require_once '../model/auth.php';

session_start();

if(!$_POST['username'] || !$_POST['password']){
  $return = array("error" => "No username or password");
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
$info["authenticated"] = TRUE;

$user_json = json_encode($info);
//echo $user_json;

header('Location: ../view/all_classes_view.html');
?>
