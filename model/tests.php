<?php

require_once 'auth.php';
require_once 'courses.php';

#test01
if(!auth(BIND_USER, BIND_PASSWD)){
  echo "Test01 failed";
  die();
}

#test02
if(!_ldap_connect()){
  echo "Test02 failed";
  die();
}

#test03
if(get_info("zakraise") == NULL){
  echo "Test 03 failed";
  die();
}

#test04
if(get_info("doesntExist") != NULL){
  echo "Test 04 failed";
  die();
}

$sam = dn_to_sam("CN=zakraise,OU=Domain Admin OU,DC=users,DC=coe,DC=utah,DC=edu");
if($sam == NULL || $sam =! 'zakraise'){
  echo "Test 05 failed";
  die();
}

$TAs = get_tas("CS 9999");
if($TAs == NULL){
 echo "Test 06 failed";
 die();
}
if($TAs[1] != "zakraise"){
  echo "Test 06 failed";
  die();
}

get_ta_courses('zakraise');
if(get_ta_courses('fake-user') != NULL){
  echo "Test 08 failed";
  die();

}

if (touch_user("zakraise", "zane", "zak", "zane zak")){
  echo "Test 09 failed";
  die();
}

if (new_course("Computer Systems", "CS", "4400", "The hard stuff!", "fake")){
  echo "Test 10 failed";
  #die();
}

if (!new_course("Computer Systems", "CS", "4401", "The hard stuff 1!", "fake 1")){
  echo "Test 10.5 failed";
  die();
}


if (add_stud_course("zakraise", "Computer Systems")){
  echo "Test 11 failed";
  die();
}

if (add_stud_course("zakraise", "Computer Systems")){
  echo "Test 12 failed";
  die();
}

for ($i = 0; $i<10; $i++){
  if (rem_stud_course("zakraise", "Computer Systems")){
    echo "Test 13 failed";
    die();
  }
}

for ($i = 0; $i<10; $i++){
  if (add_stud_course("zakraise", "Computer Systems")){
    echo "Test 14 failed";
    die();
  }
}


if (new_course("Algorithms", "CS", "4150", "Algorithms", "fake")){
  echo "Test 15 failed";
  //die();
}
if (add_stud_course("zakraise", "Algorithms")){
  echo "Test 15 failed";
  die();
}

echo get_stud_courses("zakraise")[1];

?>
