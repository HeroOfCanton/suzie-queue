<?php

require 'auth.php';

#test01
#if(!auth('', '')){
#  echo "Test01 failed";
#}

#test02
if(!_ldap_connect()){
  echo "Test02 failed";
}

get_info("zakraise");

?>
