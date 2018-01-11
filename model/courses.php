<?php
require_once 'config.php';
require_once 'auth.php';
/**
 * Functions for courses
 * 
 */

/**
 * Returns array of all registered courses
 *
 * @return array of course names
 * @return null on fail
 */
function get_avail_courses(){
  $sql_conn = mysqli_connect(SQL_SERVER, SQL_USER, SQL_PASSWD, DATABASE);
  if(!$sql_conn){
    return NULL;
  }

  $query  = "SELECT course_name FROM courses";
  $result = mysqli_query($sql_conn, $query);
  if(!$result){
    return NULL;
  }

  $courses = array();
  while($entry = mysqli_fetch_assoc($result)){
    $courses[] = $entry["course_name"];
  }

  mysqli_close($sql_conn);
  return $courses;
}

 /**
  * Adds a new course to the database
  *
  * @param string $course_name
  * @param string $depart_prefix
  * @param string $course_num
  * @param string $description
  * @param string $ldap_group
  * @param string $professor
  * @return int 0 on success, 1 on fail
  */
function new_course($course_name, $depart_prefix, $course_num, $description, $ldap_group, $professor){
  $sql_conn = mysqli_connect(SQL_SERVER, SQL_USER, SQL_PASSWD, DATABASE);
  if(!$sql_conn){
    return 1;
  }
  
  $query = "INSERT INTO courses (depart_pref, course_num, course_name, description, ldap_group, professor)
            VALUES (?, ?, ?, ?, ?, ?)";
  $stmt  = mysqli_prepare($sql_conn, $query);
  if(!$stmt){
    mysqli_close($sql_conn);
    return 1;
  }
  mysqli_stmt_bind_param($stmt, "ssssss", $depart_prefix, $course_num, $course_name, $description, $ldap_group, $professor);
  if(!mysqli_stmt_execute($stmt)){
    mysqli_stmt_close($stmt);
    mysqli_close($sql_conn);
    return 1;
  } 

  mysqli_stmt_close($stmt);
  mysqli_close($sql_conn);
  return 0;
}

/**
 * Removes the course from the database
 *
 * @param string $course_name
 * @return int 0 on success, 1 on fail
 */
function del_course($course_name){
  $sql_conn = mysqli_connect(SQL_SERVER, SQL_USER, SQL_PASSWD, DATABASE);
  if(!$sql_conn){
    return 1;
  }

  $query = "DELETE FROM courses WHERE course_name=?";
  $stmt  = mysqli_prepare($sql_conn, $query);
  if(!$stmt){
    mysqli_close($sql_conn);
    return 1;
  }
  mysqli_stmt_bind_param($stmt, "s",$course_name);
  if(!mysqli_stmt_execute($stmt)){
    mysqli_stmt_close($stmt);
    mysqli_close($sql_conn);
    return 1;
  }

  mysqli_stmt_close($stmt);
  mysqli_close($sql_conn);
  return 0;
}

 /**
  * Returns a list of TAs for the course.
  *
  * @param string $course_name
  * @return array of TA usernames
  * @return null on fail
  */
function get_tas($course_name){
  $course_group = get_course_group($course_name);
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

 /**
  * Get courses that the user is a TA for
  *
  * @param string $username
  * @return array of courses the user is a TA for 
  * @return null on error
  */
function get_ta_courses($username){
  $result = srch_by_sam($username);
  if($result == NULL){
    return NULL;
  } 

  $sql_conn = mysqli_connect(SQL_SERVER, SQL_USER, SQL_PASSWD, DATABASE);
  if(!$sql_conn){
    return NULL;
  }
 
  $groups = $result["memberof"];
  unset($groups["count"]);

  $courses = array();
  foreach($groups as $group) { //Iterate groups the user is a member of
    $group_sam = dn_to_sam($group);
    if($group_sam == NULL){
      continue; //In theory, this is not possible, but we'll check
    }

    #group_sam is returned from LDAP, so we won't worry about SQL injection here
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

 /**
  * Get courses that the user has joined as a student
  *
  * @param string $username
  * @return array of courses the user is a student in
  * @return null on error
  */
function get_stud_courses($username){
  $sql_conn = mysqli_connect(SQL_SERVER, SQL_USER, SQL_PASSWD, DATABASE);
  if(!$sql_conn){
    return NULL;
  }

  $query = "SELECT course_name FROM courses NATURAL JOIN enrolled WHERE username=?";
  $stmt  = mysqli_prepare($sql_conn, $query);
  if(!$stmt){
    mysqli_close($sql_conn);
    return NULL;
  }
  mysqli_stmt_bind_param($stmt, "s", $username);
  if(!mysqli_stmt_execute($stmt)){
    mysqli_stmt_close($stmt);
    mysqli_close($sql_conn);   
    return NULL;
  } 
  mysqli_stmt_bind_result($stmt, $course_name);
  
  $courses = array();
  while(mysqli_stmt_fetch($stmt)){
    $courses[] = $course_name;
  }

  mysqli_stmt_close($stmt);
  mysqli_close($sql_conn);
  return $courses;
}

 /**
  * Add user to course as a student
  *
  * @param string $username
  * @param string $course_name
  * @return int 0 on success, 1 on fail
  */
function add_stud_course($username, $course_name){ 
  $sql_conn = mysqli_connect(SQL_SERVER, SQL_USER, SQL_PASSWD, DATABASE);
  if(!$sql_conn){
    return 1;
  }

  $query = "REPLACE enrolled (username, course_id) VALUES ( ?, (SELECT course_id FROM courses WHERE course_name=?) )";
  $stmt  = mysqli_prepare($sql_conn, $query);
  if(!$stmt){
    mysqli_close($sql_conn);
    return 1;
  }
  mysqli_stmt_bind_param($stmt, "ss", $username, $course_name);
  if(!mysqli_stmt_execute($stmt)){
    mysqli_stmt_close($stmt);
    mysqli_close($sql_conn);
    return 1;
  }

  mysqli_stmt_close($stmt);
  mysqli_close($sql_conn);
  return 0;
}

 /**
  * Remove user (student) from course 
  *
  * @param string $username
  * @param string $course_name
  * @return int 0 on success, 1 on fail
  */
function rem_stud_course($username, $course_name){
  $sql_conn = mysqli_connect(SQL_SERVER, SQL_USER, SQL_PASSWD, DATABASE);
  if(!$sql_conn){
    return 1;
  }

  $query = "DELETE enrolled FROM enrolled NATURAL JOIN courses WHERE username=? AND course_name=?";
  $stmt  = mysqli_prepare($sql_conn, $query);
  if(!$stmt){
    mysqli_close($sql_conn);
    return 1;
  }
  mysqli_stmt_bind_param($stmt, "ss", $username, $course_name);
  if(!mysqli_stmt_execute($stmt)){
    mysqli_stmt_close($stmt);
    mysqli_close($sql_conn);
    return 1;
  }

  mysqli_stmt_close($stmt);
  mysqli_close($sql_conn);
  return 0;
}


######### HELPER METHODS #########
/**
 * Returns the LDAP group for the course
 *
 * @param int $course_name
 * @return string ldap group
 * @return null on error
 */
function get_course_group($course_name){
  $sql_conn = mysqli_connect(SQL_SERVER, SQL_USER, SQL_PASSWD, DATABASE);
  if(!$sql_conn){
    return NULL;
  }

  $query = "SELECT ldap_group FROM courses WHERE course_name=?";
  $stmt  = mysqli_prepare($sql_conn, $query);
  if(!$stmt){
    mysqli_close($sql_conn);
    return NULL;
  }
  mysqli_stmt_bind_param($stmt, "s", $course_name);
  if(!mysqli_stmt_execute($stmt)){
    mysqli_stmt_close($stmt);
    mysqli_close($sql_conn);
    return NULL;
  }
  mysqli_stmt_bind_result($stmt, $ldap_group);
  mysqli_stmt_fetch($stmt);

  mysqli_stmt_close($stmt);
  mysqli_close($sql_conn);
  return $ldap_group;
}
?>
