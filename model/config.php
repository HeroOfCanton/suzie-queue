<?php
define("LDAP_SERVER", "smith.eng.utah.edu");
define("LDAP_DOMAIN", "users.coe.utah.edu");
define("BIND_USER",   "ta-queue");
define("BIND_PASSWD", "Grad4ation18");
define("BASE_OU",     "DC=users,DC=coe,DC=utah,DC=edu");
define("COURSE_OU",   "OU=ENG Class Groups OU,OU=ENG OU,DC=users,DC=coe,DC=utah,DC=edu");

define("SQL_SERVER",  "localhost");
define("SQL_USER",    "FIXME");
define("SQL_PASSWD",  "FIXME");
define("DATABASE",    "FIXME");



/*
 *This maps a course name to its sAMAccountName in LDAP
 *
 *To add a new course to the queue, simply create a group
 *in LDAP for it in $COURSE_OU, and add it to the mapping
 *
 *Course_Name => group_sAMAccountName
 */ 
$courses_avail = array(
  "CS 9999" => "cs9999",
  "CS 9998" => "cs9998"
);
?>
