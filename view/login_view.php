<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
	<head>
		<title>TA Help Queue</title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">

		<meta name="AUTHOR"      content="Ryan Welling, Blake Burton, Zane Zakraisek"/>
		<meta name="keywords"    content="University of Utah, 2017-2018"/>
		<meta name="description" content="Senior Project"/>

		<!-- ALL CSS FILES -->
		<!-- Latest compiled and minified CSS -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css" integrity="sha384-PsH8R72JQ3SOdhVi3uxftmaW6Vc51MKb0q5P2rRUpPvrszuE4W1povHYgTpBfshb" crossorigin="anonymous">

		<!-- Optional theme -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">

		<script src="//ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

		<!-- Latest compiled and minified JavaScript -->
		<script src="//code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
		<script src="//cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js" integrity="sha384-vFJXuSJphROIrBnz7yo7oB41mKfc8JzQZiCq4NCceLEaO4IHwicKwpJf9c9IpFgh" crossorigin="anonymous"></script>
		<script src="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/js/bootstrap.min.js" integrity="sha384-alpBpkh1PFOepccYVYDB4do5UnbKysX5WZXm3XxPqe5iKTfUKjNkCk9SaVuEZflJ" crossorigin="anonymous"></script>

        <script type="text/javascript">
        	$(document).ready(function() { 
        			$("#login_button").on("click", function() {
        				$("#login_form").show();
        			});

        			$('.dropdown').hover(
				        function() {
				            $(this).find('.dropdown-menu').stop(true, true).delay(200).fadeIn();
				        }, 
				        function() {
				            $(this).find('.dropdown-menu').stop(true, true).delay(200).fadeOut();
				        }
				    	);

					    $('.dropdown-menu').hover(
				        function() {
				            $(this).stop(true, true);
				        },
				        function() {
				            $(this).stop(true, true).delay(200).fadeOut();
				        }
					    );
        		});
        </script>
      </head>

      <body style="padding-top: 70px;">       

			<div class="jumbotron" style="background-image: url('resources/img/campus.jpg') !important; background-position: center center !important">
				<div align="center">	
					<h1 style="color: #e8002b; text-shadow: 2px 2px #ffffff;">Login Portal</h1>
				</div>
			</div>
		
			<div align = "center" style="margin-top:50px;">
				<button class="btn" id="login_button"><span>Login</span></button>
			
				<a href="new_user.php"><button class="btn"><span>New User</span></button></a>
			</div>

			<?php if($_POST)
					{
						$display = "block";
					}
					else
					{
						$display = "none";
					}
			?>
			<div align="center" style="margin-top:50px;">

			<form class="form-inline" action="authenticate.php" method="post" id="login_form" style="display:<?= $display; ?>;">
			  <div class="form-group">
			    <label class="sr-only" for="exampleInputEmail3">Login</label>
			    <input class="form-control" id="Login" name="Login" type="text" pattern="[a-zA-Z0-9]+" value="<?= $_POST['Login']; ?>" placeholder="User Name" required autofocus>
			  </div>
			  <div class="form-group">
			    <label class="sr-only" for="exampleInputPassword3">Password</label>
			    <input class="form-control" id="password" name="password" type="password" minlength="8" value="<?= $_POST['password']; ?>" placeholder="Password" required>
			  </div>
			  <button id="saveForm" name="saveForm" type="submit" value="Submit" class="btn btn-primary">Sign in</button>
			  	
			  </div>
			  <p align="center"> Message: <?= $message; ?> <?= $output; ?> </p>
			</form>

			</div>
		</body>
	</html>
