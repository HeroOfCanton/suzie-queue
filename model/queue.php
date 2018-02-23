<?php
require_once 'config.php';
/**
 * 
 */

/**
 * Returns the state of the queue
 *
 * @param string $course
 * @return array
           NULL on error
 */
function get_queue($course){
  $sql_conn = mysqli_connect(SQL_SERVER, SQL_USER, SQL_PASSWD, DATABASE);
  if(!$sql_conn){
    return NULL;
  }

  $course_id = course_name_to_id($course, $sql_conn);
  if(is_null($course_id)){
    mysqli_close($sql_conn);
    return NULL;
  } 

  #Build return array
  $return = array();

  #Get the state of the queue, if its not here, it must be closed
  $query  = "SELECT * FROM queue_state WHERE course_id ='".$course_id."'";
  $result = mysqli_query($sql_conn, $query);
  if(!mysqli_num_rows($result)){
    mysqli_close($sql_conn);
    $return["state"] = "closed";
    return $return;
  }
  $entry    = mysqli_fetch_assoc($result);
  $return["state"]    = $entry["state"];
  $return["time_lim"] = $entry["time_lim"];
  $return["announce"] = [];
  $return["TAs"]      = [];

  #Get the announcements
  $query  = "SELECT announcement, tmstmp FROM announcements WHERE course_id ='".$course_id."' ORDER BY id";
  $result = mysqli_query($sql_conn, $query);
  while($entry = mysqli_fetch_assoc($result)){
    $return["announce"][] = $entry;
  }

  #Get the state of the TAs
  $query  = "SELECT ta_status.username, (SELECT TIMEDIFF(NOW(), ta_status.state_tmstmp)) as duration, users.full_name, (SELECT username FROM queue WHERE position=helping LIMIT 1) as helping 
             FROM ta_status INNER JOIN users on ta_status.username = users.username 
             WHERE course_id='".$course_id."'";
  $result = mysqli_query($sql_conn, $query);
  while($entry = mysqli_fetch_assoc($result)){
    $return["TAs"][] = $entry;
  }

  #Get the actual queue
  $query  = "SELECT queue.username, users.full_name, queue.question, queue.location 
             FROM queue INNER JOIN users on queue.username = users.username
             WHERE course_id ='".$course_id."' ORDER BY position";
  $result = mysqli_query($sql_conn, $query);
  while($entry = mysqli_fetch_assoc($result)){
    $return["queue"][] = $entry;
  }

  mysqli_close($sql_conn);
  return $return;
}

/**
 * Returns the length of the queue
 *
 * @param string $course_name
 * @return int length of queue
 *             -1 on error
 */
function get_queue_length($course_name){
  $sql_conn = mysqli_connect(SQL_SERVER, SQL_USER, SQL_PASSWD, DATABASE);
  if(!$sql_conn){
    return -1;
  }

  $query = "SELECT * FROM queue NATURAL JOIN courses WHERE course_name=?";
  $stmt  = mysqli_prepare($sql_conn, $query);
  if(!$stmt){
    mysqli_close($sql_conn);
    return -1;
  }
  mysqli_stmt_bind_param($stmt, "s", $course_name);
  if(!mysqli_stmt_execute($stmt)){
    mysqli_stmt_close($stmt);
    mysqli_close($sql_conn);
    return -1;
  }
  mysqli_stmt_store_result($stmt);
  return mysqli_stmt_num_rows($stmt);
}

/**
 * Adds student to queue
 *
 * @param string $username
 * @param string $course_name
 * @param string $question
 * @param string $location
 * @return int 0 on success, 1 on fail
 */
function enq_stu($username, $course_name, $question, $location){
  $sql_conn = mysqli_connect(SQL_SERVER, SQL_USER, SQL_PASSWD, DATABASE);
  if(!$sql_conn){
    return 1;
  }

  if(get_queue_state($course_name) != "open"){
    mysqli_close($sql_conn);
    return 1;
  }  

  $query = "INSERT INTO queue (username, course_id, question, location) 
            VALUES (?, (SELECT course_id FROM courses WHERE course_name=?), ?, ?) 
            ON DUPLICATE KEY UPDATE question=?";
  $stmt  = mysqli_prepare($sql_conn, $query);
  if(!$stmt){
    mysqli_close($sql_conn);
    return 1;
  }
  mysqli_stmt_bind_param($stmt, "sssss", $username, $course_name, $question, $location, $question);
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
 * Remove student from queue
 *
 * If a TA is helping this student, SQL will free the TA.
 * 
 * @param string $username
 * @param string $course
 * @return 0 on success, 1 on fail
 */
function deq_stu($username, $course_name){
  $sql_conn = mysqli_connect(SQL_SERVER, SQL_USER, SQL_PASSWD, DATABASE);
  if(!$sql_conn){
    return 1;
  }

  $query = "DELETE queue from queue NATURAL JOIN courses 
            WHERE username=? AND course_name=?";
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
 * Add TA to queue
 *
 * @param string $username
 * @param string $course_name
 * @return 0 onsuccess, 1 on fail
 */
function enq_ta($username, $course_name){
  $sql_conn = mysqli_connect(SQL_SERVER, SQL_USER, SQL_PASSWD, DATABASE);
  if(!$sql_conn){
    return 1;
  }

  $query = "INSERT INTO ta_status (username, course_id) 
            VALUES (?, (SELECT course_id FROM courses WHERE course_name=?) )
            ON DUPLICATE KEY UPDATE username=?";
  $stmt  = mysqli_prepare($sql_conn, $query);
  if(!$stmt){
    mysqli_close($sql_conn);
    return 1;
  }
  mysqli_stmt_bind_param($stmt, "sss", $username, $course_name, $username);
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
 * Remove TA from queue
 *
 * @param string $username
 * @param string $course_name
 * @return 0 on success, 1 on fail
 */
function deq_ta($username, $course_name){
  $sql_conn = mysqli_connect(SQL_SERVER, SQL_USER, SQL_PASSWD, DATABASE);
  if(!$sql_conn){
    return 1;
  }

  $query = "DELETE FROM ta_status 
            WHERE username=? 
            AND course_id=(SELECT course_id FROM courses WHERE course_name=?)"; 
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
 * Gets the status of the TA for the course
 *
 * @param string $username
 * @param string $course_name
 * @return int -1 on error
 *              1 if TA not on duty
 *              2 if on duty, but not helping anyone
 *              3 if on duty, and helping someone
 */
function get_ta_status($username, $course_name){
  $sql_conn = mysqli_connect(SQL_SERVER, SQL_USER, SQL_PASSWD, DATABASE);
  if(!$sql_conn){
    return -1;
  }

  $query  = "SELECT helping FROM ta_status 
             WHERE username=? 
             AND course_id=(SELECT course_id FROM courses WHERE course_name=?)";
  
  $stmt  = mysqli_prepare($sql_conn, $query);
  if(!$stmt){
    mysqli_close($sql_conn);
    return -1;
  }
  mysqli_stmt_bind_param($stmt, "ss", $username, $course_name);
  if(!mysqli_stmt_execute($stmt)){
    mysqli_stmt_close($stmt);
    mysqli_close($sql_conn);
    return -1;
  }
  mysqli_stmt_bind_result($stmt, $helping);
  if(mysqli_stmt_fetch($stmt) == NULL){
    mysqli_stmt_close($stmt);
    mysqli_close($sql_conn);
    return 1;
  }

  mysqli_stmt_close($stmt);
  mysqli_close($sql_conn);

  if($helping == NULL){
    return 2;
  }
  return 3;
}

/**
 * Sets the TA status to helping the next person in the queue.
 * Call deq_stu() before calling this again
 *
 * @param string $username
 * @param string $course_name
 * @return int 0 on success, 1 on fail
 */
function help_next_student($username, $course_name){
  $sql_conn = mysqli_connect(SQL_SERVER, SQL_USER, SQL_PASSWD, DATABASE);
  if(!$sql_conn){
    return 1;
  }

  if(get_queue_state($course_name) != "open"){
    mysqli_close($sql_conn);
    return 1;
  }

  if(get_ta_status($username, $course_name) < 2){ 
    mysqli_close($sql_conn);
    return 1;
  }

  $course_id = course_name_to_id($course_name, $sql_conn);
  if(is_null($course_id)){
    mysqli_close($sql_conn);
    return 1;
  }
  
  $query = "SELECT position FROM queue
            WHERE course_id ='".$course_id."'
            AND position NOT IN (SELECT helping FROM ta_status WHERE helping IS NOT NULL AND course_id='".$course_id."')
            ORDER BY position LIMIT 1";
  $result = mysqli_query($sql_conn, $query);
  $position = mysqli_fetch_assoc($result)['position'];

  $query = "REPLACE INTO ta_status (username, course_id, helping) VALUES (?,?,?)"; 
  $stmt  = mysqli_prepare($sql_conn, $query);
  if(!$stmt){
    mysqli_close($sql_conn);
    return 1;
  }
  mysqli_stmt_bind_param($stmt, "sis", $username, $course_id, $position);
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
 * Help particular student in queue
 *
 * @param string $TA_username
 * @param string $stud_username
 * @param string $course
 * @return int 0 on success, 1 on fail
 */
function help_student($TA_username, $stud_username, $course_name){
 $sql_conn = mysqli_connect(SQL_SERVER, SQL_USER, SQL_PASSWD, DATABASE);
  if(!$sql_conn){
    return 1;
  }

  if(get_queue_state($course_name) == "closed"){
    mysqli_close($sql_conn);
    return 1;
  }

  if(get_ta_status($TA_username, $course_name) < 2){
    mysqli_close($sql_conn);
    return 1;
  }

  $course_id = course_name_to_id($course_name, $sql_conn);
  if(is_null($course_id)){
    mysqli_close($sql_conn);
    return 1;
  } 

  $query = "REPLACE INTO ta_status (username, course_id, helping)
            VALUES (?,?, (SELECT position FROM queue WHERE username=? AND course_id=?))";
  $stmt  = mysqli_prepare($sql_conn, $query);
  if(!$stmt){
    mysqli_close($sql_conn);
    return 1;
  }
  mysqli_stmt_bind_param($stmt, "sisi", $TA_username, $course_id, $stud_username, $course_id);
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
 * Sets the TA free from helping anyone,
 * but does NOT dequeue a student they could be helping
 *
 * Note that dequeuing the student the TA is helping frees the TA automatically.
 * 
 * @param string $username
 * @param string $course_name
 * @return 0 on success, 1 on fail
 */
function free_ta($username, $course_name){
  $sql_conn = mysqli_connect(SQL_SERVER, SQL_USER, SQL_PASSWD, DATABASE);
  if(!$sql_conn){
    return 1;
  }

  $query = "UPDATE ta_status SET helping = NULL 
            WHERE username=? 
            AND course_id=(SELECT course_id FROM courses WHERE course_name=?)";
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
 * Set the time limit for the queue or 0 for no limit.
 *
 * @param string $time_lim in minutes
 * @param string $course_name
 * @return 0 on success, 1 on sql fail, 2 on invalid course/closed course
 */
function set_time_lim($time_lim, $course_name){
  $sql_conn = mysqli_connect(SQL_SERVER, SQL_USER, SQL_PASSWD, DATABASE);
  if(!$sql_conn){
    return 1;
  }

  $query = "UPDATE queue_state SET time_lim = ? 
            WHERE course_id=(SELECT course_id FROM courses WHERE course_name=?)";
  $stmt  = mysqli_prepare($sql_conn, $query);
  if(!$stmt){
    mysqli_close($sql_conn);
    return 1;
  }
  mysqli_stmt_bind_param($stmt, "is", $time_lim, $course_name);
  if(!mysqli_stmt_execute($stmt)){
    mysqli_stmt_close($stmt);
    mysqli_close($sql_conn);
    return 1;
  }

  if(!mysqli_stmt_affected_rows($stmt)){
    mysqli_stmt_close($stmt);
    mysqli_close($sql_conn);
    return 2;
  }

  mysqli_stmt_close($stmt);
  mysqli_close($sql_conn);
  return 0;
}

/**
 * Undocumented function
 *
 * @param [type] $stud_username
 * @param [type] $course
 * @return void
 */
function increase_stud_priority($stud_username, $course_name){
  return change_stud_priority($stud_username, $course_name, "increase");
}

/**
 * Undocumented function
 *
 * @param [type] $stud_username
 * @param [type] $course
 * @return void
 */
function decrease_stud_priority($stud_username, $course_name){
  return change_stud_priority($stud_username, $course_name, "decrease");
}

/**
 * Get the state of the queue
 *
 * @param string $course_name
 * @return void
 */
function get_queue_state($course_name){
  return change_queue_state($course_name, NULL);
}

/**
 * Open the queue
 *
 * @param string $course_name
 * @return void
 */
function open_queue($course_name){
  return change_queue_state($course_name, "open");
}

/**
 * Close the queue
 *
 * @param string $course_name
 * @return void
 */
function close_queue($course_name){
  return change_queue_state($course_name, "closed");
}

/**
 * Freeze the queue
 *
 * @param string $course_name
 * @return void
 */
function freeze_queue($course_name){
  return change_queue_state($course_name, "frozen");
}


//HELPER FUNCTIONS
/**
 * Changes the state of the course queue
 * 
 * I'd like to move the input and output states
 * from strings to ints
 *
 * @param string $course_name
 * @param string $state
 * @return string $state of queue
 * @return NULL on error
 */
function change_queue_state($course_name, $state){
  $sql_conn = mysqli_connect(SQL_SERVER, SQL_USER, SQL_PASSWD, DATABASE);
  if(!$sql_conn){
    return NULL;
  }

  $course_id = course_name_to_id($course_name, $sql_conn);
  if(is_null($course_id)){
    mysqli_close($sql_conn);
    return NULL;
  }

  if($state == "closed"){ //By deleting the entry in queue_state, we cascade the other entries
    $query = "DELETE FROM queue_state WHERE course_id='".$course_id."'";
    if(!mysqli_query($sql_conn, $query)){
      mysqli_close($sql_conn);
      return NULL;
    }
  }elseif($state == "frozen"){ //Since REPLACE calls DELETE then INSERT, calling REPLACE would CASCADE all other tables, we use ON DUPLICATE KEY UPDATE instead
    $query = "INSERT INTO queue_state (course_id, state) VALUES ('".$course_id."','frozen') ON DUPLICATE KEY UPDATE state='frozen'";
    if(!mysqli_query($sql_conn, $query)){
      mysqli_close($sql_conn);
      return NULL;
    }
  }elseif($state == "open"){
    $query = "INSERT INTO queue_state (course_id, state) VALUES ('".$course_id."','open') ON DUPLICATE KEY UPDATE state='open'";
    if(!mysqli_query($sql_conn, $query)){
      mysqli_close($sql_conn);
      return NULL;
    }
  }else{//Just querying the state of the queue if $state==NULL
    $query  = "SELECT state FROM queue_state WHERE course_id ='".$course_id."'";
    $result = mysqli_query($sql_conn, $query);
    if(!mysqli_num_rows($result)){
      mysqli_close($sql_conn);
      return "closed";
    }
    $entry = mysqli_fetch_assoc($result);
    $state = $entry["state"];
  }

  mysqli_close($sql_conn);
  return $state;
}

/**
 * Converts the name of a course to the course ID used in SQL
 * 
 * I'd like to eventually get rid of this function in favor of 
 * just embedding subqueries or doing table joins
 * 
 * For now though, this function is used mainly to prevent
 * SQL injection in the functions that call it, by verifying
 * course_name input
 *
 * @param string $course_name
 * @param sql_conn $sql_conn
 * @return int course_id used in SQL
 * @return null on error
 */
function course_name_to_id($course_name, $sql_conn){
  if(!$sql_conn){
    return NULL;
  }

  $query = "SELECT course_id FROM courses WHERE course_name=?";
  $stmt  = mysqli_prepare($sql_conn, $query);
  if(!$stmt){
    return NULL;
  }
  mysqli_stmt_bind_param($stmt, "s", $course_name);
  if(!mysqli_stmt_execute($stmt)){
    mysqli_stmt_close($stmt);
    return NULL;
  }
  mysqli_stmt_bind_result($stmt, $course_id);
  mysqli_stmt_fetch($stmt);

  mysqli_stmt_close($stmt);
  return $course_id;
}

/**
 * Undocumented function
 *
 * @param [type] $stud_username
 * @param [type] $course
 * @return void
 */
function change_stud_priority($stud_username, $course_name, $operation){
  $sql_conn = mysqli_connect(SQL_SERVER, SQL_USER, SQL_PASSWD, DATABASE);
  if(!$sql_conn){
    return 1;
  }

  $query = "SELECT position, username, course_id, question, location FROM queue WHERE username=? AND course_id=(SELECT course_id from courses where course_name=?)";
  $stmt  = mysqli_prepare($sql_conn, $query);
  if(!$stmt){
    return 1;
  }
  mysqli_stmt_bind_param($stmt, "ss", $stud_username, $course_name);
  if(!mysqli_stmt_execute($stmt)){
    mysqli_stmt_close($stmt);
    return 1;
  }
  mysqli_stmt_bind_result($stmt, $position1, $username1, $course_id, $question1, $location1);
  mysqli_stmt_fetch($stmt);
  mysqli_stmt_close($stmt);


  if($operation == "increase"){
    $query = "SELECT position, username, course_id, question, location FROM queue 
              WHERE position<'".$position1."' AND course_id='".$course_id."' AND position NOT IN (SELECT helping FROM ta_status WHERE helping IS NOT NULL AND course_id='".$course_id."') 
              ORDER BY position DESC LIMIT 1";
  }elseif($operation == "decrease"){
    $query = "SELECT position, username, course_id, question, location FROM queue 
              WHERE position>'".$position1."' AND course_id='".$course_id."' AND position NOT IN (SELECT helping FROM ta_status WHERE helping IS NOT NULL AND course_id='".$course_id."') 
              ORDER BY position ASC LIMIT 1";
  }else{
    return 1;
  }
  $result = mysqli_query($sql_conn, $query);
  $entry = mysqli_fetch_assoc($result);
  if(!$entry){
    return 1;
  }

  $position2 = $entry['position'];
  $username2 = $entry['username'];
  $question2 = $entry['question'];
  $location2 = $entry['location'];

  $query = "DELETE FROM queue WHERE position = '".$position1."'";
  $result = mysqli_query($sql_conn, $query);
  $query = "DELETE FROM queue WHERE position = '".$position2."'";
  $result = mysqli_query($sql_conn, $query);

  $query = "INSERT INTO queue (position, username, course_id, question, location) 
            VALUES ('".$position2."', '".$username1."', '".$course_id."', '".$question1."', '".$location1."')";
  mysqli_query($sql_conn, $query);
  $query = "INSERT INTO queue (position, username, course_id, question, location) 
            VALUES ('".$position1."', '".$username2."', '".$course_id."', '".$question2."', '".$location2."');";
  mysqli_query($sql_conn, $query);

  return 0;
}

?>
