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
  start();
});

function start(){
  $("#title").text(course+' Queue');
  url = "../api/user/get_info.php";
  posting = $.post( url);
  var done = function(data){
    var dataString = JSON.stringify(data);
    var dataParsed = JSON.parse(dataString);
    my_username = dataParsed.student_info["username"];
    url = "../api/user/my_classes.php";
    posting = $.get( url);
    var done = function(data){
      var dataString = JSON.stringify(data);
      var dataParsed = JSON.parse(dataString);
      if($.inArray(course, dataParsed["ta_courses"]) != -1){
        is_TA = true;
      }
      else if($.inArray(course, dataParsed["student_courses"]) != -1){
        is_TA = false;
      }
      else{
        alert("Not enrolled in course");
      }
      get_queue(course, 5000); //This function calls itself every 5 seconds
    }
    posting.done(done);
  }
  posting.done(done);
}

function get_queue(course, refresh) {
  $("#state_button").hide();
  $("#state_button").unbind("click");
 
  var url = "../api/queue/get_queue.php";
  var posting = $.post( url, { course: course } );

  var done = function(data) {
    var dataString = JSON.stringify(data);
    var dataParsed = JSON.parse(dataString);
    if(dataParsed.error){
      alert("Error fetching queue");
      return;
    }

    if(dataParsed.state == "closed"){
      $("#queue_state").text("State: Closed");
    }else{ //Queue is open
      $("#queue_state").text("State: Open");
    }
  
    //This block of code does the majority of the rendering 
    render_ta_table(dataParsed.TAs)
    if(is_TA){
      render_queue_table(dataParsed.queue, "ta");
      render_ta_view(dataParsed)
    }else{
      render_queue_table(dataParsed.queue, "student");
      render_student_view(dataParsed)
    }
    
    //Schedule the queue to refresh again. This way the calls can't overlap
    if(refresh != 0){
      setTimeout(function(){get_queue(course, refresh);}, refresh);
    }
  }
  posting.done(done);
}

//Shows the TAs that are on duty
function render_ta_table(TAs){
  $("#ta_on_duty h4").remove();
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
  $("#state_button").show();
}

function render_student_view(dataParsed){
  queue = dataParsed.queue;
  in_queue = false;
  for(session in queue){
    if(my_username == queue[session]["username"]){
      in_queue = true;
      break;
    }
  }

  $("#join_button").unbind("click");
  if(!in_queue){//Not in queue
    $("#join_button").text("Enter Queue");
    $("#join_button").show();
    $("#join_button").click(function( event ) {
      event.preventDefault();
      enqueue_student(course, "question"    , "location");
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

//Displays the queue table
function render_queue_table(queue, role){
  $("#queue tr").remove();
  $('#queue').append("<tr> <th>Student</th> <th>Location</th> <th>Question</th> </tr>");
  for(row in queue){
    username = queue[row].username;
    question = queue[row].question;
    Location = queue[row].location;
    //If role is TA, add button to help to student
    $('#queue').append('<tr> <td>'+username+'</td> <td>'+Location+'</td> <td>'+question+'</td> </tr>');
  }
}






//API Endpoint calls
//This code should be fine for alpha
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
  var done = function(data){
    var dataString = JSON.stringify(data);
    var dataParsed = JSON.parse(dataString);
    if($.inArray(course, dataParsed["error"]) != -1){
      alert(dataParsed["error"]);
    }else{
      get_queue(course, 0); //refreshes the page
    }
  }
  posting.done(done);
}

/*
 *Students call dequeue_student(course, null) to dequeue themselves
 *TAs call dequeue_student(course, username) to dequeue student
 */
function dequeue_student(course, username){
  url = "../api/queue/dequeue_student.php";
  if(username == null){
    posting = $.post( url, { course: course } );
  }
  else{
    posting = $.post( url, { course: course, username: username } );
  }
  var done = function(data){
    var dataString = JSON.stringify(data);
    var dataParsed = JSON.parse(dataString);
    if($.inArray(course, dataParsed["error"]) != -1){
      alert(dataParsed["error"]);
    }else{
      get_queue(course, 0); //refreshes the page
    }
  }
  posting.done(done);
}

enqueue_ta = function(course){
  url = "../api/queue/enqueue_ta.php";
  posting = $.post( url, { course: course } );
}

dequeue_ta = function(course){
  url = "../api/queue/dequeue_ta.php";
  posting = $.post( url, { course: course } );
}

next_student = function(course){
  url = "../api/queue/next_student.php";
  posting = $.post( url, { course: course } );
}
