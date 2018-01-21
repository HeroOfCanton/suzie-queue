get_queue = function() {
  
  url = "../api/queue/get_queue.php";
  var $posting = $.post( url, { course: "CS 4400: Computer Systems" } );

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

