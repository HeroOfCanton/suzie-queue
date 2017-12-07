<?php
require_once 'config.php';
require_once 'auth.php';

/*
 *Returns list of all possible courses in LDAP
 *Each course has a group in LDAP
 */
function get_avail_courses(){
  $sql_conn = mysqli_connect(SQL_SERVER, SQL_USER, SQL_PASSWD, DATABASE);
  if(!$sql_conn){
    return NULL; //error
  }

  $query  = "SELECT course_name FROM courses";
  $result = mysqli_query($sql_conn, $query);

  $courses = array();
  while($entry = mysqli_fetch_assoc($result)){
    $courses[] = $entry["course_name"];
  }

  mysqli_close($sql_conn);
  return $courses;
}

/*
 *Adds the course to the database
 *Only authorized teachers can call this.
 *Course LDAP group should exist first
 *  When a new course is to be created, they need to send 
 *  the IT people a request for a new group. The group sam goes here.
 */
function new_course($course_name, $depart_prefix, $course_num, $description, $ldap_group){
  #Check input validity

  $sql_conn = mysqli_connect(SQL_SERVER, SQL_USER, SQL_PASSWD, DATABASE);
  if(!$sql_conn){
    return 1;
  }
  
  #Check if course_name already exists!
  
  $query = "INSERT INTO courses (depart_pref, course_num, course_name, description, ldap_group) VALUES ('".$depart_prefix."','".$course_num."','".$course_name."','".$description."','".$ldap_group."')";
  if(!mysqli_query($sql_conn, $query)){
    mysqli_close($sql_conn);
    return 1;
  }

  mysqli_close($sql_conn);
  return 0;
}

/*
 *Removes the course from the database
 *IMPLEMENT IN SPRING
 */
function del_course($course_name){
}



/*
 *Returns a list of TAs for the course.
 *Information is pulled from LDAP
 */
function get_tas($course){
  $course_group = get_course_group($course);
  if($course_group == NULL){
    return NULL;
  }

  $result = srch_by_sam($course_group);
  if($result == NULL){
    return NULL;
  }

  $members = $result["member"];
  foreach($members as &$member) {
    $member = dn_to_sam($member);
  }

  return $members;
}

/*
 *Get courses that the individual is a TA for
 *NOTE: These are taken from LDAP, and not stored in SQL
 */
function get_ta_courses($username){
  $result = srch_by_sam($username);
  if($result == NULL){
    return NULL;
  } 

  $sql_conn = mysqli_connect(SQL_SERVER, SQL_USER, SQL_PASSWD, DATABASE);
  if(!$sql_conn){
    return NULL; //error
  }
 
  $groups = $result["memberof"];
  $courses = array();
  foreach($groups as $group) {
    $group_sam = dn_to_sam($group);

    $query  = "SELECT course_name FROM courses WHERE ldap_group ='".$group_sam."'";
    $result = mysqli_query($sql_conn, $query);
    if(!mysqli_num_rows($result)){
      continue; //No class in the database with this ldap group
    }
    
    //possible multiple courses use the same ldap_group
    while($entry = mysqli_fetch_assoc($result)){
      $courses[] = $entry["course_name"]; 
    }
    
  }

  mysqli_close($sql_conn);
  return $courses;
}

/*
 *Get courses that the student has joined.
 *NOTE: Does not return courses an individual is a TA for.
 */
function get_stud_courses($username){
  $sql_conn = mysqli_connect(SQL_SERVER, SQL_USER, SQL_PASSWD, DATABASE);
  if(!$sql_conn){
    return NULL; //error
  }

  #Verify course exists
  $query  = "SELECT course_id FROM enrolled WHERE username ='".$username."'";
  $result = mysqli_query($sql_conn, $query);
  if(!mysqli_num_rows($result)){
    mysqli_close($sql_conn);
    return array(); //Not in any courses
  }
  
  $courses = array();
  while($entry = mysqli_fetch_assoc($result)){
    $course_id = $entry["course_id"];
    $courses[] = $course_id;
  }
  $courses = '('.implode(",",$courses).')';

  $query = "SELECT course_name FROM courses WHERE course_id IN ".$courses."";
  $result = mysqli_query($sql_conn, $query);
  if(!mysqli_num_rows($result)){
    mysqli_close($sql_conn);
    return NULL; //error
  }

  $courses = array();
  while($entry = mysqli_fetch_assoc($result)){
    $course_name = $entry["course_name"];
    $courses[]   = $course_name;
  }

  mysqli_close($sql_conn);
  return $courses;
}

/*
 *Add student to course in database
 *Assumes the course already exists in the courses table 
 *NOTE: Not meant for TAs
 */
function add_stud_course($username, $course_name){ 
  $sql_conn = mysqli_connect(SQL_SERVER, SQL_USER, SQL_PASSWD, DATABASE);
  if(!$sql_conn){
    return 1;
  }

  #Verify user exists

  #Verify course exists
  $query  = "SELECT course_id FROM courses WHERE course_name ='".$course_name."'";
  $result = mysqli_query($sql_conn, $query);
  if(!mysqli_num_rows($result)){
    mysqli_close($sql_conn);
    return 1;  
  }
  $entry = mysqli_fetch_assoc($result);
  $course_id = $entry["course_id"];

  $query = "INSERT IGNORE INTO enrolled (username, course_id) VALUES ( '".$username."','".$course_id."')";
  if(!mysqli_query($sql_conn, $query)){
    mysqli_close($sql_conn);
    return 1;
  }
  mysqli_close($sql_conn);
  return 0;
}

/*
 *Remove student from course in databse
 *NOTE: Not meant for TAs
 */
function rem_stud_course($username, $course_name){
  $sql_conn = mysqli_connect(SQL_SERVER, SQL_USER, SQL_PASSWD, DATABASE);
  if(!$sql_conn){
    return 1;
  }

  #Verify user exists

  #Verify course exists
  $query  = "SELECT course_id FROM courses WHERE course_name ='".$course_name."'";
  $result = mysqli_query($sql_conn, $query);
  if(!mysqli_num_rows($result)){
    mysqli_close($sql_conn);
    return 1;
  }
  $entry = mysqli_fetch_assoc($result);
  $course_id = $entry["course_id"];

  $query = "DELETE IGNORE FROM enrolled WHERE username='".$username."' AND course_id='".$course_id."'";
  if(!mysqli_query($sql_conn, $query)){
    mysqli_close($sql_conn);
    return 1;
  }
  mysqli_close($sql_conn);
  return 0;
}





######### HELPER METHODS #########
function get_course_group($course_name){
  $sql_conn = mysqli_connect(SQL_SERVER, SQL_USER, SQL_PASSWD, DATABASE);
  if(!$sql_conn){
    return 1;
  }

  #Check if course_name already exists!

  $query = "SELECT ldap_group FROM courses WHERE course_name ='".$course_name."'";
  $result = mysqli_query($sql_conn, $query);
  if(!mysqli_num_rows($result)){
    mysqli_close($sql_conn);
    return NULL;
  }
  $entry = mysqli_fetch_assoc($result);
  $ldap_group = $entry["ldap_group"];

  mysqli_close($sql_conn);
  return $ldap_group;
}
?>
