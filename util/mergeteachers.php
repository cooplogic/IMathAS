<?php

require("../validate.php");

if ($myrights<100) {
	exit;
}

if (empty($_POST['from']) || empty($_POST['to'])) {
	$query = "SELECT id, LastName, FirstName, SID, lastaccess FROM imas_users WHERE rights>11 ORDER BY LastName, FirstName";
	$result = mysql_query($query) or die("Query failed : " . mysql_error());
	$ops = '';
	while ($row = mysql_fetch_row($result)) {
		$ops .= '<option value="'.$row[0].'">'.$row[1].', '.$row[2].' ('.$row[3].') '.tzdate('n/j/y',$row[4]).'</option>';
	}
	require("../header.php");
	echo "<h2>Merge Teacher Accounts</h2>";
	echo '<form method="post">';
	echo 'Move everything from <select name="from">'.$ops.'</select><br/>';
	echo 'to <select name="to">'.$ops.'</select><br/>';
	echo '<input type="submit" value="Go"/>';
	echo '</form>';
	
} else {
$from = $_POST['from'];
$to = $_POST['to'];

$query = "UPDATE imas_courses SET ownerid='$to' WHERE ownerid='$from'";
mysql_query($query) or die("Query failed : " . mysql_error());
$query = "UPDATE imas_questionset SET ownerid='$to' WHERE ownerid='$from'";
mysql_query($query) or die("Query failed : " . mysql_error());
$query = "UPDATE imas_libraries SET ownerid='$to' WHERE ownerid='$from'";
mysql_query($query) or die("Query failed : " . mysql_error());
$query = "UPDATE imas_library_items SET ownerid='$to' WHERE ownerid='$from'";
mysql_query($query) or die("Query failed : " . mysql_error());
$query = "UPDATE imas_rubrics SET ownerid='$to' WHERE ownerid='$from'";
mysql_query($query) or die("Query failed : " . mysql_error());
$query = "UPDATE imas_diags SET ownerid='$to' WHERE ownerid='$from'";
mysql_query($query) or die("Query failed : " . mysql_error());

$query = "UPDATE imas_teachers SET userid='$to' WHERE userid='$from'";
mysql_query($query) or die("Query failed : " . mysql_error());
$query = "UPDATE imas_tutors SET userid='$to' WHERE userid='$from'";
mysql_query($query) or die("Query failed : " . mysql_error());

$query = "UPDATE imas_forum_posts SET userid='$to' WHERE userid='$from'";
mysql_query($query) or die("Query failed : " . mysql_error());
$query = "UPDATE imas_forum_views SET userid='$to' WHERE userid='$from'";
mysql_query($query) or die("Query failed : " . mysql_error());
$query = "UPDATE imas_forum_likes SET userid='$to' WHERE userid='$from'";
mysql_query($query) or die("Query failed : " . mysql_error());
$query = "UPDATE imas_forum_subscriptions SET userid='$to' WHERE userid='$from'";
mysql_query($query) or die("Query failed : " . mysql_error());

$query = "UPDATE imas_wiki_revisions SET userid='$to' WHERE userid='$from'";
mysql_query($query) or die("Query failed : " . mysql_error());
$query = "UPDATE imas_wiki_views SET userid='$to' WHERE userid='$from'";
mysql_query($query) or die("Query failed : " . mysql_error());

$query = "UPDATE imas_ltiusers SET userid='$to' WHERE userid='$from'";
mysql_query($query) or die("Query failed : " . mysql_error());

$query = "UPDATE imas_login_log SET userid='$to' WHERE userid='$from'";
mysql_query($query) or die("Query failed : " . mysql_error());

$query = "DELETE FROM imas_users WHERE id='$from'";
mysql_query($query) or die("Query failed : " . mysql_error());

echo "Done";
}
