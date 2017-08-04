<?php

session_start();

$username = $_SESSION['username'];
$rank = $_SESSION['rank'];
$grade = $_SESSION['grade'];

?>

<!DOCTYPE html>

<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
<script src="../js/scripts.js" type="text/javascript"></script>

<head>
	<title>
		Chapter Sweet
	</title>
	<link href="../css/main.css" rel="stylesheet" type="text/css" />
</head>

<body>
	<div id="wrapper">
<!--Spooky bar at the top-->
		<header>
				<img src="../imgs/iconImage.png" alt="icon" width="80" height="80" id="iconMain">
				<p class="titleText">
					Chapter Sweet
				</p>
		</header>
<!--Spooky stuff in the middle-->
		<div id="contentPane">
		<table class="columnsTable">
		<tr class="columnsRow">
		<td class="columns">
			<div id="menus">

			<!--Pages Tabs-->
			<form action="eventSelection.php">
    			<input class="bigButton" type="submit" value="Event Selection" />
			</form>
			<br>

			<form action="minutes.php">
    			<input class="bigButton" type="submit" value="Minutes" />
			</form>
			<br>

			<form action="announcements.php">
    			<input class="bigButton" type="submit" value="Announcements" />
			</form>
			<br>

			<?php
			if($rank == "admin"){
			?>
				<form action="assignpoints.php">
	    			<input class="bigButton" type="submit" value="Assign Points" />
				</form>
				<br>
			<?php
			}
			?>

			<form action="../php/logout.php">
    			<input class="bigButton" type="submit" value="Logout" />
			</form>
			<br>

			<?php
			if($rank == "admin"){
			?>
				<form action="danger.php">
	    			<input class="bigButton" style="background-color:#DF2935;" type="submit" value="! Admin Settings !" />
				</form>
				<br>
			<?php
			}
			?>

			</div>
		</td>
		<td class="columns">

			<?php
			if(isset($username) && isset($rank)){
			?>

				<p class = "bodyTextType1">

				<?php
					$article = "a";
					if($rank == "officer" || $rank == "admin"){
						$article = "an";
					}
					echo "Welcome, " . $username . " who is " . $article . " " . $rank . " in grade ".$grade;
				?>

				</p>

			<?php
			}
			?>

		</td>
		</tr>
		</table>
		</div>
<!--Less spooky stuff at the bottom-->
		<footer>
			<center><p class="bodyTextType2">
				© Joshua Famous 2017
			</p></center>
		</footer>
	</div>	
</body>

<script src="../js/menu.js" type="text/javascript"></script>

</html>