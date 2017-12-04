<?php
require_once 'config.php';


/*
 *Returns the state of the queue
 *This function will be called numerous times per minute
 *This function returns all information in the queue
 *  It's up to the controller to implement access control.
 */
function get_queue($course){
  $sql_conn = mysqli_connect(SQL_SERVER, SQL_USER, SQL_PASSWD, DATABASE);
  if(!$sql_conn){
    return NULL;
  }

  $course_id = course_name_to_id($course, $sql_conn);
  if($course_id == NULL){
    mysqli_close($sql_conn);
    return NULL;
  } 

  #Build return array
  $return = array();

  #Get the state of the queue
  $query  = "SELECT * FROM queue_state WHERE course_id ='".$course_id."'";
  $result = mysqli_query($sql_conn, $query);
  if(!mysqli_num_rows($result)){
    mysqli_close($sql_conn);
    return NULL;
  }
  $entry    = mysqli_fetch_assoc($result);
  $return["state"]    = $entry["state"];
  $return["time_lim"] = $entry["time_lim"];

  #Get the state of the TAs
  $query  = "SELECT * FROM ta_status WHERE course_id ='".$course_id."'";
  $result = mysqli_query($sql_conn, $query);
  while($entry = mysqli_fetch_assoc($result)){
    $return["TAs"][] = $entry;
  }

  #Get the actual queue
  $query  = "SELECT * FROM queue WHERE course_id ='".$course_id."' ORDER BY position";
  $result = mysqli_query($sql_conn, $query);
  while($entry = mysqli_fetch_assoc($result)){
    $return["queue"][] = $entry;
  }

  mysqli_close($sql_conn);
  return $return;
}





/*
 *Enqueue a student at the end of the queue
 *NEED TO FIX THIS TO PREVENT A STUDENT FROM BEING IN THE SAME QUEUE MULTIPLE TIMES
 */
function enq_stu($username, $course, $question, $location){
  #VERIFY INPUT

  $sql_conn = mysqli_connect(SQL_SERVER, SQL_USER, SQL_PASSWD, DATABASE);
  if(!$sql_conn){
    return 1;
  }

  $course_id = course_name_to_id($course, $sql_conn);
  if($course_id == NULL){
    mysqli_close($sql_conn);
    return 1;
  }

  if(get_queue_state($course) != "open"){
    return 1;
  }  

  $query = "INSERT INTO queue (username, course_id, question, location) VALUES ('".$username."','".$course_id."','".$question."','".$location."')";
  if(!mysqli_query($sql_conn, $query)){
    mysqli_close($sql_conn);
    return 1;
  }
  mysqli_close($sql_conn);
  return 0;
}

/*
 *Remove student from queue
 *If a TA is helping this student, SQL will free the TA. 
 */
function deq_stu($username, $course){
  $sql_conn = mysqli_connect(SQL_SERVER, SQL_USER, SQL_PASSWD, DATABASE);
  if(!$sql_conn){
    return 1;
  }

  $course_id = course_name_to_id($course, $sql_conn);
  if($course_id == NULL){
    mysqli_close($sql_conn);
    return 1;
  }

  $query = "DELETE IGNORE FROM queue WHERE username='".$username."' AND course_id='".$course_id."'";
  if(!mysqli_query($sql_conn, $query)){
    mysqli_close($sql_conn);
    return 1;
  }
  mysqli_close($sql_conn);
  return 0;
}





/*
 *Puts the TA on duty
 *Up to the controller to verify that $username is a TA for $course
 */
function enq_ta($username, $course){
  $sql_conn = mysqli_connect(SQL_SERVER, SQL_USER, SQL_PASSWD, DATABASE);
  if(!$sql_conn){
    return 1;
  }

  if(get_queue_state($course) != "open"){
    return 1;
  }

  $course_id = course_name_to_id($course, $sql_conn);
  if($course_id == NULL){
    mysqli_close($sql_conn);
    return 1;
  }

  $query = "INSERT INTO ta_status (username, course_id) VALUES ('".$username."','".$course_id."')";
  if(!mysqli_query($sql_conn, $query)){
    mysqli_close($sql_conn);
    return 1;
  }

  mysqli_close($sql_conn);
  return 0;
}

/*
 *Removes to TA from duty
 */
function deq_ta($username, $course){
  $sql_conn = mysqli_connect(SQL_SERVER, SQL_USER, SQL_PASSWD, DATABASE);
  if(!$sql_conn){
    return 1;
  }

  if(get_queue_state($course) != "open"){
    return 1;
  }

  $course_id = course_name_to_id($course, $sql_conn);
  if($course_id == NULL){
    mysqli_close($sql_conn);
    return 1;
  }

  $query = "DELETE IGNORE FROM ta_status WHERE username='".$username."' AND course_id='".$course_id."'"; 
  if(!mysqli_query($sql_conn, $query)){
    mysqli_close($sql_conn);
    return 1;
  }

  mysqli_close($sql_conn);
  return 0;

}

function get_ta_status($username, $course){
  $sql_conn = mysqli_connect(SQL_SERVER, SQL_USER, SQL_PASSWD, DATABASE);
  if(!$sql_conn){
    return 1;
  }

  if(get_queue_state($course) != "open"){
    return 1;
  }

  $course_id = course_name_to_id($course, $sql_conn);
  if($course_id == NULL){
    mysqli_close($sql_conn);
    return 1;
  }

  $query  = "SELECT * FROM ta_status WHERE username='".$username."' AND course_id='".$course_id."'";
  $result = mysqli_query($sql_conn, $query);
  if(!mysqli_num_rows($result)){
    mysqli_close($sql_conn);
    return array("dequeue");
  }
  $entry = mysqli_fetch_assoc($result);

  $return = array(
    "enqueue",
    "helping" => $entry["helping"]
  );

  mysqli_close($sql_conn);
  return $return;
}

/*
 *Sets the TA status to helping the next person in the queue.
 *Call deq_stud() before calling this again, or you'll just get the same person.
 */
function help_next_student($username, $course){
  $sql_conn = mysqli_connect(SQL_SERVER, SQL_USER, SQL_PASSWD, DATABASE);
  if(!$sql_conn){
    return 1;
  }

  if(get_queue_state($course) != "open"){
    mysqli_close($sql_conn);
    return 1;
  }

  if(get_ta_status($username, $course)[0] != "enqueue"){
    mysqli_close($sql_conn);
    return 1;
  }

  $course_id = course_name_to_id($course, $sql_conn);
  if($course_id == NULL){
    mysqli_close($sql_conn);
    return 1;
  }

  $query  = "SELECT position FROM queue WHERE course_id ='".$course_id."' ORDER BY position";
  $result = mysqli_query($sql_conn, $query);  
  if(!mysqli_num_rows($result)){
    $position = NULL; //Nobody left to help
  }else{
    $position = mysqli_fetch_assoc($result)["position"];
  }
  
  $query = "REPLACE INTO ta_status (username, course_id, helping) VALUES ('".$username."','".$course_id."','".$position."')";
  if(!mysqli_query($sql_conn, $query)){
    mysqli_close($sql_conn);
    return 1;
  }
  
  mysqli_close($sql_conn);
  return 0;  
}

/*
 *Keeps the TA on duty in the queue
 *Not helping anyone though
 */
function set_free_ta($username, $course){
 $sql_conn = mysqli_connect(SQL_SERVER, SQL_USER, SQL_PASSWD, DATABASE);
  if(!$sql_conn){
    return 1;
  }

  if(get_queue_state($course) != "open"){
    mysqli_close($sql_conn);
    return 1;
  }

  if(get_ta_status($username, $course)[0] != "enqueue"){
    mysqli_close($sql_conn);
    return 1;
  }

  $course_id = course_name_to_id($course, $sql_conn);
  if($course_id == NULL){
    mysqli_close($sql_conn);
    return 1;
  }
  
  $query = "UPDATE ta_status SET helping = NULL WHERE username = '".$username."' AND course_id = '".$course_id."'";
  if(!mysqli_query($sql_conn, $query)){
    mysqli_close($sql_conn);
    return 1;
  }

  mysqli_close($sql_conn);
  return 0;
}




/*
 *Returns the state of the queue
 *"open", "closed", "paused"
 */
function get_queue_state($course){
  return change_queue_state($course, NULL);
}

function open_queue($course){
  return change_queue_state($course, "open");
}

function close_queue($course){
 return change_queue_state($course, "closed");
}

function pause_queue($course){
  return change_queue_state($course, "paused");
}




//HELPER FUNCTIONS

function change_queue_state($course, $state){
  $sql_conn = mysqli_connect(SQL_SERVER, SQL_USER, SQL_PASSWD, DATABASE);
  if(!$sql_conn){
    return NULL;
  }

  $course_id = course_name_to_id($course, $sql_conn);
  if($course_id == NULL){
    mysqli_close($sql_conn);
    return NULL;
  }

  if($state != NULL){
    $query = "UPDATE queue_state SET state='".$state."' WHERE course_id ='".$course_id."'";
    if(!mysqli_query($sql_conn, $query)){
      mysqli_close($sql_conn);
      return NULL;
    }

    if($state == "closed"){
      if(tear_down_queue($course_id, $sql_conn)){
        mysqli_close($sql_conn);
        return NULL;
      }
    }
  }else{//Just querying the state of the queue if $state==NULL
    $query  = "SELECT state FROM queue_state WHERE course_id ='".$course_id."'";
    $result = mysqli_query($sql_conn, $query);
    if(!mysqli_num_rows($result)){
      mysqli_close($sql_conn);
      return NULL;
    }
    $entry = mysqli_fetch_assoc($result);
    $state = $entry["state"];
  }

  mysqli_close($sql_conn);
  return $state;
}

/*
 *Tears down a queue in the appropriate order
 *
 */
function tear_down_queue($course_id, $sql_conn){
  if(!$sql_conn){
    return 1;
  }

  $query = "DELETE IGNORE FROM ta_status WHERE course_id='".$course_id."'";
  if(!mysqli_query($sql_conn, $query)){
    mysqli_close($sql_conn);
    return 1;
  }

  $query = "DELETE IGNORE FROM queue WHERE course_id='".$course_id."'";
  if(!mysqli_query($sql_conn, $query)){
    mysqli_close($sql_conn);
    return 1;
  }
  return 0;
}


function course_name_to_id($course, $sql_conn){
  if(!$sql_conn){
    return NULL;
  }

  $query  = "SELECT course_id FROM courses WHERE course_name ='".$course."'";
  $result = mysqli_query($sql_conn, $query);
  if(!mysqli_num_rows($result)){
    return NULL;
  }
  $entry = mysqli_fetch_assoc($result);
  $course_id = $entry["course_id"];

  return $course_id;
}


?>
