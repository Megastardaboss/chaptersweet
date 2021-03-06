<?php

session_start();

$username = $_SESSION['username'];
$rank = $_SESSION['rank'];
$fullname = $_SESSION['fullname'];
$chapter = $_SESSION['chapter'];

//get permission settings
require('../php/connect.php');

//INFO POSTING
$query="SELECT value FROM settings WHERE name='officerInfoPermission' AND chapter='$chapter'";

$result = mysqli_query($link, $query);

if (!$result){
	die('Error: ' . mysqli_error($link));
}

//save the result
list($perm) = mysqli_fetch_array($result);
$officerPerm = $perm;

//EMAIL PERM
$query="SELECT value FROM settings WHERE name='officerEmailPermission' AND chapter='$chapter'";

$result = mysqli_query($link, $query);

if (!$result){
	die('Error: ' . mysqli_error($link));
}

//save the result
list($perm) = mysqli_fetch_array($result);
$emailPerm = $perm;

//posting announcements
if(isset($_POST['body'])){

	//variables assignment
	$articleTitle = addslashes($_POST['title']);
	$articleBody = addslashes($_POST['body']);
	$articlePoster = addslashes($_SESSION['fullname']);
	$doMail = $_POST['mail'];

	require('../php/connect.php');

	$query = "INSERT INTO announcements (title, body, poster, date, chapter) VALUES ('$articleTitle', '$articleBody', '$articlePoster', now(), '$chapter')";

	$result = mysqli_query($link, $query);

	if (!$result){
		die('Error: ' . mysqli_error($link));
	}

	//emailing announcements
	if($doMail == "yes"){

		//get users
		$query="SELECT fullname, email FROM users WHERE chapter='$chapter'";

		$result = mysqli_query($link, $query);

		if (!$result){
			die('Error: ' . mysqli_error($link));
		}

		//for each user
		while(list($fullname, $email) = mysqli_fetch_array($result)){

			//actual mail part
			$mailMessage = "
			<html>
			<h1></html> $articleTitle <html></h1>
<p><pre></html>
$articleBody
<html><pre></p>
			<br>
			<p>For more information about your events and various other chapter-related functions, visit <a href='http://chaptersweet.x10host.com'>http://chaptersweet.x10host.com</a>.</p>
			<p>If you have any questions or concerns, contact your advisor.</p>
			<p>This email is automated, do not attempt to respond.</p>
			</html>
			";

			$headers = "MIME-Version: 1.0" . "\r\n";
			$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

			// More headers
			$headers .= 'From: '.$articlePoster.' <chapters@xo7.x10hosting.com>' . "\r\n";

			mail($email,"TSA Chapter Announcement",$mailMessage,$headers);

		}
		$activityForm = "Wrote and Emailed Announcement " . $articleTitle;
	}
	else{
		$activityForm = "Wrote but Did Not Email Announcement " . $articleTitle;
	}
	
	$sql = "INSERT INTO activity (user, activity, date, chapter) VALUES ('$fullname', '$activityForm', now(), '$chapter')";

	if (!mysqli_query($link, $sql)){
		die('Error: ' . mysqli_error($link));
	}

	mysqli_close($link);

	$fmsg =  "Article '".$articleTitle."' Uploaded Successfully!";

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
	<!-- Global site tag (gtag.js) - Google Analytics -->
		<script async src="https://www.googletagmanager.com/gtag/js?id=UA-110539742-3"></script>
		<script>
		  window.dataLayer = window.dataLayer || [];
		  function gtag(){dataLayer.push(arguments);}
		  gtag('js', new Date());
		
		  gtag('config', 'UA-110539742-3');
		</script>

	<title>
		Chapter Sweet
	</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
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
	          	<li class="nav-item"><a class="nav-link" href="eventSelection.php">Event Selection</a></li>   
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
			    <li class="nav-item active"><a class="nav-link" href="reporter.php">Reporter</a></li>
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
						Announcements
					</p>
					<p class="" style="padding-left:20px;">
						Important Messages from the <span class="text-primary">Reporter</span>
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
					        <h5 class="modal-title" id="helpModalTitle">About Announcements</h5>
					        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
					          <span aria-hidden="true">&times;</span>
					        </button>
					      </div>
					      <div class="modal-body">
					        The Announcements page is where officers (primarily the Reporter) and the adviser can send out important information to the chapter.
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
<!--Spooky stuff closer to the middle-->

			<?php
			if(isset($fmsg)){
				?>
					<div style="margin: 15px 15px 15px 15px;" class="alert alert-info" role="alert">
						<button type="button" class="close" data-dismiss="alert" aria-label="Close">
						    <span aria-hidden="true">&times;</span>
						</button>
				  		<p><?php
							echo $fmsg;
						?></p>
					</div>
				<?php
				}
			?>

				<!--ANNOUNCE-->
				<form method="post" id="articleWriteForm">
					<br>
					Title:
					<br>
					<input class="taskFormInput" style="width:800px; height:40px;" type="text" name="title" id="title">
					<br><br>
					Body:
					<br>
					<textarea form="articleWriteForm" cols="110" rows="15" name="body" id="body"></textarea>
					<br><br>
					<?php 
					if(($rank == "officer" && $emailPerm == "yes") || $rank == "admin" || $rank == "adviser"){ ?>
					<select id="mail" name="mail">
							<option value="no">Do Not Email</option>
							<option value="yes">Send As Email</option>
					</select>
					<br><br>
					<?php } ?>
					<input class="btn btn-primary" name="upload" type="submit" class="box" id="upload" value="Post">
				</form>

			</center>

		</div>
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

<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
<script src="../js/scripts.js" type="text/javascript"></script>

</html>