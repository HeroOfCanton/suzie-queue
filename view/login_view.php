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
			
			<!-- Navigation -->
		    <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
		        <div class="container">
		            <!-- Brand and toggle get grouped for better mobile display -->
		            <div class="navbar-header">
		                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
		                    <span class="sr-only">Toggle navigation</span>
		                    <span class="icon-bar"></span>
		                    <span class="icon-bar"></span>
		                    <span class="icon-bar"></span>
		                </button>
		                <a class="navbar-brand" href="#">Graduate Tracker</a>
		            </div>
		            <!-- Collect the nav links, forms, and other content for toggling -->
		            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
	                <ul class="nav navbar-nav navbar-right">
	                  <li class="dropdown">
	                  	<a class="dropdown-toggle" href="#" data-toggle="dropdown" role="button" aria-expanded="false" title="Main">Main / Email</a>
       								<ul class="dropdown-menu">
       									<li><a href="href=../../index.html" title="Main Page">Main Page</a></li>
       									<li><a href="mailto:ryan@ryanwelling.com" title="Email Me">Email Me</a></li>
    									</ul> 
										</li>

										<!-- Next Main Menu Item -->
										<li><a href="index.html" title="Grad Progess Main Page">Grad Progress Main</a></li>

										<!-- Next Main Menu Item -->
										<li class="active"><a href="entrance.php" title="Entrance Portal">Entrance Portal</a></li>
						<?php 
                        if($_SESSION['Role'] == Student)
                        {
                            $navelem = '<li><a href="Student/student_forms.php?id=' .$_SESSION['ID'] .'" title="Student Form">Your Forms</a></li>';
                        }
                        else if($_SESSION['Role'] == Faculty)
                        {
                            $navelem = '<li><a href="Advisor/students.php?lastName=' .$_SESSION['Last_Name'] .'" title="Advisor Portal">List of Students</a></li>';
                        }
                        else {
                        	$navelem = '<li><a href="DGS/overview.php" title="DGS Home">DGS Home</a></li>';
                        }
	                    ?>
	                    <!-- Next Main Menu Item -->
	                  <?= $navelem; ?>
	                </ul>
		            </div>
		            <!-- /.navbar-collapse -->
		        </div>
		        <!-- /.container -->
		    </nav>
		    
		    <div class="container">
					<div class="row">
						<div class="col-sm-12">
							<h2 style="padding-bottom: 25px;">Welcome to the <span style="color: #e8002b;">University of Utah</span> School of Computing Graduate Progress Tracker</h2>
						</div><!--col-sm-12-->
					</div><!--row-->
					<div class="row">
						<div class="col-sm-6">
							<p style="font-size:150%;">The School of Computing tracks Graduate student progress each semester to make sure the students are making satisfactory progress toward their degree. </p>
						</div><!--col-sm-6-->
						<div class="col-sm-6">
							<p style="font-size:150%;"> If you're an existing user, please login, and you'll be taken to the correct portal to access your information. If you're a new user, please register using the 'New User' button below.</p>
						</div><!--col-sm-6-->
					</div><!--row-->
				</div><!--container-->
			
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
