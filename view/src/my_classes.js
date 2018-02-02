$(function () {
  var $url = "../api/user/my_classes.php";
  var $get = $.get( $url );
  $get.done(function(data){
    var dataString = JSON.stringify(data);
    var dataParsed = JSON.parse(dataString);
    stud_courses = dataParsed.student_courses;
    ta_courses   = dataParsed.ta_courses;
    renderCourseTable(ta_courses, "TA");
    renderCourseTable(stud_courses, "Student");
  });
});

function renderCourseTable(courses, role) {
  var table = $('#my_classes');
  courses.forEach(function (course) {
    var tableRow = $('<tr>');
    tableRow.append($('<td>').text(course));
    tableRow.append($('<td>').text(role));
    URI = encodeURI("main_queue_view.html?course="+course);
    tableRow.append( '<td> <a href="'+URI+'"> <button class="btn btn-primary"><span>GoTo</span> </button></a> </td>'  );
    table.append(tableRow);
  });
}
