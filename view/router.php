<?php
session_start();

$REQUEST_URI = $_SERVER["REQUEST_URI"];

if(strpos($REQUEST_URI, 'index.php')){
  if($_SESSION["username"]){
    header("Location: ./classes.php");
    die();
  }
}
else{
  if (!$_SESSION["username"]){
    header("Location: ../index.php");
    die();
  }
}

?>
