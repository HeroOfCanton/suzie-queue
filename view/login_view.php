<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
	<head>
		<title>TA Help Queue</title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<meta name="AUTHOR"      content="Ryan Welling, Blake Burton, Zane Zakraisek"/>
		<meta name="keywords"    content="University of Utah, 2017-2018"/>
		<meta name="description" content="Senior Project"/>

		<!-- ALL CSS FILES -->
		<!-- Latest compiled and minified CSS -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
		<!-- Optional theme -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
		<!-- jQuery CDN -->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script> 
		<!-- Latest compiled and minified JavaScript -->
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
	</head>

      <body>       
		  <div class="jumbotron" style="background-image: url('resources/img/campus.jpg') !important; background-position: center center !important">
			  <div align="center">	
				  <h1 style="color: #e8002b; text-shadow: 2px 2px #ffffff;">Login Portal</h1>
			  </div>	
		  </div>
		  <form class="form-inline" action="../api/login.php" method="post" id="login_form">
			  <div class="form-group">
				  <label class="sr-only" for="exampleInputEmail3">Login</label>
				  <input class="form-control" id="Login" name="username" type="text" pattern="[a-zA-Z0-9]+" value="<?= $_POST['Login']; ?>" placeholder="User Name" required autofocus>
			  </div>
			  <div class="form-group">
				  <label class="sr-only" for="exampleInputPassword3">Password</label>
				  <input class="form-control" id="password" name="password" type="password" minlength="8" value="<?= $_POST['password']; ?>" placeholder="Password" required>
			  </div>
			  <button id="saveForm" name="saveForm" type="submit" value="Submit" class="btn btn-primary">Sign in</button>
		  </form>
	  </body>
</html>
