<?php

session_start();

//global variables
$username = $_SESSION['username'];
$rank = $_SESSION['rank'];
$fullname = $_SESSION['fullname'];
$chapter = $_SESSION['chapter'];

//EMAIL PERMISSION
require('../php/connect.php');

$query="SELECT value FROM settings WHERE name='officerEmailPermission' AND chapter='$chapter'";

$result = mysqli_query($link, $query);

if (!$result){
	die('Error: ' . mysqli_error($link));
}

//save the result
list($perm) = mysqli_fetch_array($result);
$emailPerm = $perm;

//get permission settings

//INFO POSTING
$query="SELECT value FROM settings WHERE name='officerInfoPermission' AND chapter='$chapter'";

$result = mysqli_query($link, $query);

if (!$result){
	die('Error: ' . mysqli_error($link));
}

//save the result
list($perm) = mysqli_fetch_array($result);
$officerPerm = $perm;

//function for adding a team to an event
if(isset($_POST['evt'])){

	require('../php/connect.php');
	
	$name = $_POST['evt'];
	$i = $_POST['num'];
	$isq = 'yes';
	
	//get max and min members for this event
		$query = "SELECT membermin, membermax FROM events WHERE name='$name'";

		$result = mysqli_query($link, $query);

		if (!$result){
			die('Error: ' . mysqli_error($link));
		}

		//for each event at the current competition level
		list($min, $max) = mysqli_fetch_array($result);
			
				//add that event to the TEAMS table
				$blank = ' ';
				if($max == 1){
					$sql = "INSERT INTO teams (event, team, member1, member2, member3, member4, member5, member6, qualifier, chapter, min) VALUES ('$name', '$i', '$blank', NULL, NULL, NULL, NULL, NULL, '$isq', '$chapter', '$min')";
				}
				if($max == 2){
					$sql = "INSERT INTO teams (event, team, member1, member2, member3, member4, member5, member6, qualifier, chapter, min) VALUES ('$name', '$i', '$blank', '$blank', NULL, NULL, NULL, NULL, '$isq', '$chapter', '$min')";
				}
				if($max == 3){
					$sql = "INSERT INTO teams (event, team, member1, member2, member3, member4, member5, member6, qualifier, chapter, min) VALUES ('$name', '$i', '$blank', '$blank', '$blank', NULL, NULL, NULL, '$isq', '$chapter', '$min')";
				}
				if($max == 4){
					$sql = "INSERT INTO teams (event, team, member1, member2, member3, member4, member5, member6, qualifier, chapter, min) VALUES ('$name', '$i', '$blank', '$blank', '$blank', '$blank', NULL, NULL, '$isq', '$chapter', '$min')";
				}
				if($max == 5){
					$sql = "INSERT INTO teams (event, team, member1, member2, member3, member4, member5, member6, qualifier, chapter, min) VALUES ('$name', '$i', '$blank', '$blank', '$blank', '$blank', '$blank', NULL, '$isq', '$chapter', '$min')";
				}
				if($max == 6){
					$sql = "INSERT INTO teams (event, team, member1, member2, member3, member4, member5, member6, qualifier, chapter, min) VALUES ('$name', '$i', '$blank', '$blank', '$blank', '$blank', '$blank', '$blank', '$isq', '$chapter', '$min')";
				}
				

				if (!mysqli_query($link, $sql)){
					die('Error: ' . mysqli_error($link));
				}
}

//function for removing a team from an event
if(isset($_POST['qualname'])){

	require('../php/connect.php');
	
	$name = $_POST['qualname'];
	$team = $_POST['qualteam'];
			
	//remove from the TEAMS table
	$sql = "DELETE FROM teams WHERE event='$name' AND chapter='$chapter' AND team='$team'";
	
	if (!mysqli_query($link, $sql)){
		die('Error: ' . mysqli_error($link));
	}
}

//functions for event signup
if(isset($_POST['slot'])){

	require('../php/connect.php');

		//get user's eventpoints
		$pointsquery="SELECT eventpoints FROM users WHERE username='$username' AND fullname='$fullname' AND chapter='$chapter'";

		$pointsresult = mysqli_query($link,$pointsquery);

		if (!$pointsresult){
			die('Error: ' . mysqli_error($link));
		}

		list($eventpoints) = mysqli_fetch_array($pointsresult);

		//if the user has event points
		if($eventpoints > 0){

			//method variables, save locally
			$name = $_POST['name'];
			$team = $_POST['team'];
			$slot = $_POST['slot'];
			$open = $_POST['open'];

			//if this slot is open
			if($open == "yes"){

				//default variable values
				$memberColumn = "member" . $slot;
				$alreadyInEvent = "no";

				//check if user is already in that event
				$checkEventSql = "SELECT member1, member2, member3, member4, member5, member6 FROM teams WHERE event='$name' AND chapter='$chapter'";

				$checkEventResult = mysqli_query($link,$checkEventSql);

				if (!$checkEventResult){
					die('Error: ' . mysqli_error($link));
				}

				//for each team of the event the user is trying to enter
				while(list($member1, $member2, $member3, $member4, $member5, $member6) = mysqli_fetch_array($checkEventResult)){
					if($member1 == $fullname || $member2 == $fullname || $member3 == $fullname || $member4 == $fullname || $member5 == $fullname || $member6 == $fullname){
						$alreadyInEvent = "yes";
					}
				}

				//only enter event if not already in it
				if($alreadyInEvent == "no"){

					//add the user to that team
					$sql = "UPDATE teams SET $memberColumn='$fullname' WHERE event='$name' AND team='$team' AND chapter='$chapter'";

					if (!mysqli_query($link,$sql)){
						die('Error: ' . mysqli_error($link));
					}
					
					//add the team to the changes table
					$sql = "INSERT INTO changes (event, team, slot, name, date, chapter) VALUES ('$name', '$team', '$slot', '$fullname', now(), '$chapter')";

					if (!mysqli_query($link,$sql)){
						die('Error: ' . mysqli_error($link));
					}
					
					//add to the activity log
					$activityForm = "Added Event " . $name;
					$sql = "INSERT INTO activity (user, activity, date, chapter) VALUES ('$fullname', '$activityForm', now(), '$chapter')";
			
					if (!mysqli_query($link, $sql)){
						die('Error: ' . mysqli_error($link));
					}

					//decrease event points
					$newPoints = $eventpoints - 1;

					$eventSql = "UPDATE users SET eventpoints='$newPoints' WHERE username='$username' AND fullname='$fullname' AND chapter='$chapter'";

					if (!mysqli_query($link,$eventSql)){
						die('Error: ' . mysqli_error($link));
					}

				}else{
					//this occurs when user tries to double-register
					$fmsg = "Already In Event!";
				}
			}
		}
	
	mysqli_close($link);
}

?>

<!DOCTYPE html>

<head>
	<!-- Bootstrap, cause it dabs -->
	<link rel="stylesheet" href="../bootstrap-4.1.0/css/bootstrap.min.css">
    <script src="../js/jquery.min.js"></script>
    <script src="../js/popper.min.js"></script>
    <script src="../bootstrap-4.1.0/js/bootstrap.min.js"></script>
    <link href="https://fonts.googleapis.com/css?family=Raleway" rel="stylesheet">
	<title>
		Chapter Sweet
	</title>
	<link href="../css/main.css" rel="stylesheet" type="text/css" />
</head>

<body>
<!--Spooky bar at the top-->
	<nav class="navbar navbar-dark darknav navbar-expand-sm">
	  	<div class="container-fluid">
		   	<span id="openNavButton" style="font-size:30px;cursor:pointer;color:white;padding-right:30px;" onclick="toggleNav()">&#9776;</span>
			<div class="ml-auto navbar-nav">
			    	<a class="nav-item nav-link active" href="../php/logout.php">Logout</a>
			</div>
	</div>
	</nav>
<!--Spooky stuff in the middle-->
	<div class="container-fluid">
		<div class="row">
		<div id="mySidenav" style="padding-right:0; padding-left:0;" class="sidenav darknav">
			<nav style="width:100%;" class="navbar navbar-dark darknav">
			  <div class="container" style="padding-left:0px;">
			  <ul class="nav navbar-nav align-top">
			   <a class="navbar-brand icon" href="#"><img src="../imgs/iconImage.png" alt="icon" width="60" height="60">Chapter <?php if($_SESSION['chapter'] == 2){ echo "<i>Fresh</i>"; }else{ echo "Sweet"; } ?></a>
			   <li class="nav-item"><a class="nav-link" href="../index.php">Dashboard</a></li>
			   <?php
				if($rank == "admin" || $rank == "officer" || $rank == "adviser"){
				?>
			   <li class="nav-item"><a class="nav-link" href="users.php">My Chapter</a></li>
			   <?php
				}
				?>
			   <?php
				if(!($blockedPages == "info" || $blockedPages == "all") || $rank == "admin" || $rank == "adviser"){
				?>
			   <li class="nav-item"><a class="nav-link" href="files.php">Files</a></li>
			   <?php
				}
				?>
			   <?php
				if(!($blockedPages == "info" || $blockedPages == "all") || $rank == "admin" || $rank == "adviser"){
				?>
			   <li class="nav-item"><a class="nav-link" href="rules.php">Event Rules</a></li>
			   <?php
				}
				?>
				<li class="nav-item active"><a class="nav-link" href="#">Event Selection</a></li>
	          	   <?php
				if(!($blockedPages == "info" || $blockedPages == "all") || $rank == "admin" || $rank == "adviser"){
				?>
			   <li class="nav-item"><a class="nav-link" href="secretary.php">Secretary</a></li>
			   <?php
				}
				?>
			   <?php
				 if(($rank == "officer" && ($officerPerm == "all" || $officerPerm == "minutesAnnouncements" || $officerPerm == "filesAnnouncements" || $officerPerm == "announcements")) || $rank == "admin" || $rank == "adviser"){
				 ?>
			    <li class="nav-item"><a class="nav-link" href="reporter.php">Reporter</a></li>
			    <?php
				 }
				 ?>
		           <?php
				 if($rank == "officer" || $rank == "admin" || $rank == "adviser"){ 
				 ?>
			    <li class="nav-item"><a class="nav-link" href="treasurer.php">Treasurer</a></li>
			    <?php
				 }
				 ?>
                           <?php
				 if(!($blockedPages == "info" || $blockedPages == "all") || $rank == "admin" || $rank == "adviser"){
				 ?>
			    <li class="nav-item"><a class="nav-link" href="parli.php">Parliamentarian</a></li>
			    <?php
				 }
				 ?>
			   
			   <li class="nav-item"><a class="nav-link" href="leap.php">LEAP Resume</a></li>
			   <?php
				if($rank == "admin" || $rank == "adviser"){
				?>
			   <li class="nav-item"><a class="nav-link" href="danger.php">Adviser Settings</a></li>
			   <?php
				}
				?>
			  </ul>
			  </div>
			</nav>
		</div>
		<div id="pageBody">
			<div class="row">
				<div class="col-10">
					<p class="pageHeader" style="padding-left:20px;">
						Event Selection
					</p>
					<p class="" style="padding-left:20px;">
						Real-Time <span class="text-primary">Event Signup</span>
					</p>
				</div>
				<div class="col-2">
					<button type="button" class="btn btn-link openHelpModal" data-toggle="modal" data-target="#helpModal">
					  Help
					</button>

					<!-- Help modal -->
					<div class="modal fade" id="helpModal" tabindex="-1" role="dialog" aria-labelledby="helpModalTitle" aria-hidden="true">
					  <div class="modal-dialog" role="document">
					    <div class="modal-content">
					      <div class="modal-header">
					        <h5 class="modal-title" id="helpModalTitle">About Event Selection</h5>
					        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
					          <span aria-hidden="true">&times;</span>
					        </button>
					      </div>
					      <div class="modal-body">
					        The Event Selection page hosts real-time event selection for your chapter, and displays the current breakdown of what users are in what events.
					        <hr>
					        <b>Check Back Later</b><br>
					        This page's help guide is still being written.
					      </div>
					    </div>
					  </div>
					</div>
				</div>
			</div>
		<center>
					<?php
					if($rank == "admin" || $rank == "officer" || $rank == "adviser"){
					?>
						<button onclick="window.print()" class="btn btn-lg btn-primary">Print Sheet</button>
					<?php
					}
					?>
					<?php
					if($rank == "admin" || $rank == "adviser" ||($rank == "officer" && $emailPerm == "yes")){
					?>
						<form method="post" action="../php/confirmEventsEmail.php" style="margin-top:10px;">
							<input type="submit" id="confirmEventsButton" name="confirmEventsButton" value="Email Event Confirmation" class="btn btn-lg btn-primary" />
						</form>
					<?php
					}
					?>
					<p class = "bodyTextType1">

						<?php
						//echo "Event Points : <b>".$_SESSION['eventpoints']."</b>";
						?>
						<div id="eventPoints">Loading Event Points...</div>

					</p><br>
					<?php
						if(isset($fmsg)){
						?>

							<p class = "bodyTextType1">

							<?php
							echo $fmsg;
							?>

							</p><br>

						<?php
						}
					?>
<!--Event Selection Sheet-->
					<?php
						//require('../php/getTeams.php');
					?>
					<div id="changed" style="display:none;"></div>
					<div id="events"><center>Loading Teams...</center></div>
				</center>
			</div>
		</div>
	</div>

<!--Spooky stuff at the bottom-->
	<footer class="darknav">
		<center><p class="bodyTextType2">
			Copyright Joshua Famous 2018
		</p></center>
	</footer>
</body>

<iframe id="hideFrame" name="hideFrame" style="display:none;"></iframe>		
<form action="../php/getTeams.php" target="hideFrame" style="display:none;" name="teamsUpdateForm"></form>

<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
<script src="../js/scripts.js" type="text/javascript"></script>
<script src="../js/teamsUpdate.js" type="text/javascript"></script>

</html>