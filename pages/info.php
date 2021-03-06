<?php

session_start();

$username = $_SESSION['username'];
$rank = $_SESSION['rank'];
$fullname = $_SESSION['fullname'];

//function to get chapter balance
function getChapterBalance()
{
	$returnValue = 0;
	require('../php/connect.php');
	$transQ = "SELECT personto, personfrom, description, amount, date FROM transactions";
	$transR = mysqli_query($link, $transQ);
	if (!$transR){
		die('Error: ' . mysqli_error($link));
	}
	while($row = mysqli_fetch_array($transR)){
		if($row['personto'] == 'Chapter'){
			$returnValue += $row['amount'];
		}
		if($row['personfrom'] == 'Chapter'){
			$returnValue -= $row['amount'];
		}
	}
	return $returnValue;
}

//get permission settings
require('../php/connect.php');

//INFO POSTING
$query="SELECT value FROM settings WHERE name='officerInfoPermission'";

$result = mysqli_query($link, $query);

if (!$result){
	die('Error: ' . mysqli_error($link));
}

//save the result
list($perm) = mysqli_fetch_array($result);
$officerPerm = $perm;

//EMAIL
$query="SELECT value FROM settings WHERE name='officerEmailPermission'";

$result = mysqli_query($link, $query);

if (!$result){
	die('Error: ' . mysqli_error($link));
}

//save the result
list($perm) = mysqli_fetch_array($result);
$emailPerm = $perm;

//file uploading
if(isset($_POST['uploadFile']) && $_FILES['userfile']['size'] > 0){

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

	$result = mysqli_query($link, $query);

	if (!$result){
		die('Error: ' . mysqli_error($link));
	}

	mysqli_close($link);

	$fmsg =  "File ".$fileName." Uploaded Successfully!";

}

//event rules uploading
if(isset($_POST['uploadRules']) && $_FILES['userfile']['size'] > 0){

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
	
	$class = "rules";

	require('../php/connect.php');

	$query = "INSERT INTO minutes (name, size, type, content, date, view, poster, class) VALUES ('$fileName', '$fileSize', '$fileType', '$content', now(), '$view', '$poster', '$class')";

	$result = mysqli_query($link, $query);

	if (!$result){
		die('Error: ' . mysqli_error($link));
	}

	mysqli_close($link);

	$fmsg =  "Rules File ".$fileName." Uploaded Successfully!";

}

if(isset($_POST['uploadMinutes']) && $_FILES['userfile']['size'] > 0){

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
	$class = "minutes";

	//get poster
	$poster = $_SESSION['fullname'];

	require('../php/connect.php');

	$query = "INSERT INTO minutes (name, size, type, content, date, view, poster, class) VALUES ('$fileName', '$fileSize', '$fileType', '$content', now(), '$view', '$poster', '$class')";

	$result = mysqli_query($link, $query);

	if (!$result){
		die('Error: ' . mysqli_error($link));
	}

	mysqli_close($link);

	$fmsg =  "File ".$fileName." Uploaded Successfully!";

}

//posting announcements
if(isset($_POST['body'])){

	//variables assignment
	$articleTitle = addslashes($_POST['title']);
	$articleBody = addslashes($_POST['body']);
	$articlePoster = addslashes($_SESSION['fullname']);
	$doMail = $_POST['mail'];

	require('../php/connect.php');

	$query = "INSERT INTO announcements (title, body, poster, date) VALUES ('$articleTitle', '$articleBody', '$articlePoster', now())";

	$result = mysqli_query($link, $query);

	if (!$result){
		die('Error: ' . mysqli_error($link));
	}

	//emailing announcements
	if($doMail == "yes"){

		//get users
		$query="SELECT fullname, email FROM users";

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

	}

	mysqli_close($link);

	$fmsg =  "Article '".$articleTitle."' Uploaded Successfully!";

}

//handling transactions
if(isset($_POST['amount'])){

	//variables assignment
	$personfrom = $_POST['personfrom'];
	$personto = $_POST['personto'];
	$amount = $_POST['amount'];
	$description = addslashes($_POST['description']);

	require('../php/connect.php');

	//get real name of person to
	if($personto != "expense" && $personto != "chapter"){

		$nameQuery = "SELECT fullname FROM users WHERE id='$personto'";

		$nameResult = mysqli_query($link, $nameQuery);

		if (!$nameResult){
			die('Error: ' . mysqli_error($link));
		}

		list($realNameTo) = mysqli_fetch_array($nameResult);

	}
	else if($personto == "expense"){
		$realNameTo = "Expense";
	}
	else if($personto == "chapter"){
		$realNameTo = "Chapter";
	}

	//get real name of person from
	if($personfrom != "income" && $personfrom != "chapter"){

		$nameQuery = "SELECT fullname FROM users WHERE id='$personfrom'";

		$nameResult = mysqli_query($link, $nameQuery);

		if (!$nameResult){
			die('Error: ' . mysqli_error($link));
		}

		list($realNameFrom) = mysqli_fetch_array($nameResult);

	}
	else if($personfrom == "income"){
		$realNameFrom = "Income";
	}
	else if($personfrom == "chapter"){
		$realNameFrom = "Chapter";
	}

	//make the transaction
	$query = "INSERT INTO transactions (personto, personfrom, description, amount, date) VALUES ('$realNameTo', '$realNameFrom', '$description', '$amount', now())";

	$result = mysqli_query($link, $query);

	if (!$result){
		die('Error: ' . mysqli_error($link));
	}

	//update balances
	if($personto != "expense" && $personto != "chapter"){

		$query2 = "UPDATE users SET balance=balance+'$amount' WHERE id='$personto'";

		$result2 = mysqli_query($link, $query2);

		if (!$result2){
			die('Error: ' . mysqli_error($link));
		}

	}
	if($personfrom != "income" && $personfrom != "chapter"){

		$query3 = "UPDATE users SET balance=balance-'$amount' WHERE id='$personfrom'";

		$result3 = mysqli_query($link, $query3);

		if (!$result3){
			die('Error: ' . mysqli_error($link));
		}

	}

	mysqli_close($link);

	$fmsg =  "Transaction of ".$amount." Completed Successfully!";

}

?>

<!DOCTYPE html>

<head>
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

	<div id="wrapper">
<!--Spooky stuff at the top-->
		<header>
				<img src="../imgs/iconImage.png" alt="icon" width="80" height="80" id="iconMain">
				<p class="titleText">
					Chapter <?php if($_SESSION['chapter'] == 'freshman'){ echo "<i>Fresh</i>"; }else{ echo "Sweet"; } ?>
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
			<div id="contentPane" style="overflow:hidden">

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
				<!--Rules-->
					<span onclick="showRules();"><a href="#"><img src="../imgs/folder.png" height="64" width="64"><p class="bodyTextType1">Event Rules</p></a></span>
				<!--Minutes-->
					<span onclick="showMinutes();"><a href="#"><img src="../imgs/icon_minutes.png" height="64" width="64"><p class="bodyTextType1">Secretary</p></a></span>
				<!--Announcements-->
					<span onclick="showAnnouncements();"><a href="#"><img src="../imgs/icon_announcements.png" height="64" width="64"><p class="bodyTextType1">Announcements</p></a></span>
				<!--Announce-->
				<?php if(($rank == "officer" && ($officerPerm == "all" || $officerPerm == "minutesAnnouncements" || $officerPerm == "filesAnnouncements" || $officerPerm == "announcements")) || $rank == "admin" || $rank == "adviser"){ ?>
					<span onclick="showPost();"><a href="#"><img src="../imgs/icon_announce.png" height="64" width="64"><p class="bodyTextType1">Reporter</p></a></span>
				<?php } ?>
				<!--Audit-->
				<?php if($rank == "officer" || $rank == "admin" || $rank == "adviser"){ ?>
					<span onclick="showAudit();"><a href="#"><img src="../imgs/wallet.png" height="64" width="64"><p class="bodyTextType1">Treasurer</p></a></span>
				<?php } ?>
				<!--parli pro-->
					<span onclick="showParliamentarian();"><a href="#"><img src="../imgs/icon_parli.png" height="64" width="64"><p class="bodyTextType1">Parliamentarian</p></a></span>

				</div>

				<!--FILES-->
				<div id="filesDiv" class="infoTab">

				<div class="userDashHeader" style="width:80%;">
					<p class="subTitleText" style="padding-top:15px">Files</p>
				</div>

				<!--Description-->
				<p class="bodyTextType1">
					Here you can view all of your chapter's important files. Officers and Advisers can upload new files.
				</p>

				<?php if(($rank == "officer" && ($officerPerm == "all" || $officerPerm == "minutesFiles" || $officerPerm == "filesAnnouncements" || $officerPerm == "files")) || $rank == "admin" || $rank == "adviser"){ ?>
					<form method="post" enctype="multipart/form-data" class="fileForm">
						<input type="hidden" name="MAX_FILE_SIZE" value="2000000">
						<span><input style="font-size:16px; border:1px solid #B60000;" name="userfile" type="file" id="userfile"></span>
						<span>Who Can View :
						<select id="view" name="view">
							<option value="all">All</option>
							<option value="officer">Officers Only</option>
						</select></span>
						<span><input class="submitButton" style="width:100px;height:30px;font-size:16px;" name="uploadFile" type="submit" class="box" id="uploadFile" value="Upload"></span>
					</form>
				<?php } ?>

				<br>
				<br>

				<?php

				require('../php/connect.php');

				$query="SELECT id, name, date, view, poster FROM minutes WHERE class='file'";

				$result = mysqli_query($link, $query);

				if (!$result){
					die('Error: ' . mysqli_error($link));
				}

				$doMemberSkip = 0;

				if(mysqli_num_rows($result) == 0){
					echo "No Files Found!<br>";
				}
				else{
					//FOR MEMBERS - check if all available files are hidden
					if($rank == "member"){

						$viewLevel = "all";

						$query2="SELECT id, view FROM minutes WHERE view='$viewLevel'";

						$result2 = mysqli_query($link, $query2);

						if (!$result2){
							die('Error: ' . mysqli_error($link));
						}

						if(mysqli_num_rows($result2) == 0){
							$doMemberSkip = 1;
						}

					}

					if($doMemberSkip == 1){
							echo "No Files Found!<br>";
					}
					else{
						while(list($id, $name, $date, $view, $poster) = mysqli_fetch_array($result)){
							if(($view == "officer" && ($rank == "officer" || $rank == "admin" || $rank == "adviser")) || ($view == "all")){
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
						
				mysqli_close($link);

				?>

				</div>
				
				<!--RULES-->
				<div id="rulesDiv" style="display:none;" class="infoTab">

				<div class="userDashHeader" style="width:80%;">
					<p class="subTitleText" style="padding-top:15px">Event Rules</p>
				</div>

				<!--Description-->
				<p class="bodyTextType1">
					Here you can view the rules for events. Officers and Advisers can upload new rules files.
				</p>

				<?php if($rank == "admin" || $rank == "adviser"){ ?>
					<form method="post" enctype="multipart/form-data" class="fileForm">
						<input type="hidden" name="MAX_FILE_SIZE" value="2000000">
						<span><input style="font-size:16px; border:1px solid #B60000;" name="userfile" type="file" id="userfile"></span>
						<span>Who Can View :
						<select id="view" name="view">
							<option value="all">All</option>
						</select></span>
						<span><input class="submitButton" style="width:100px;height:30px;font-size:16px;" name="uploadRules" type="submit" class="box" id="uploadRules" value="Upload"></span>
					</form>
				<?php } ?>

				<br>
				<br>

				<?php

				require('../php/connect.php');

				$query="SELECT id, name, date, view, poster FROM minutes WHERE class='rules'";

				$result = mysqli_query($link, $query);

				if (!$result){
					die('Error: ' . mysqli_error($link));
				}

				$doMemberSkip = 0;

				if(mysqli_num_rows($result) == 0){
					echo "No Files Found!<br>";
				}
				else{
					//FOR MEMBERS - check if all available files are hidden
					if($rank == "member"){

						$viewLevel = "all";

						$query2="SELECT id, view FROM minutes WHERE view='$viewLevel'";

						$result2 = mysqli_query($link, $query2);

						if (!$result2){
							die('Error: ' . mysqli_error($link));
						}

						if(mysqli_num_rows($result2) == 0){
							$doMemberSkip = 1;
						}

					}

					if($doMemberSkip == 1){
							echo "No Files Found!<br>";
					}
					else{
						while(list($id, $name, $date, $view, $poster) = mysqli_fetch_array($result)){
							if(($view == "officer" && ($rank == "officer" || $rank == "admin" || $rank == "adviser")) || ($view == "all")){
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
						
				mysqli_close($link);

				?>

				</div>

				<!--MINUTES-->
				<div id="minutesDiv" style="display:none;" class="infoTab">

				<div class="userDashHeader" style="width:80%;">
					<p class="subTitleText" style="padding-top:15px">Minutes</p>
				</div>

				<!--Description-->
				<p class="bodyTextType1">
					Here you can view the minutes of chapter meetings. The secretary can upload minutes here.
				</p>

				<?php if(($rank == "officer" && ($officerPerm == "all" || $officerPerm == "minutesFiles" || $officerPerm == "minutesAnnouncements" || $officerPerm == "minutes")) || $rank == "admin" || $rank == "adviser"){ ?>
					<form method="post" enctype="multipart/form-data" class="fileForm">
						<input type="hidden" name="MAX_FILE_SIZE" value="2000000">
						<span><input style="font-size:16px; border:1px solid #B60000;" name="userfile" type="file" id="userfile"></span>
						<span>Who Can View :
						<select id="view" name="view">
							<option value="all">All</option>
							<option value="officer">Officers Only</option>
						</select></span>
						<span><input class="submitButton" style="width:100px;height:30px;font-size:16px;" name="uploadMinutes" type="submit" class="box" id="uploadMinutes" value="Upload"></span>
					</form>
				<?php } ?>

				<br>
				<br>

				<?php

				require('../php/connect.php');

				$query="SELECT id, name, date, view, poster FROM minutes WHERE class='minutes'";

				$result = mysqli_query($link, $query);

				if (!$result){
					die('Error: ' . mysqli_error($link));
				}

				$doMemberSkip = 0;

				if(mysqli_num_rows($result) == 0){
					echo "No Minutes Found!<br>";
				}
				else{
					//FOR MEMBERS - check if all available files are hidden
					if($rank == "member"){

						$viewLevel = "all";

						$query2="SELECT id, view FROM minutes WHERE view='$viewLevel'";

						$result2 = mysqli_query($link, $query2);

						if (!$result2){
							die('Error: ' . mysqli_error($link));
						}

						if(mysqli_num_rows($result2) == 0){
							$doMemberSkip = 1;
						}

					}

					if($doMemberSkip == 1){
							echo "No Minutes Found!<br>";
					}
					else{
						while(list($id, $name, $date, $view, $poster) = mysqli_fetch_array($result)){
							if(($view == "officer" && ($rank == "officer" || $rank == "admin" || $rank == "adviser")) || ($view == "all")){
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
						
				mysqli_close($link);

				?>

				</div>

				<!--ANNOUNCEMENTS-->
				<div id="announcementsDiv" style="display:none;" class="infoTab">

				<div class="userDashHeader" style="width:80%;">
					<p class="subTitleText" style="padding-top:15px">Announcements</p>
				</div>

				<!--Description-->
				<p class="bodyTextType1">
					Here you can view all of your chapter's announcements.
				</p>

				<div style="text-align: left;">
				<?php

				require('../php/connect.php');

				$query="SELECT * FROM announcements ORDER BY id DESC";

				$result = mysqli_query($link, $query);

				if (!$result){
					die('Error: ' . mysqli_error($link));
				}		

				if(mysqli_num_rows($result) == 0){
					echo "No Articles Found!<br>";
				}
				else{
					while(list($id, $title, $body, $poster, $date) = mysqli_fetch_array($result)){
						?>

						<p style="font-weight: bold; font-family:tahoma; font-size:24px; padding-left:15%; padding-top:10px;"><?php echo "".$title ?></p>
						<p style="font-size:14px; font-family:tahoma; padding-left:15%; padding-top:10px;"><?php echo "By : ".$poster ?></p>
						<p style="font-size:14px; font-family:tahoma; padding-left:15%; padding-top:10px;"><?php echo "".$date ?></p>
						<br><br>
						<pre>
						<p style="font-size:12px; font-family:tahoma; padding-left:20%; padding-top:10px; padding-bottom: 10px;">
<?php echo "".$body ?>
						</p>
						</pre>
						
						<?php
					}
				}
						
				mysqli_close($link);

				?>
				</div>

				</div>
				
				<!--ANNOUNCE-->
				<div id="postDiv" style="display:none;" class="infoTab">

				<div class="userDashHeader" style="width:80%;">
					<p class="subTitleText" style="padding-top:15px">Post Announcement</p>
				</div>

				<!--Description-->
				<p class="bodyTextType1">
					Officers and Advisers can write and post announcements here.
				</p>

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
					<input class="submitButton" name="upload" type="submit" class="box" id="upload" value="Post">
				</form>

				</div>

				<!--AUDIT-->
				<div id="auditDiv" style="display:none;" class="infoTab">

				<div class="userDashHeader" style="width:80%;">
					<p class="subTitleText" style="padding-top:15px">Ledger</p>
				</div>

				<!--Description-->
				<p class="bodyTextType1">
					Officers and Advisers can view the ledger and transactions, and make withdrawals and deposits here.
				</p>
				
				<a download="ledger.txt" id="downloadlink">Download Ledger</a><br><br>
				
				<p style="display:none;" id='auditText'>
				<?php
				
				require('../php/connect.php');
				
				$descriptionsIncomeC = array();
				$descriptionsExpenseC = array();
				$incomeSumC = 0.00;
				$expenseSumC = 0.00;
				
				$descriptionsIncomeM = array();
				$descriptionsExpenseM = array();
				$incomeSumM = 0.00;
				$expenseSumM = 0.00;
			
				$catsQ = "SELECT personto, personfrom, description, amount, date FROM transactions";
				$catsR = mysqli_query($link, $catsQ);
				if (!$catsR){
					die('Error: ' . mysqli_error($link));
				}
			
				while($row = mysqli_fetch_array($catsR)){
					//chapter balance
					if($row['personto'] == "Chapter"){
						$incomeSumC += $row['amount'];
						if(!array_key_exists($row['description'], $descriptionsIncomeC)){
							$descriptionsIncomeC[$row['description']] = $row['amount'];
						}
						else{
							$descriptionsIncomeC[$row['description']] = $descriptionsIncomeC[$row['description']] + $row['amount'];
						}
					}
					else if($row['personfrom'] == "Chapter"){
						$expenseSumC += $row['amount'];
						if(!array_key_exists($row['description'], $descriptionsExpenseC)){
							$descriptionsExpenseC[$row['description']] = $row['amount'];
						}
						else{
							$descriptionsExpenseC[$row['description']] = $descriptionsExpenseC[$row['description']] + $row['amount'];
						}
					}
					//member balance
					if($row['personto'] != "Chapter" && $row['personto'] != "Expense"){
						$incomeSumM += $row['amount'];
						if(!array_key_exists($row['description'], $descriptionsIncomeM)){
							$descriptionsIncomeM[$row['description']] = $row['amount'];
						}
						else{
							$descriptionsIncomeM[$row['description']] = $descriptionsIncomeM[$row['description']] + $row['amount'];
						}
					}
					else if($row['personfrom'] != "Chapter" && $row['personfrom'] != "Income"){
						$expenseSumM += $row['amount'];
						if(!array_key_exists($row['description'], $descriptionsExpenseM)){
							$descriptionsExpenseM[$row['description']] = $row['amount'];
						}
						else{
							$descriptionsExpenseM[$row['description']] = $descriptionsExpenseM[$row['description']] + $row['amount'];
						}
					}
				}
				
				echo "Chapter Audit";
				echo "\n";
				echo "\t\t\t\t";
				echo date('Y-m-d H:i:s');
				echo "\n";
				echo "\t\t\t\t";
				echo "Generated By : " . $fullname;
				echo "\n------------------------------------------------------------------------------------\nChapter Account\n------------------------------------------------------------------------------------";
				echo "\n";
				echo "\t\tIncome";
				foreach ($descriptionsIncomeC as $key => $value) {
				    echo "\n";
				    echo "\t\t\t";
				    echo "{$key} : \${$value}";
				}
				echo "\n\n";
				echo "\t\tExpenses";
				foreach ($descriptionsExpenseC as $key => $value) {
				    echo "\n";
				    echo "\t\t\t";
				    echo "{$key} : \${$value}";
				}
				echo "\n\n";
				echo "\t\tTotals";
				echo "\n";
				echo "\t\t\t";
				echo "Income : $" . $incomeSumC;
				echo "\n";
				echo "\t\t\t";
				echo "Expense : $" . $expenseSumC;
				echo "\n";
				echo "\t\t\t";
				echo "Change : $" . ($incomeSumC - $expenseSumC);
				echo "\n------------------------------------------------------------------------------------\nMember Accounts\n------------------------------------------------------------------------------------";
				echo "\n";
				echo "\t\tIncome";
				foreach ($descriptionsIncomeM as $key => $value) {
				    echo "\n";
				    echo "\t\t\t";
				    echo "{$key} : \${$value}";
				}
				echo "\n\n";
				echo "\t\tExpenses";
				foreach ($descriptionsExpenseM as $key => $value) {
				    echo "\n";
				    echo "\t\t\t";
				    echo "{$key} : \${$value}";
				}
				echo "\n\n";
				echo "\t\tTotals";
				echo "\n";
				echo "\t\t\t";
				echo "Income : $" . $incomeSumM;
				echo "\n";
				echo "\t\t\t";
				echo "Expense : $" . $expenseSumM;
				echo "\n";
				echo "\t\t\t";
				echo "Change : $" . ($incomeSumM - $expenseSumM);
				echo "\n------------------------------------------------------------------------------------\nTotal\n------------------------------------------------------------------------------------";
				echo "\n";
				echo "\t\t\t";
				echo "Chapter Balance : $" . (getChapterBalance()) . "";
				echo "\n";
				echo "\t\t\t";
				//get total user balance
				require('../php/connect.php');

				$query="SELECT SUM(balance) FROM users";

				$result = mysqli_query($link, $query);

				if (!$result){
					die('Error: ' . mysqli_error($link));
				}
				list($cumBalance) = mysqli_fetch_array($result);
				echo "Cumulative User Balances : $" . $cumBalance;
				echo "\n";
				echo "\t\t\t";
				echo "Total Balance : $" . ($cumBalance + getChapterBalance());
				
				?>
				</p>
				
				<script>
				var textFile = null;
				function makeTextFile(text) {
				  text = text.replace(/\n/g, '\r\n');
				  var data = new Blob([text], {type: 'text/plain'});
				
				  // If we are replacing a previously generated file we need to
				  // manually revoke the object URL to avoid memory leaks.
				  if (textFile !== null) {
				    window.URL.revokeObjectURL(textFile);
				  }
				
				  textFile = window.URL.createObjectURL(data);
				
				  return textFile;
				}
				var auditTx = document.getElementById('auditText').innerHTML;
				document.getElementById('downloadlink').href = makeTextFile(auditTx);
				</script>

				<form method="post" class="fileForm">
					<span>$Amount : <input style="font-size:16px; border:1px solid #B60000;" name="amount" type="number" id="amount" value="<?php echo isset($_POST['amount']) ? $_POST['amount'] : '' ?>"></span>
					<span>From :
					<!--Give each user as an option-->
					<select id="personfrom" name="personfrom">
						<option value="income">Income</option>
						<option value="chapter">Chapter</option>
						<?php

						require('../php/connect.php');

						$query="SELECT id, fullname, rank FROM users ORDER BY fullname ASC";

						$result = mysqli_query($link, $query);

						if (!$result){
							die('Error: ' . mysqli_error($link));
						}	

						while(list($id, $personname, $personrank) = mysqli_fetch_array($result)){
							if($personrank != "admin"){
							?>

							<option value="<?php echo $id ?>"><?php echo $personname ?></option>
							
							<?php
							}
						}
								
						mysqli_close($link);

						?>
					</select></span>
					<span>To :
					<!--Give each user as an option-->
					<select id="personto" name="personto">
						<option value="expense">Expense</option>
						<option value="chapter">Chapter</option>
						<?php

						require('../php/connect.php');

						$query="SELECT id, fullname, rank FROM users ORDER BY fullname ASC";

						$result = mysqli_query($link, $query);

						if (!$result){
							die('Error: ' . mysqli_error($link));
						}	

						while(list($id, $personname, $personrank) = mysqli_fetch_array($result)){
							if($personrank != "admin"){
							?>

							<option value="<?php echo $id ?>"><?php echo $personname ?></option>
							
							<?php
							}
						}
								
						mysqli_close($link);

						?>
					</select></span>
					<span>Description : 
					<input style="font-size:14px; border:1px solid #B60000;" name="description" type="text" id="description" value="<?php echo isset($_POST['description']) ? $_POST['description'] : '' ?>"></span>
					<span><input class="submitButton" style="width:100px;height:30px;font-size:16px;" name="transact" type="submit" class="box" id="transact" value="Transact"></span>
				</form>

				<br><br>

				<b><p style="font-size:14px; font-family:tahoma; padding-top:10px;"><?php echo "Chapter Balance : $" . getChapterBalance(); ?></p></b>

				<?php

				//SECOND THING - TRANSACTIONS

				require('../php/connect.php');

				$query="SELECT * FROM transactions ORDER BY id DESC";

				$result = mysqli_query($link, $query);

				if (!$result){
					die('Error: ' . mysqli_error($link));
				}		

				if(mysqli_num_rows($result) == 0){
					echo "No Transactions Found!<br>";
				}
				else{
					while(list($id, $personto, $personfrom, $description, $amount, $date) = mysqli_fetch_array($result)){
						?>

						<div class="basicHoverDiv" style="overflow:hidden;">
						<div class="basicSpanDiv" style="width:100%;">
							<span><p style="font-size:14px; font-family:tahoma; padding-left:15%; padding-top:10px;"><?php echo "From : ".$personfrom ?></p></span>
							<span><p style="font-size:14px; font-family:tahoma; padding-left:15%; padding-top:10px;"><?php echo "To : ".$personto ?></p></span>
							<span><p style="font-size:14px; font-family:tahoma; padding-left:15%; padding-top:10px;"><?php echo "$".$amount ?></p></span>
							<span><p style="font-size:14px; font-family:tahoma; padding-left:15%; padding-top:10px;"><?php echo "On : ".$date ?></p></span>
						</div>
						<center><p style="font-size:14px; font-family:tahoma; padding-top:10px;"><?php echo $description ?></p></center>
						</div>
						
						<?php
					}
				}
						
				mysqli_close($link);

				?>

				</div>
				
				<!--PARLI PRO-->
				<div id="parliDiv" style="display:none;" class="infoTab">

				<div class="userDashHeader" style="width:80%;">
					<p class="subTitleText" style="padding-top:15px">Parliamentarian</p>
				</div>

				<!--Description-->
				<p class="bodyTextType1">
					Here the Chapter Team can share study resources and tests.
				</p>
				
				<center>
				
				<b><p class="bodyTextType1">Helpful Guides : </p></b><br>
				
				<a href="https://docs.google.com/presentation/d/19JnTf9YjODwRgyt2N4jIxER_rYEQZaEyjZtwk_zvyRs/edit?usp=sharing">State Guide</a><br>
				<a href="http://tsaweb.org/sites/default/files/Parliamentary_Procedure_Basics.pptx">National Beginner Guide</a><br>
				<a href="http://tsaweb.org/sites/default/files/Parliamentary_Procedure_Advanced.pptx">National Advanced Guide</a><br>
				
				<br>
				
				<b><p class="bodyTextType1">The Latest Test : </p></b>

				<iframe src="https://docs.google.com/forms/d/e/1FAIpQLSdvGr8_rHFdb60GTzJmlFz3xDlco_eTw4cdbCShC1kVBCiZ6g/viewform?embedded=true" width="760" height="500" frameborder="0" marginheight="0" marginwidth="0">Loading...</iframe>
				
				</center>

				</div>

			</center>
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