$(document).ready(function(){
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
  is_TA = false;
  $("#title").text(course+' Queue');
  get_info();
  get_queue(course); //we should make this call synchronous
  setInterval(function(){get_queue(course);}, 5000);
});

function get_queue(course) {
  $("#queue tr").remove();
  $("#ta_on_duty h4").remove();
  $("#state_button").hide();
  $("#join_button").unbind("click");
  $("#join_button").hide();
  $("#join_button").unbind("click");
  $('#queue_table').hide(); 
 
  var url = "../api/queue/get_queue.php";
  var posting = $.post( url, { course: course } );

  var done = function(data) {
    var dataString = JSON.stringify(data);
    var dataParsed = JSON.parse(dataString);
    if(dataParsed.error){
      alert("Error fetching queue");
      return;
    }
   
    render_ta_table(dataParsed.TAs)
    if(is_TA){
      render_ta_view(dataParsed)
    }else{
      render_student_view(dataParsed)
    }
      
  }

  posting.done(done);
}

function render_ta_table(TAs){
  for(TA in TAs){
    ta_username = TAs[TA]["username"];
    $('#ta_on_duty').append("<h4>"+ta_username+"</h4>");
  }
}

function render_ta_view(dataParsed){
  queue_state = dataParsed.state;
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

function render_student_view(dataParsed){
  queue = dataParsed.queue;
  queue_state = dataParsed.state;
  in_queue = false;
  for(session in queue){
    if(my_username == queue[session]["username"]){
      in_queue = true;
      break;
    }
  }
  if(queue_state == "closed"){
    $("#queue_state").text("State: Closed");
    return;
  }else{
    $('#queue_table').show();
    $("#queue_state").text("State: Open");
    $('#queue').show();
    $('#queue').append("<tr> <th>Student</th> <th>Location</th> <th>Question</th> </tr>");
    for(row in queue){
      username = queue[row].username;
      question = queue[row].question;
      Location = queue[row].location;
      $('#queue').append('<tr> <td>'+username+'</td> <td>'+Location+'</td> <td>'+question+'</td> </tr>');
    }
  }

  if(!in_queue){//Not in queue
    $("#join_button").text("Enter Queue");
    $("#join_button").show();
    $("#join_button").click(function( event ) {
      event.preventDefault();
      enqueue_student(course, "this is my question", "This is my location");
    });
  }
  else{ //In queue
    $("#join_button").text("Leave Queue");
    $("#join_button").show();
    $("#join_button").click(function( event ) {
      event.preventDefault();
      dequeue_student(course);
    });
  }
}




function open_queue(course){
  url = "../api/queue/open.php";
  posting = $.post( url, { course: course } );
}

function close_queue(course){
  url = "../api/queue/close.php";
  posting = $.post( url, { course: course } );
}

function enqueue_student(course, question, Location){
  url = "../api/queue/enqueue_student.php";
  posting = $.post( url, { course: course, question: question, location: Location } );
}

function dequeue_student(course){
  url = "../api/queue/dequeue_student.php";
  posting = $.post( url, { course: course } );
}

function get_info(){
  url = "../api/user/get_info.php";
  posting = $.post( url);
  var done = function(data){
    var dataString = JSON.stringify(data);
    var dataParsed = JSON.parse(dataString);
    my_username = dataParsed.student_info["username"];
  } 
  posting.done(done);
}

/*
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



