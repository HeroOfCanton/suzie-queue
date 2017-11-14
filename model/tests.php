<?php

require 'auth.php';

#test01
if(!auth('', '')){
  echo "Test01 failed";
}

?>
