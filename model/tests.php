<?php

require_once 'auth.php';
require_once 'courses.php';

#test01
if(!auth('ta-queue', 'Grad4ation18')){
  echo "Test01 failed";
}

#test02
if(!_ldap_connect()){
  echo "Test02 failed";
}

#test03
if(get_info("zakraise") == NULL){
  echo "Test 03 failed";
}

#test04
if(get_info("doesntExist") != NULL){
  echo "Test 04 failed";
}

$sam = dn_to_sam("CN=zakraise,OU=Domain Admin OU,DC=users,DC=coe,DC=utah,DC=edu");
if($sam == NULL || $sam =! 'zakraise'){
  echo "Test 05 failed";
}

$TAs = get_tas("CS 9999");
if($TAs == NULL){
 echo "Test 06 failed";
}
if($TAs[1] != "zakraise"){
  echo "Test 06 failed";
}

get_ta_courses('zakraise');

?>
