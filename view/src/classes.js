function renderCourse(course) {
    // #7 continued
    var tableRow = $('<tr>');
    tableRow.append($('<td>').text(course));
    action = "enrollCourse('"+course+"')";
    tableRow.append('<td> <button onclick="'+action+'">Enroll</button> </td>');
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
        url: "../api/classes/all_classes.php",
        dataType: "json",
        async: true,
        success: function (coursesResponse, textStatus, jqXHR) {
            // #5
            // Unncessary if data type is set properly:
            // coursesResponse = JSON.parse(coursesResponse);
            console.log(coursesResponse.all_courses);

            // coursesResponse should be similar in form to the hardcoded 'classes' object 
            // we defined at the top. This is the data returned from the all_classes endpoint.
            renderCourseTable(coursesResponse.all_courses);
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


function enrollCourse(course) {
  url = "../api/user/add_class.php";
  var $posting = $.post( url, { course: course} );
  $posting.done( function(data) {
    var dataString = JSON.stringify(data);
    var dataParsed = JSON.parse(dataString);
  });
}

function dropCourse(course) {
  url = "../api/user/rem_class.php";
  var $posting = $.post( url, { course: course} );
  $posting.done( function(data) {
    var dataString = JSON.stringify(data);
    var dataParsed = JSON.parse(dataString);
  });
}


// 1. Server returns all_classes_view.php
// 1.1 Browser parses the markup, creating the Document Object Model (DOM). This will contain a DOM "node" that had the id "classes-table" (or whatever it is).
// 2. Browser sees script tag for 'src/classes.js', requests that asynchronously
// 3. This script runs, declaring (but not calling) the renderCourse and renderCourseTable functions. It also calls $.ready, providing a function to run WHEN THE DOCUMENT IS FINISHED LOADING (not at the time $.ready is called).
// 4. Document finishes loading, $.ready callback is executed making the $.ajax call, making an asynchronous request to the backend API endpoint
// 5. Server processes the request and sends a response, triggering the callback that was provided to the $.ajax call through the 'success' parameter. This calls renderCourseTable w/ the JSON that the AJAX call received.
// 6. renderCourseTable grabs the DOM node for the table begins looping over each class.
// 7. For each class, renderCourse is called, creating a NEW DOM node ("tr"), appending the text data as needed, and returning the DOM node. Note that this is a "detached" DOM node in that it has not yet been inserted into the DOM "tree" and thus not yet rendered.
// 8. When renderCourse returns, renderCourseTable will insert it into the table, rendering it on the page because the table DOM node was already attached to the rendered DOM tree.
