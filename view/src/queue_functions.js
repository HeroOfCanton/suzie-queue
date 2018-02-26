var dialog;
var form;

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
  if(typeof course === 'undefined'){
    window.location ='./my_classes.php';
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
  $("#freeze_button").hide();
  $("#time_form").hide(); 
  $("#join_button").hide();
 
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
      is_TA = false;
      if($.inArray(course, dataParsed["ta_courses"]) != -1){
        is_TA = true;
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
      alert(dataParsed.error);
      return;
    }

    $("#queue_state").text("State: "+dataParsed.state);
    if(dataParsed.time_lim >0){
       $("#time_limit").text("Time Limit: "+dataParsed.time_lim);
    }else{
       $("#time_limit").text("Time Limit: None");
    }

    //Render the announcements box
    render_ann_box(dataParsed.announce);

    //This block of code does the majority of the rendering
    render_ta_table(dataParsed.TAs)
    if(is_TA){
      render_queue_table(dataParsed, "ta");
      render_ta_view(dataParsed)
    }else{
      render_queue_table(dataParsed, "student");
      render_student_view(dataParsed)
    }

    //Schedule the queue to refresh again. This way the calls can't overlap
    if(refresh != 0){
      setTimeout(function(){get_queue(course, refresh);}, refresh);
    }
  }
  posting.done(done);
}


function render_ann_box(anns){
  $("#anns tr").remove();
  for(ann in anns){
    
    var new_row = $('<tr>  <td><b>'+anns[ann]["tmstmp"].split(" ")[0]+':  </b></td>  <td><b>'+anns[ann]["announcement"]+'</b></td> </tr>');
    $('#anns').append(new_row);
  }
}

//Shows the TAs that are on duty
function render_ta_table(TAs){
  $("#ta_on_duty h4").remove();
  for(TA in TAs){
    full_name = TAs[TA]["full_name"];
    $('#ta_on_duty').append("<h4>"+full_name+"</h4>");
  }
}

function render_ta_view(dataParsed){
  $("#state_button").unbind("click");
  $("#duty_button").unbind("click");
  $("#freeze_button").unbind("click");
  $("#time_form").unbind("submit");

  queue_state = dataParsed.state;
  if(queue_state == "closed"){
    document.getElementById("state_button").style.background='ForestGreen';
    $("#state_button").text("OPEN QUEUE");
    $("#state_button").click(function( event ) {
      event.preventDefault();
      open_queue(course);
    });
    $("#duty_button").hide();
    $("#freeze_button").hide();
  }else{ //open or frozen
    document.getElementById("state_button").style.background='FireBrick';
    $("#state_button").text("CLOSE QUEUE");
    $("#state_button").click(function( event ) {
      event.preventDefault();
      close_queue(course);
    });
   
    if(queue_state == "open"){ 
      document.getElementById("freeze_button").style.background='#1B4F72';
      $("#freeze_button").text("FREEZE QUEUE");
      $("#freeze_button").click(function( event ) {
        event.preventDefault();
        freeze_queue(course);
      });
    }else{ //frozen
      document.getElementById("freeze_button").style.background='Orange';
      $("#freeze_button").text("RESUME QUEUE");
      $("#freeze_button").click(function( event ) {
        event.preventDefault();
        open_queue(course);
      });
    }

    TAs_on_duty = dataParsed.TAs;
    on_duty= false;
    for(var entry = 0; entry < TAs_on_duty.length; entry++){
      if(TAs_on_duty[entry].username == my_username){
        on_duty = true;
      }
    } 
    
    if(!on_duty) {
      document.getElementById("duty_button").style.background='ForestGreen';
      $("#duty_button").text("GO ON DUTY");
      $("#duty_button").click(function(event){
         event.preventDefault();
	 enqueue_ta(course); 
      });
    }
    else{
      document.getElementById("duty_button").style.background='FireBrick';
      $("#duty_button").text("GO OFF DUTY");
      $("#duty_button").click(function(event){
	 event.preventDefault();
         dequeue_ta(course); 
      });
    }
    $("#duty_button").show();
    $("#freeze_button").show();
    $("#time_form").show();
    $("#time_form").submit(function(event){
      event.preventDefault();
      var limit = $(this).find( "input[id='time_limit_input']" ).val();
      set_limit(course, limit);
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
 
  var state = dataParsed.state; 
  if(state == "closed" || (state == "frozen" && !in_queue )){
    $("#join_button").hide();
    return;
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
function render_queue_table(dataParsed, role){
  var queue = dataParsed.queue;
  var TAs   = dataParsed.TAs;
  $("#queue tr").remove();
  $('#queue').append("<tr> <th>Pos.</th> <th>Student</th> <th>Location</th> <th>Question</th> </tr>");
 
  helping = {};
  for(TA in TAs ){
    if(TAs[TA].helping != null){
      helping[TAs[TA].helping] = TAs[TA].duration;  
    }
  }
  
  time_lim = dataParsed.time_lim;
 
  var i = 1;
  for(row in queue){
    let username  = queue[row].username;
    let full_name = queue[row].full_name;
    var question  = queue[row].question;
    var Location  = queue[row].location;
    var new_row = $('<tr> <td>'+i+'</td> <td>'+full_name+'</td> <td>'+Location+'</td> <td>'+question+'</td> </tr>');
    i++;   
 
    if( username in helping ){
      new_row.css("background-color", "#b3ffb3");
      if(time_lim>0){
        duration = helping[username];
        fields = duration.split(':');
        duration = parseInt(fields[0])*3600 + parseInt(fields[1])*60 + parseInt(fields[2]);
        time_rem = time_lim*60-duration;

        if(time_rem <=0){
          new_row.css("background-color", "#fe2b40"); //User is over time limit
        }
      }
    }

    if(is_TA) {
      var dequeue_button = $('<button class="btn btn-primary" ><span>Dequeue</span> </button>');
      dequeue_button.click(function(event) {
        dequeue_student(course, username);
      });
      if( username in helping ){
        var help_button = $('<button class="btn btn-primary" ><span>Release</span> </button>');
        help_button.click(function(event){
          release_ta(course);
        });
      }else{
        var help_button = $('<button class="btn btn-primary" ><span>Help</span> </button>');
        help_button.click(function(event){
          enqueue_ta(course); //Maybe make this cleaner. 
          help_student(course, username);
        });
      }
      var increase_button = $('<button type="button" class="btn btn-primary btn-arrow-left">up</button>');
      increase_button.click(function(event){
        inc_priority(course, username); 
      });
      var decrease_button = $('<button type="button" class="btn btn-primary btn-arrow-left">down</button>');
      decrease_button.click(function(event){
        dec_priority(course, username);
      });


      new_row.append("<td>");
      new_row.append(help_button);
      new_row.append("</td>");
      new_row.append("<td>");
      new_row.append(dequeue_button);
      new_row.append("</td>");
      new_row.append("<td>");
      new_row.append(increase_button);
      new_row.append("</td>");
      new_row.append("<td>");
      new_row.append(decrease_button);
      new_row.append("</td>");
    }
    $('#queue').append(new_row);
  }
}



//API Endpoint calls
//This code should be fine for alpha
function open_queue(course){
  url = "../api/queue/open.php";
  posting = $.post( url, { course: course } );
  var done = function(data){
    var dataString = JSON.stringify(data);
    var dataParsed = JSON.parse(dataString);
    if(dataParsed.error){
      alert(dataParsed["error"]);
    }else{
      get_queue(course, 0); //refreshes the page
    }
  }
  posting.done(done);
}

function close_queue(course){
  url = "../api/queue/close.php";
  posting = $.post( url, { course: course } );
  var done = function(data){
    var dataString = JSON.stringify(data);
    var dataParsed = JSON.parse(dataString);
    if(dataParsed.error){
      alert(dataParsed["error"]);
    }else{
      get_queue(course, 0); //refreshes the page
    }
  }
  posting.done(done);
}

function freeze_queue(course){
  url = "../api/queue/freeze.php";
  posting = $.post( url, { course: course } );
  var done = function(data){
    var dataString = JSON.stringify(data);
    var dataParsed = JSON.parse(dataString);
    if(dataParsed.error){
      alert(dataParsed["error"]);
    }else{
      get_queue(course, 0); //refreshes the page
    }
  }
  posting.done(done);
}


function enqueue_student(course, question, Location){
  url = "../api/queue/enqueue_student.php";
  posting = $.post( url, { course: course, question: question, location: Location } );
  var done = function(data){
    var dataString = JSON.stringify(data);
    var dataParsed = JSON.parse(dataString);
    if(dataParsed.error){
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
    if(dataParsed.error){
      alert(dataParsed["error"]);
    }else{
      get_queue(course, 0); //refreshes the page
    }
  }
  posting.done(done);
}

function release_ta(course){
  url = "../api/queue/release_ta.php";
  posting = $.post( url, { course: course } );
  var done = function(data){
    var dataString = JSON.stringify(data);
    var dataParsed = JSON.parse(dataString);
    if(dataParsed.error){
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
    if(dataParsed.error){
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
    if(dataParsed.error){
      alert(dataParsed["error"]);
    }else{
      get_queue(course, 0); //refreshes the page
    }
  }
  posting.done(done);
}

function inc_priority(course, student){
  url = "../api/queue/inc_priority.php";
  posting = $.post( url, { course: course, student: student } );
  var done = function(data){
    var dataString = JSON.stringify(data);
    var dataParsed = JSON.parse(dataString);
    if(dataParsed.error){
      alert(dataParsed["error"]);
    }else{
      get_queue(course, 0); //refreshes the page
    }
  }
  posting.done(done);
}

function dec_priority(course, student){
  url = "../api/queue/dec_priority.php";
  posting = $.post( url, { course: course, student: student } );
  var done = function(data){
    var dataString = JSON.stringify(data);
    var dataParsed = JSON.parse(dataString);
    if(dataParsed.error){
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
  posting = $.post( url, { course: course, student: username } );
  var done = function(data){
    var dataString = JSON.stringify(data);
    var dataParsed = JSON.parse(dataString);
    if(dataParsed.error){
      alert(dataParsed["error"]);
    }else{
      get_queue(course, 0); //refreshes the page
    }
  }
  posting.done(done);
}

function set_limit(course, limit){
  url = "../api/queue/set_limit.php";
  posting = $.post( url, { course: course, time_lim: limit } );
  var done = function(data){
    var dataString = JSON.stringify(data);
    var dataParsed = JSON.parse(dataString);
    if(dataParsed.error){
      alert(dataParsed["error"]);
    }else{
      get_queue(course, 0); //refreshes the page
    }
  }
  posting.done(done);
}
