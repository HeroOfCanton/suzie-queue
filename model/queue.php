<?php
require 'config.php';

/*
 *Enqueue a student at the end of the queue
 */
function enq_stu($username, $course){
}

/*
 *Remove student from queue
 */
function deq_stu($username, $course){
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
 *
 */
function is_queue_open($course){
}



/*
 *
 */
function set_time_lim($course, $limit){
}

/*
 *
 */
function get_time_lim($course, $limit){
}

?>
