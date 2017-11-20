<?php
require_once '../model/auth.php';

session_start();

if(!$_POST['username'] || !$_POST['password']){
  echo "{}";
  die();
}

$username = $_POST['username'];
$password = $_POST['password'];

if(!auth($username, $password)){
  echo "{}";
  die();
}

$info = get_info($username);
if($info == NULL){
  echo "{}";
  die();
}

$_SESSION["username"] = $username;
$_SESSION["authed"]   = TRUE;

$user_json = json_encode($info);
echo $user_json;
?>
