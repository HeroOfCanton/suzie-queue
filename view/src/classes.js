get_classes();

function get_classes(){
  var $url = "../api/classes/all_classes.php";
  var $get = $.get( $url );
  $get.done(function(data){
    var dataString = JSON.stringify(data);
    var dataParsed = JSON.parse(dataString);
    allCourses = dataParsed.all_courses;

    var $url = "../api/user/my_classes.php";
    var $get = $.get( $url );
    $get.done( function(data) {
      var dataString = JSON.stringify(data);
      var dataParsed = JSON.parse(dataString);
      renderCourseTable(allCourses, dataParsed);
    });
  });
}

function renderCourseTable(allCourses, dataParsed) {
  $('#all_classes tr').remove();
  var table = $('#all_classes');
  myCourses = dataParsed.student_courses;
  ta_courses= dataParsed.ta_courses;
  for(course in allCourses){
    var course_name = allCourses[course];
    var tableRow = $('<tr>');
    tableRow.append($('<td>').text( course_name ));
    if( $.inArray(course_name, ta_courses) >= 0 ){
      tableRow.append('<td> <button class="btn btn-primary" disabled> TA </button> </td>');
    }
    else if( $.inArray(course_name, myCourses) >= 0 ){
      text = "Leave";
      action = "dropCourse('"+course_name+"')";
      tableRow.append('<td> <button class="btn btn-primary" onclick="'+action+'">'+text+'</button> </td>');
    }
    else{
      text = "Enroll";
      action = "enrollCourse('"+course_name+"')";
      tableRow.append('<td> <button class="btn btn-primary" onclick="'+action+'">'+text+'</button> </td>');
    }
    table.append(tableRow);
  }
}

function enrollCourse(course) {
  url = "../api/user/add_class.php";
  var $posting = $.post( url, { course: course} );
  $posting.done( function(data) {
    var dataString = JSON.stringify(data);
    var dataParsed = JSON.parse(dataString);
    if(dataParsed["error"]){
      alert(dataParsed["error"])
    }else{
      get_classes(); 
    }
  });
}

function dropCourse(course) {
  url = "../api/user/rem_class.php";
  var $posting = $.post( url, { course: course} );
  $posting.done( function(data) {
    var dataString = JSON.stringify(data);
    var dataParsed = JSON.parse(dataString);
    if(dataParsed["error"]){
      alert(dataParsed["error"])
    }else{
      get_classes();
    }
  });
}
