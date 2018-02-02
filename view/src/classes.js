$(function () {
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
      myCourses = dataParsed.student_courses;
      renderCourseTable(allCourses, myCourses);
    });
  });
});

function renderCourseTable(allCourses, myCourses) {
  var table = $('#all_classes');
  for(course in allCourses){
    course_name = allCourses[course];
    if( $.inArray(course_name, myCourses) >= 0 ){
      text = "Leave";
      action = "dropCourse('"+course_name+"')";
    }
    else{
      text = "Enroll";
      action = "enrollCourse('"+course_name+"')";
    }
    var tableRow = $('<tr>');
    tableRow.append($('<td>').text( course_name ));
    tableRow.append('<td> <button class="btn btn-primary" onclick="'+action+'">'+text+'</button> </td>');
    table.append(tableRow);
  }
}

function enrollCourse(course) {
  url = "../api/user/add_class.php";
  var $posting = $.post( url, { course: course} );
  $posting.done( function(data) {
    var dataString = JSON.stringify(data);
    var dataParsed = JSON.parse(dataString);
    location.reload(); 
  });
}

function dropCourse(course) {
  url = "../api/user/rem_class.php";
  var $posting = $.post( url, { course: course} );
  $posting.done( function(data) {
    var dataString = JSON.stringify(data);
    var dataParsed = JSON.parse(dataString);
    location.reload(); 
  });
}
