login = function( event ) {
  event.preventDefault();
  
  var $form = $( this ),
  $username = $form.find( "input[name='username']" ).val(),
  $password = $form.find( "input[name='password']" ).val(),
  url = "../api/login.php";

  var $posting = $.post( url, { username: $username, password: $password } );

  $posting.done(function( data ) {
    var dataString = JSON.stringify(data);
    var dataParsed = JSON.parse(dataString);
    if(dataParsed.authenticated){

      var $get_req = $.get("../api/user/my_classes.php");
      $get_req.done( function(data) {
        var dataString = JSON.stringify(data);
        var dataParsed = JSON.parse(dataString);
        if(dataParsed.student_courses.length + dataParsed.ta_courses.length === 0){
          window.location.href = '../view/classes.php';
        }
        else{
          window.location.href = '../view/my_classes.php';
        }
      });

    }
    else{
      alert(dataParsed.error);
    }
  });
}

$(document).ready(function(){
  $("#login_form").submit( login ); 
});
