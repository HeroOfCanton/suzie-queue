<?php

require_once '../auth.php';
require_once '../courses.php';
require_once '../queue.php';


if (new_course("CS 1030: Foundations of CS", "CS", "1030", "The hard stuff 1!", "fake 1")){
  echo "Test 10.5 failed";
  die();
}

if (new_course("CS 1410: Introduction to Object-Oriented Programming", "CS", "1410", "The hard stuff 1!", "fake 1")){
  echo "Test 10.5 failed";
  die();
}

if (new_course("CS 2100: Descrete Scructures", "CS", "2100", "The hard stuff 1!", "fake 1")){
  echo "Test 10.5 failed";
  die();
}

if (new_course("CS 2420: Introduction to Algorithms and Data Structures", "CS", "2420", "The hard stuff 1!", "fake 1")){
  echo "Test 10.5 failed";
  die();
}

if (new_course("CS 3100: Models of Computation", "CS", "3100", "The hard stuff 1!", "fake 1")){
  echo "Test 10.5 failed";
  die();
}

if (new_course("CS 3500: Software Practice 1", "CS", "3500", "The hard stuff 1!", "fake 1")){
  echo "Test 10.5 failed";
  die();
}

if (new_course("CS 3505: Software Practice 2", "CS", "3505", "The hard stuff 1!", "fake 1")){
  echo "Test 10.5 failed";
  die();
}

if (new_course("CS 3810: Computer Organization", "CS", "3810", "The hard stuff 1!", "fake 1")){
  echo "Test 10.5 failed";
  die();
}

if (new_course("CS 4150: Algorithms", "CS", "4150", "The hard stuff 1!", "fake 1")){
  echo "Test 10.5 failed";
  die();
}

if (new_course("CS 4400: Computer Systems", "CS", "4400", "The hard stuff 1!", "cs4400-queue-test")){
  echo "Test 10.5 failed";
  die();
}



echo "Setup Completed Successfully";
?>
