<?php
session_start();
$rank = $_SESSION['rank'];
$daUsah = $_SESSION['eventsUser'];
$thisrank = $_SESSION['eventsUserRank'];
$chapter = $_SESSION['chapter'];

//get events for removal
require('../php/connect.php');

//current ID
$query="SELECT idnumber FROM users WHERE fullname='$daUsah' AND chapter='$chapter'";

$result = mysqli_query($link, $query);

if (!$result){
	die('Error: ' . mysqli_error($link));
}

//save the result
list($idnumber) = mysqli_fetch_array($result);

//EVENT POINTS for OFFICERS
$query="SELECT value FROM settings WHERE name='eventpointsPermission' AND chapter='$chapter'";

$result = mysqli_query($link, $query);

if (!$result){
	die('Error: ' . mysqli_error($link));
}

//save the result
list($perm) = mysqli_fetch_array($result);
$eventPointsPerm = $perm;

//Event Removal Permission for OFFICERS
$query="SELECT value FROM settings WHERE name='eventRemovalPermission' AND chapter='$chapter'";

$result = mysqli_query($link, $query);

if (!$result){
	die('Error: ' . mysqli_error($link));
}

//save the result
list($perm) = mysqli_fetch_array($result);
$removalPerm = $perm;

//ID Changing Permission for OFFICERS
$query="SELECT value FROM settings WHERE name='idPermission' AND chapter='$chapter'";

$result = mysqli_query($link, $query);

if (!$result){
	die('Error: ' . mysqli_error($link));
}

//save the result
list($perm) = mysqli_fetch_array($result);
$idPerm = $perm;

//get user's events
$queryid="SELECT id FROM users WHERE fullname='$daUsah' AND chapter='$chapter'";

$resultid = mysqli_query($link, $queryid);

if (!$resultid){
	die('Error: ' . mysqli_error($link));
}

//show each event as option	
while(list($hold) = mysqli_fetch_array($resultid)){
	$daID = $hold;
}

//get user's events
$queryid="SELECT username FROM users WHERE fullname='$daUsah' AND chapter='$chapter'";

$resultid = mysqli_query($link, $queryid);

if (!$resultid){
	die('Error: ' . mysqli_error($link));
}

//show each event as option	
while(list($theusername) = mysqli_fetch_array($resultid)){
	$daUsername = $theusername;
}

$out = "";
$out = $out .  '<div class="modal-dialog modal-lg">';
$out = $out .  '<div class="modal-content">';
$out = $out .  '<div class="modal-header">';
$out = $out .  '<h4 class="modal-title">' . $_SESSION['eventsUser'] . '</h4>';
$out = $out .  '<button type="button" id="closeModalButton" class="close" data-dismiss="modal">&times;</button>';
$out = $out .  '</div>';
$out = $out .  '<div class="modal-body">';
//account info
if($rank == "admin" || $rank == "adviser" || ($rank == "officer" && $idPerm == "yes")){
	$out = $out .  '<div class="adminDataSection" id="userEvents" style="margin-bottom:15px;">';
	$out = $out .  '<p class="userDashSectionHeader" style="padding-left:0px;">Account Information</p><br>';
	//username setting
	if($rank == "admin" || $rank == "adviser"){
		$out = $out .  '<form class="basicSpanDiv" method="post" style="width:100%; height:40px; padding-top:15px;">';
			$out = $out .  '<span>';
				$out = $out .  '<b>Edit Username</b>';
			$out = $out .  '</span>';
				$out = $out .  "<input type='hidden' name='usernameFieldUserID' value='" . $daID . "'>";
			$out = $out .  '<span>';
				$out = $out .  '<input type="text" id="usernameFieldNewname" name="usernameFieldNewname" value="' . $daUsername . '">';
			$out = $out .  '</span>';
			$out = $out .  '<span>';
				$out = $out .  '<input type="submit" class="btn btn-primary" value="Set Username">';
			$out = $out .  '</span>';
		$out = $out .  '</form>';
	}
	//set id
	$out = $out .  '<form class="basicSpanDiv" method="post" style="width:100%; height:40px; padding-top:15px;">';
		$out = $out .  '<span>';
			$out = $out .  '<b>Edit Individual ID</b>';
		$out = $out .  '</span>';
			$out = $out .  "<input type='hidden' name='idname' value='" . $daID . "'>";
		$out = $out .  '<span>';
			$out = $out .  '<input type="number" id="idnum" name="idnum" value="' . $idnumber . '">';
		$out = $out .  '</span>';
		$out = $out .  '<span>';
			$out = $out .  '<input type="submit" class="btn btn-primary" value="Set ID">';
		$out = $out .  '</span>';
	$out = $out .  '</form>';
	//set rank
	if($rank == "admin" || $rank == "adviser"){
		if($thisrank != "admin" && $thisrank != "adviser"){
			$out = $out .  '<form class="basicSpanDiv" method="post" style="width:100%; height:40px; padding-top:15px;">';
				$out = $out .  '<span>';
					$out = $out .  '<b>Set Rank</b>';
				$out = $out .  '</span>';
					$out = $out .  "<input type='hidden' name='thisUser' value='" . addslashes($daUsah) . "'>";
				$out = $out .  '<span>';
					$out = $out . '<input type="hidden" name="newRank" value="';
					if($thisrank=='member'){ $out = $out . 'officer'; }
					if($thisrank=='officer'){ $out = $out . 'member'; }
					$out = $out . '"/>';
				$out = $out .  '</span>';
				$out = $out .  '<span>';
					$out = $out . '<input type="submit" name="promoteUser" class="btn btn-primary" value="Make ';
					if($thisrank=='member'){ $out = $out . 'Officer'; }
					if($thisrank=='officer'){ $out = $out . 'Member'; } 
					$out = $out . '" />';
				$out = $out .  '</span>';
			$out = $out .  '</form>';
		}
	}
	$out = $out .  '<br></div>';
}
//user's events
$out = $out .  '<div class="adminDataSection" id="userEvents" style="margin-bottom:15px;">';
	
	$out = $out .  "<br>";
	$out = $out .  '<p class="userDashSectionHeader" style="padding-left:0px;">' . $_SESSION['eventsUser'] . "'s Events</p> ";
	$out = $out .  "<br>";

	require('../php/connect.php');

	//get user's events
	$queryEve="SELECT event, team FROM teams WHERE (member1='$daUsah' OR member2='$daUsah' OR member3='$daUsah' OR member4='$daUsah' OR member5='$daUsah' OR member6='$daUsah') AND chapter='$chapter'";

	$resultEve = mysqli_query($link, $queryEve);

	if (!$resultEve){
		die('Error: ' . mysqli_error($link));
	}

	//check for users with no events
	if(mysqli_num_rows($resultEve) == 0){
		$out = $out .  "<p style='font-family:tahoma; font-size:14px; padding-left:20px; padding-top:15px;'><b>User Is Not Registered For Any Events!</b></p>";
	}

	//space out events when they're displayed
	$doEventNewline = 0;

	//in a table, of course
	$out = $out .  "<table>";
	$out = $out .  "<tr style='height: 225px; vertical-align: top;'>";

	while(list($event, $team) = mysqli_fetch_array($resultEve)){

		$doEventNewline += 1;

		//rows of 3
		if($doEventNewline > 3){
			$out = $out .  "</tr>";
			$out = $out .  "<tr style='height: 225px; vertical-align: top;'>";
			$doEventNewline = 1;
		}

		$out = $out .  "<td style='width:225px; position:relative;'>
			<p style='font-family:tahoma; font-size:14px; padding-left:20px; padding-top:15px;'><b>" . $event . "</b></p>";

		$out = $out .  "<br>";

		$checkName = addslashes($_SESSION['eventsUser']);
		$checkEvent = addslashes($event);

		//get user's tasks
		$taskQuery="SELECT id, task, done FROM tasks WHERE team='$team' AND event='$checkEvent' AND chapter='$chapter'";

		$taskResult = mysqli_query($link, $taskQuery);

		if (!$taskResult){
			die('Error: ' . mysqli_error($link));
		}

		//check for users with no events
		if(mysqli_num_rows($taskResult) == 0){
			$out = $out .  "<p style='font-family:tahoma; font-size:12px; padding-left:20px; padding-top:15px;'>No Tasks!</p>";
		}

		//for each task
		while(list($id, $task, $done) = mysqli_fetch_array($taskResult)){
			$out = $out .  "<br>";
			$out = $out .  "<form method='post'>";
			$out = $out .  "<input type='hidden' name='event' value='" . $event . "'>";
			$out = $out .  "<input type='hidden' name='task' value='" . $task . "'>";
			if($done == "yes"){
				$out = $out .  "<input style='padding-left:20px;' class='noCheckBox' type='checkbox' checked>";
			}
			else{
				$out = $out .  "<input style='padding-left:20px;' class='noCheckBox' type='checkbox'>";
			}
			$out = $out .  "<p style='padding-left:20px; display:inline-block;'>" . $task . "</p>";
			$out = $out .  "</form>";
		}


		$out = $out .  "</td>";

	}

	$out = $out .  "</tr>";
	$out = $out .  "</table>";
	$out = $out .  "<br><br>";
	if($rank == "admin" || $rank == "adviser" || ($rank == "officer" && $removalPerm == "yes")){

		$out = $out .  '<p class="userDashSectionHeader" style="padding-left:0px;">Remove From Events</p>';
		$out = $out .  '<p class="bodyTextType1">Here you can remove this user from any of their events.</p>';

		$out = $out .  '<form class="basicSpanDiv" method="post" id="removeFromEventForm" style="width:100%; height:40px; padding-top:15px;">';
		$out = $out .  "<input type='hidden' name='deleteEventUser' value='";
		$out = $out . 	$_SESSION['eventsUser'];
		$out = $out .  "' /> ";
		$out = $out .  "<span>";
		$out = $out .  "<b>Delete From Event</b>";
		$out = $out .  '</span>';
		$out = $out .  '<span>';
		$out = $out .  'Event:';
		$out = $out .  '<select id="eventDelete" name="eventDelete">';

		//get events for removal
		require('../php/connect.php');

		//get user's events
		$queryDele="SELECT event FROM teams WHERE (member1='$daUsah' OR member2='$daUsah' OR member3='$daUsah' OR member4='$daUsah' OR member5='$daUsah' OR member6='$daUsah') AND chapter='$chapter'";

		$resultDele = mysqli_query($link, $queryDele);

		if (!$resultDele){
			die('Error: ' . mysqli_error($link));
		}

		//show each event as option	
		while(list($event) = mysqli_fetch_array($resultDele)){
			$out = $out .  '<option value="' . $event . '"">' . $event . '</option>';
		}

		//closing stuff, remove event button
		$out = $out .  '</select></span><span><input type="submit" class="btn btn-danger" value="Remove"></span></form><br>';

	}

	$out = $out . '</div>';

if($rank == "admin" || $rank == "adviser" || ($rank == "officer" && $eventPointsPerm == "yes")){
	$out = $out .  '<div class="adminDataSection">';
	$out = $out .  '<p class="userDashSectionHeader" style="padding-left:0px;">User Management</p><br>';
				
	$out = $out .  '<form class="basicSpanDiv" method="post" style="width:100%; height:40px; padding-top:15px;">';
		$out = $out .  '<span>';
			$out = $out .  '<b>Assign User Event Points</b>';
		$out = $out .  '</span>';
			$out = $out .  "<input type='hidden' name='pointsTo' value='" . $daID . "'>";
		$out = $out .  '<span>';
			$out = $out .  'How Many Points :';
			$out = $out .  '<input type="number" id="points" name="points">';
		$out = $out .  '</span>';
		$out = $out .  '<span>';
			$out = $out .  '<input type="submit" class="btn btn-primary" value="Assign Points">';
		$out = $out .  '</span>';
	$out = $out .  '</form>';
	$out = $out .  '<form class="basicSpanDiv" method="post" style="width:100%; height:40px; padding-top:15px;">';
		$out = $out .  '<span>';
			$out = $out .  '<b>Remove User Event Points</b>';
		$out = $out .  '</span>';
			$out = $out .  "<input type='hidden' name='pointsFrom' value='" . $daID . "'>";
		$out = $out .  '<span>';
			$out = $out .  'How Many Points :';
			$out = $out .  '<input type="number" id="points" name="points">';
		$out = $out .  '</span>';
		$out = $out .  '<span>';
			$out = $out .  '<input type="submit" class="btn btn-danger" value="Remove Points">';
		$out = $out .  '</span>';
	$out = $out .  '</form>';
}
if($rank == "admin" || $rank == "adviser"){
	$out = $out .  '<form class="basicSpanDiv" method="post" id="deleteUserForm" style="width:100%; height:40px; padding-top:15px;">';
		$out = $out .  '<span>';
			$out = $out .  '<b>Delete Account</b>';
		$out = $out .  '</span>';
			$out = $out .  "<input type='hidden' name='thisUser' id='thisUser' value='" . $daID . "'>";
		$out = $out .  '<span>';
		$out = $out .  'Are You Sure? :';
			$out = $out .  '<select id="confirmDeleteUser" name="confirmDeleteUser">';
				$out = $out .  '<option value="no">No</option>';
				$out = $out .  '<option value="yes">Yes</option>';
			$out = $out .  '</select>';
		$out = $out .  '</span>';
		$out = $out .  '<span>';
			$out = $out .  '<input type="submit" name="deleteUser" class="btn btn-danger" value="Delete Account" />';
		$out = $out .  '</span>';
	$out = $out .  '</form>';
}

$out = $out .  '<br>';
$out = $out .  '</div>';

$out = $out . "<script>$('#userModal').modal('show');</script>";

$out = $out .  '</div></div></div>';

echo $out;
?>
						