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

  #Verify course exists
  $query  = "SELECT course_id FROM courses WHERE course_name ='".$course."'";
  $result = mysqli_query($sql_conn, $query);
  if(!mysqli_num_rows($result)){
    mysqli_close($sql_conn);
    return NULL;
  }
  $entry = mysqli_fetch_assoc($result);
  $course_id = $entry["course_id"];

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
 */
function enq_stu($username, $course, $question, $location){
  #VERIFY INPUT

  $sql_conn = mysqli_connect(SQL_SERVER, SQL_USER, SQL_PASSWD, DATABASE);
  if(!$sql_conn){
    return 1;
  }

  #Verify course exists
  $query  = "SELECT course_id FROM courses WHERE course_name ='".$course."'";
  $result = mysqli_query($sql_conn, $query);
  if(!mysqli_num_rows($result)){
    mysqli_close($sql_conn);
    return 1;
  }
  $entry = mysqli_fetch_assoc($result);
  $course_id = $entry["course_id"];

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
 */
function deq_stu($username, $course){
  $sql_conn = mysqli_connect(SQL_SERVER, SQL_USER, SQL_PASSWD, DATABASE);
  if(!$sql_conn){
    return 1;
  }

  #Verify course exists
  $query  = "SELECT course_id FROM courses WHERE course_name ='".$course."'";
  $result = mysqli_query($sql_conn, $query);
  if(!mysqli_num_rows($result)){
    mysqli_close($sql_conn);
    return NULL;
  }
  $entry = mysqli_fetch_assoc($result);
  $course_id = $entry["course_id"];

  $query = "DELETE IGNORE FROM queue WHERE username='".$username."' AND course_id='".$course_id."'";
  if(!mysqli_query($sql_conn, $query)){
    mysqli_close($sql_conn);
    return 1;
  }
  mysqli_close($sql_conn);
  return 0;
}

/*
 *Enqueue student at position in queue
 *Position must be >0
 *If position is > (length -1), student is placed at end of queue
 */
function place_stu($username, $position, $course){
}

/*
 *
 */
function pause_stu($username, $course){
}



/*
 *
 */
function enq_ta($username, $course){
}

/*
 *
 */
function deq_ta($username, $course){
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



/*
 *
 */
function set_time_lim($course, $limit){
}

/*
 *
 */
function get_time_lim($course){
}



//HELPER FUNCTIONS
function change_queue_state($course, $state){
  $sql_conn = mysqli_connect(SQL_SERVER, SQL_USER, SQL_PASSWD, DATABASE);
  if(!$sql_conn){
    return NULL;
  }

  #Verify course exists
  $query  = "SELECT course_id FROM courses WHERE course_name ='".$course."'";
  $result = mysqli_query($sql_conn, $query);
  if(!mysqli_num_rows($result)){
    mysqli_close($sql_conn);
    return NULL;
  }
  $entry = mysqli_fetch_assoc($result);
  $course_id = $entry["course_id"];

  if($state != NULL){
    $query = "UPDATE queue_state SET state='".$state."' WHERE course_id ='".$course_id."'";
    if(!mysqli_query($sql_conn, $query)){
      mysqli_close($sql_conn);
      return NULL;
    }

    if($state == "closed"){
      $query = "DELETE IGNORE FROM queue WHERE course_id='".$course_id."'";
      if(!mysqli_query($sql_conn, $query)){
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
?>
