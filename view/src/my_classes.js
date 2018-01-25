$(function () {
  var $url = "../api/user/my_classes.php";
  var $get = $.get( $url );
  $get.done(function(data){
    var dataString = JSON.stringify(data);
    var dataParsed = JSON.parse(dataString);
    myCourses = dataParsed.student_courses;
    renderCourseTable(myCourses);
  });
});

function renderCourseTable(courses) {
  var table = $('#my_classes');
  courses.forEach(function (course) {
    var tableRow = $('<tr>');
    tableRow.append($('<td>').text(course));
    URI = encodeURI("main_queue_view.html?course="+course);
    tableRow.append( '<td> <a href="'+URI+'"> <button class="btn btn-primary"><span>GoTo</span> </button></a> </td>'  );
    table.append(tableRow);
  });
}
