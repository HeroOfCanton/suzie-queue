var dialog;
var form;
var lab_location;
var question;
var being_helped = false;

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

  dialog = $( "#dialog-form" ).dialog({
    autoOpen: false,
    height: 400,
    width: 350,
    modal: true,
    buttons: {
      "Enter Queue": function() {
	  lab_location = document.getElementById("location").value;
	  question = document.getElementById("question").value;
	  enqueue_student(course, question, lab_location);
	  dialog.dialog( "close" );
      },
      Cancel: function() {
        dialog.dialog( "close" );
      }
    }
  });
  $("#duty_button").hide();
  $("#state_button").hide();
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
  $("#state_button").hide();
  $("#state_button").unbind("click");

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

    TAs_on_duty = dataParsed.TAs;
    on_duty= false;
    for(var entry = 0; entry < TAs_on_duty.length; entry++){
      if(TAs_on_duty[entry].username == my_username){
        on_duty = true;
      }
    } 
    
    if(!on_duty) {
      $("#duty_button").text("ON DUTY");
      $("#duty_button").click(function(event){
         event.preventDefault();
	 enqueue_ta(course); 
      });
    }
    else{
      $("#duty_button").text("OFF DUTY");
      $("#duty_button").click(function(event){
	 event.preventDefault();
         dequeue_ta(course); 
      });
    }
  }
  $("#state_button").show();
  $("#join_button").hide();
  $("#duty_button").show();
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
      dialog.dialog( "open" );
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
    var new_row = $('<tr> <td>'+username+'</td> <td>'+Location+'</td> <td>'+question+'</td> </tr>');
    if(is_TA) {
      var dequeue_button = $('<button class="btn btn-primary" ><span>Dequeue Student</span> </button>');
	  dequeue_button.click(function(event) {
	    dequeue_student(course, username);
	  });
      new_row.append(dequeue_button);
	  var help_button = $('<button class="btn btn-primary" ><span>Help Student</span> </button>');
	  help_button.click(function(){
	    being_helped = true;
	  });
	  new_row.append(help_button);
    }
    $('#queue').append(new_row);
  }
  if(being_helped) {
    $(this).closest('table').children('tr:first').css("background-color", "#b3ffb3");
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
  being_helped = false;
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

function enqueue_ta(course){
  url = "../api/queue/enqueue_ta.php";
  posting = $.post( url, { course: course } );
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

function dequeue_ta(course){
  url = "../api/queue/dequeue_ta.php";
  posting = $.post( url, { course: course } );
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

next_student = function(course){
  url = "../api/queue/next_student.php";
  posting = $.post( url, { course: course } );
}

function help_student(course, username){
  url = "../api/queue/help_student.php";
  posting = $.post( url, { course: course, username: username } );
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
