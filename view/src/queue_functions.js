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

get_queue = function(course) {
  url = "../api/queue/get_queue.php";
  var $posting = $.post( url, { course: course } );

  $posting.done(function( data ) {
    var dataString = JSON.stringify(data);
    var dataParsed = JSON.parse(dataString);
    if(dataParsed.error){
      alert("Error");
      //put a refresh or redirect or something here
    }
    alert(dataParsed); 
  });
}

open_queue = function(course){
  url = "../api/queue/open.php";
  posting = $.post( url, { course: course } );
}

close_queue = function(course){
  url = "../api/queue/close.php";
  posting = $.post( url, { course: course } );
}

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

