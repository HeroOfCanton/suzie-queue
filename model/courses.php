<?php
require_once 'config.php';

/*
 *Returns list of all possible courses in LDAP
 *Each course has a group in LDAP
 */
function get_avail_courses(){
  #Eventually we'll check this list against LDAP and only return
  #the courses who's LDAP groups exist.
  global $courses_avail;
  return array_keys($courses_avail);
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
 *Returns a list of TAs for the course.
 *Information is pulled from LDAP
 */
function get_tas($course){
  $course_group = course_lookup($course);
  if($course_group == NULL){
    return NULL;
  }

  $ldap_conn = _ldap_connect();

  if($ldap_conn == NULL){
    return NULL;
  }

  $filter = "(sAMAccountName=$course_group)";
  $results = ldap_search($ldap_conn, BASE_OU, $filter);
  $entries = ldap_get_entries($ldap_conn, $results);

  if(!$entries["count"]){
    return NULL;
  }

  $members = $entries[0]["member"];
  foreach($members as &$member) {
    $member = dn_to_sam($member);
  }

  _ldap_disconnect($ldap_conn);

  return $members;
}

/*
 *Get courses that the individual is a TA for
 *NOTE: These are taken from LDAP, and not stored in SQL
 */
function get_ta_courses($username){
  global $courses_avail;
  $ldap_conn = _ldap_connect();

  if($ldap_conn == NULL){
    return NULL;
  }

  $filter = "(sAMAccountName=$username)";
  $results = ldap_search($ldap_conn, BASE_OU, $filter);
  $entries = ldap_get_entries($ldap_conn, $results);
  
  if(!$entries["count"]){
    return NULL;
  }
  
  $groups = $entries[0]["memberof"];
  $courses = array();
  foreach($groups as $group) {
    $group_sam = dn_to_sam($group);
    $course = array_search($group_sam, $courses_avail);
    if($course){
      $courses[] = $course;
    }
  }

  _ldap_disconnect($ldap_conn);

  return $courses;
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





######### HELPER METHODS #########
/*
 *Return the Active Directory group name for a course
 */
function course_lookup($course){
  global $courses_avail;
  return $courses_avail[$course];
}




?>
