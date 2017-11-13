<?php
require 'config.php';

/*
 *Returns list of all possible courses in LDAP
 *Each course has a group in LDAP
 */
function get_avail_courses(){
}

/*
 *Adds the course to the database
 */
function new_course($course){
}

/*
 *Removes the course from the database
 */
function del_course($course){
}



/*
 *Returns a list of TAs for the course
 *Information is pulled from LDAP
 */
function get_tas($course){
}

/*
 *Get courses that the individual is a TA for
 *NOTE: These are taken from LDAP, and not stored in SQL
 */
function get_ta_courses($username){
}



/*
 *Get courses that the student has joined.
 *NOTE: Does not return courses an individual is a TA for.
 */
function get_stud_courses($username){
}

/*
 *Add student to course in database
 *NOTE: Not meant for TAs
 */
function add_stud_course($username, $course){
}

/*
 *Remove student from course in databse
 *NOTE: Not meant for TAs
 */
function rem_stud_course($username, $course){
}

?>
