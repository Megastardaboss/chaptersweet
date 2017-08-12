<?php

session_start();

$username = $_SESSION['username'];
$rank = $_SESSION['rank'];
$fullname = $_SESSION['fullname'];

if(isset($_POST['upload']) && $_FILES['userfile']['size'] > 0){

	//file details
	$fileName = $_FILES['userfile']['name'];
	$tmpName = $_FILES['userfile']['tmp_name'];
	$fileSize = $_FILES['userfile']['size'];
	$fileType = $_FILES['userfile']['type'];

	//file data manipulation
	$fp = fopen($tmpName, 'r');
	$content = fread($fp, filesize($tmpName));
	$content = addslashes($content);
	fclose($fp);

	if(!get_magic_quotes_gpc()){

		$fileName = addslashes($fileName);

	}

	//file viewality
	$view = $_POST['view'];

	//get poster
	$poster = $_SESSION['fullname'];

	require('../php/connect.php');

	$query = "INSERT INTO minutes (name, size, type, content, date, view, poster) VALUES ('$fileName', '$fileSize', '$fileType', '$content', now(), '$view', '$poster')";

	$result = mysql_query($query);

	if (!$result){
		die('Error: ' . mysql_error());
	}

	mysql_close();

	$fmsg =  "File ".$fileName." Uploaded Successfully!";

}

?>

<!DOCTYPE html>

<head>
	<title>
		Chapter Sweet
	</title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<link href="../css/main.css" rel="stylesheet" type="text/css" />
</head>

<body>

	<div id="wrapper">
<!--Spooky stuff at the top-->
		<header>
				<img src="../imgs/iconImage.png" alt="icon" width="80" height="80" id="iconMain">
				<p class="titleText">
					Chapter Sweet
				</p>
		</header>
<!--Spooky stuff still kind of at the top-->
		<div id="subTitleBar">
			<form action="../index.php">
    			<input class="backButton" type="submit" value="Back" />
			</form>
			<center><p class="subTitleText">
				Information
			</p></center>
		</div>
<!--Spooky stuff closer to the middle-->
			<div id="contentPane">

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

				<!--INFO TYPES LINKS-->
				<center>
				<div class="iconLinks">

				<!--Files-->
					<span onclick="showFiles();"><a href="#"><img src="../imgs/icon_files.png" height="64" width="64"><p class="bodyTextType1">Files</p></a></span>
				<!--Minutes-->
					<span onclick="showMinutes();"><a href="#"><img src="../imgs/icon_minutes.png" height="64" width="64"><p class="bodyTextType1">Minutes</p></a></span>
				<!--Announcements-->
					<span onclick="showAnnouncements();"><a href="#"><img src="../imgs/icon_announcements.png" height="64" width="64"><p class="bodyTextType1">Announcements</p></a></span>

				</div>

				<!--FILES-->
				<div id="filesDiv">

				<div class="userDashHeader" style="width:80%;">
					<p class="subTitleText" style="padding-top:15px">Files</p>
				</div>

				<!--Description-->
				<p class="bodyTextType1">
					Here you can view all of your chapter's important files. Officers and Admins can upload new files.
				</p>

				<!--
					<form method="post" enctype="multipart/form-data">
						<br>
						<input type="hidden" name="MAX_FILE_SIZE" value="2000000">
						<input class="taskFormInput" name="userfile" type="file" id="userfile">
						<br><br>
						Who Can View :
						<select id="view" name="view">
							<option value="all">All</option>
							<option value="officer">Officers Only</option>
						</select>
						<br><br>
						<input class="submitButton" name="upload" type="submit" class="box" id="upload" value="Upload">
					</form>
				-->

				<br>
				<br>

				<?php

				require('../php/connect.php');

				$query="SELECT id, name, date, view, poster FROM minutes WHERE class='file'";

				$result = mysql_query($query);

				if (!$result){
					die('Error: ' . mysql_error());
				}

				$doMemberSkip = 0;

				if(mysql_num_rows($result) == 0){
					echo "No Files Found!<br>";
				}
				else{
					//FOR MEMBERS - check if all available files are hidden
					if($rank == "member"){

						$viewLevel = "all";

						$query2="SELECT id, view FROM minutes WHERE view='$viewLevel'";

						$result2 = mysql_query($query2);

						if (!$result2){
							die('Error: ' . mysql_error());
						}

						if(mysql_num_rows($result2) == 0){
							$doMemberSkip = 1;
						}

					}

					if($doMemberSkip == 1){
							echo "No Files Found!<br>";
					}
					else{
						while(list($id, $name, $date, $view, $poster) = mysql_fetch_array($result)){
							if(($view == "officer" && ($rank == "officer" || $rank == "admin")) || ($view == "all")){
								?>
							<a class="minutesLink" href="../php/download.php?id=<?php echo "".$id ?>" style="float:left; padding-left: 25%;"><?php echo "".$name ?></a>
							<?php
							if($view == "officer"){ ?>
									<p style="float:left; padding-left: 10%;">Private</p>
								<?php } ?>
							<p style="float:right; padding-right: 25%;"><?php echo "".$date ?></p>
							<p style="float:right; padding-right: 10%;"><?php echo "".$poster ?></p>
							<br>
							
							<?php
							}
						}
					}
				}
						
				mysql_close();

				?>

				</div>

				<!--MINUTES-->
				<div id="minutesDiv">

				</div>

				<!--ANNOUNCEMENTS-->
				<div id="announcementsDiv">

				</div>


			</div>

<!--Spooky stuff at the bottom-->
		<footer>
			<center><p class="bodyTextType2">
				© Joshua Famous 2017
			</p></center>
		</footer>
	</div>	
</body>

<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
<script src="../js/scripts.js" type="text/javascript"></script>

</html>