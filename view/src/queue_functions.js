//GET parsing snippet from CHRIS COYIER
var query = window.location.search.substring(1);
var vars = query.split("&");
for (var i=0;i<vars.length;i++) {
  var pair = vars[i].split("=");
  if(pair[0] == "course"){
    course = decodeURIComponent(pair[1]);
    break;
  }
}
get_queue(course);
is_TA = false;

function get_queue(course) {
  var url = "../api/queue/get_queue.php";
  var posting = $.post( url, { course: course } );
 
  var done = function(data) {
    var dataString = JSON.stringify(data);
    var dataParsed = JSON.parse(dataString);
    if(dataParsed.error){
      alert("Error fetching queue");a
      return;
    }
    
    queue_state = dataParsed.state;
    if(is_TA){
      if(queue_state == "closed"){
        $("#state_button").text("OPEN QUEUE");
        $("#state_button").click(function( event ) {
          event.preventDefault();
          open_queue(course);
        });
      }else{
        $("#state_button").text("CLOSE QUEUE");
        $("#state_button").click(function( event ) {
          event.preventDefault();
          close_queue(course);
        });
      }
    }
      
  }

  posting.done(done);
}

function open_queue(course){
  url = "../api/queue/open.php";
  posting = $.post( url, { course: course } );
}

function close_queue(course){
  url = "../api/queue/close.php";
  posting = $.post( url, { course: course } );
}
/*
enqueue_student = function(){
  url = "../api/queue/enqueue_student.php";
}

dequeue_student = function(){
  url = "../api/queue/dequeue_student.php";
}

enqueue_ta = function(){
  url = "../api/queue/enqueue_ta.php";
}

dequeue_ta = function(){
  url = "../api/queue/dequeue_ta.php";
}

next_student = function(){
  url = "../api/queue/next_student.php";
}
*/



