login = function( event ) {
  event.preventDefault();
  
  var $form = $( this ),
  $username = $form.find( "input[name='username']" ).val(),
  $password = $form.find( "input[name='password']" ).val(),
  url = $form.attr( "action" );

  var $posting = $.post( url, { username: $username, password: $password } );

  $posting.done(function( data ) {
    var dataString = JSON.stringify(data);
    var dataParsed = JSON.parse(dataString);
    if(dataParsed.authenticated){
      window.location.href = '../view/all_classes_view.html';
    }
    else{
      alert('Invalid Login');
    }
  });
}

$(document).ready(function(){
  $("#login_form").submit( login ); 
});
