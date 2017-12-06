function renderCourse(course) {
    // #7 continued
    var tableRow = $('<tr>');
    tableRow.append($('<td>').text(course));
    //tableRow.append($('<td>').text(course.id));
    //tableRow.append($('<td>').text(course.teacher));
    return tableRow;
}

function renderCourseTable(courses) {
    // #6
    var table = $('#all_classes');
    courses.forEach(function (each_class) {
        // #7
        var renderedCourse = renderCourse(each_class);
        // #8
        table.append(renderedCourse);
        // You will now see the new table row rendered into the browser at the point
    });
}

// #3
$(function () {
    // #4
    $.ajax({
        url: "../../api/classes/all_classes.php",
        dataType: "json",
        async: true,
        success: function (coursesResponse, textStatus, jqXHR) {
            // #5
            // Unncessary if data type is set properly:
            // coursesResponse = JSON.parse(coursesResponse);

            console.log(coursesResponse);

            // coursesResponse should be similar in form to the hardcoded 'classes' object 
            // we defined at the top. This is the data returned from the all_classes endpoint.
            renderCourseTable(coursesResponse);
        }
    });
    
    /*
     Promise alternative to 'success' parameter:
        .then(function (coursesResponse) {
            // Unncessary if data type is set properly:
            // coursesResponse = JSON.parse(coursesResponse);
            renderCourseTable(coursesResponse);
        });
    */
});
