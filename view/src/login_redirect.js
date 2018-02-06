//This is a dummy endpoint call that verifies that
//the user is authenticated
//
//Until we write the view the professional way, using
//an endpoint mapper, this file should be sourced in
//every page that sits behind a login
//
//Obviously you can never implement security redirects
//in javascript, since its rendered on the client side,
//but since ALL endpoint calls are authenticated on the 
//server side anyway, we run no risk of leaking any 
//information implementing the login redirects on the 
//client side
var $get_req = $.get("../api/user/my_classes.php");
$get_req.done( function(data) {
  var dataString = JSON.stringify(data);
  var dataParsed = JSON.parse(dataString);
  if(!dataParsed.authenticated){
    window.location.href = '../view/index.html';
  }
});

function logout(){
  var $get_req = $.get("../api/logout.php");
  $get_req.done( function(data) {
    location.reload();
  });
}
