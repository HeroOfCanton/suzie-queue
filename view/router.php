<?php
session_start();

//Make sure requests for view pages are authenticated
if (!$_SESSION["username"]){
  header("Location: ./index.html");
  die();
}
?>
