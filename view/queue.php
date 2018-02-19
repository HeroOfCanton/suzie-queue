<?php
  include "router.php"
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
	<head>
		<title>Queue - Main</title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<meta name="AUTHOR"      content="Ryan Welling, Blake Burton, Zane Zakraisek"/>
		<meta name="keywords"    content="University of Utah, 2017-2018, College of Engineering"/>
		<meta name="description" content="Senior Project"/>

		<!-- ALL CSS FILES -->
		<link rel="stylesheet" type="text/css" href="../resources/CSS/global.css">
		<!-- Latest compiled and minified CSS -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
		<!-- Optional theme -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
		<!-- jQuery CDN -->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
		<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
		<!-- Latest compiled and minified JavaScript -->
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
        <!-- queue source -->
        <script src="./src/queue_functions.js"></script>
        <script src="./src/login_redirect.js"></script>
	</head>
	<body>

		<nav class="navbar navbar-default">
            <div class="container-fluid">
                <!-- Brand and toggle get grouped for better mobile display -->
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="#">
                        <img alt="Brand" src="../resources/img/UHz.png">
                    </a>
                </div>

                <!-- Collect the nav links, forms, and other content for toggling -->
                <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                    <ul class="nav navbar-nav navbar-right">
                        <li>
                            <a href="classes.php">All Classes</a>
                        </li>
                        <li>
                            <a href="my_classes.php">My Classes</a>
                        </li>
                        <li class="active">
                            <a href="#">Queue</a>
                        </li>
                        <li>
                            <a href="#" onclick="logout();">Logout</a>
                        </li>
                    </ul>
                </div><!-- /.navbar-collapse -->
            </div><!-- /.container-fluid -->
        </nav>  

		<div class="jumbotron jumbotron-billboard" style="margin-top: -15px; opacity: 0.75;">
			<div align="center" style="margin-top:  -40px; margin-bottom: -20px">	
				<h1 id="title" style="color: #404040; text-shadow: 2px 2px #000000;"></h1>
                                <h2 id="queue_state" style="color: #404040; "></h2>
			</div>
		</div>

		<div class="container">
			<div class="row">
				<div class="col-sm-2">
					<div class="panel panel-primary">
                		<div class="panel-heading">
                			<h3 class="panel-title">TA on Duty</h3>
                		</div>
                		<div class="panel-body" id="ta_on_duty">
                		</div>
            		</div>
				</div>
				<div class="col-sm-8 col-sm-offset-1">
					<div class="panel panel-primary">
                		<div class="panel-heading">
                			<h1 class="panel-title" align="center" style="text-decoration: underline;">TA Announcements</h1>
                		</div>
                		<div class="panel-body" style="max-height: 150; overflow-y: scroll;"  id=announcements></div>
            		</div>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-2">
					<div class="panel panel-primary">
                		<div class="panel-heading">
                			<h3 class="panel-title">TA Hours</h3>
                		</div>
                		<div class="panel-body">
                    		<p>
                    		   Mon 1-3p <br/>
                    		   Wed 1-3p <br/>
                    		   Fri 2-5p <br/>
                    		</p>
                		</div>
            		</div>
				</div>
				<div class="col-sm-8 col-sm-offset-1">
					<div id ="queue_table">
						<div class="panel panel-primary">
						<!-- Default panel contents -->
							<div class="panel-heading">
								<h3 class="panel-title">Queue</h3>
							</div>
								<table class="table table-hover" id="queue" align="center" style="margin-left:auto; margin-right:auto;"> </table>
						</div><!--panel-->
					</div><!--id-->
				</div><!--col-sm-8-->
			</div><!--row-->
			<div class="row">
				<div class="col-sm-2">
				  <button class="btn btn-success pull-right" id="duty_button"></button>
					<button class="btn btn-success pull-right" id="state_button"></button>
                    <button class="btn btn-success pull-right" id="join_button"></button>
                    <div id="dialog-form" title="Location and Question">
                        <p class="validateTips">All form fields are required.</p>
                        <form>
                            <fieldset>
                                <label for="location">Location</label>
                                    <input type="text" name="location" id="location" class="text" maxlength="50">
                                <label for="question">Question</label>
                                    <input type="text" name="question" id="question" value="" class="text" maxlength="50">
                            </fieldset>
                        </form>
                    </div> 
				</div>
			</div>
		</div><!--container-->
	</body>
</html>