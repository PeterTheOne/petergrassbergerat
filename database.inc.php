<?php

include_once("config.inc.php");

function db_connect() {
	$db_con = mysqli_connect(HOST, USERNAME, PASSWD, DBNAME);
		//TODO: error handling..
		if (!$db_con) {
			die('Could not connect: ' . mysqli_error($db_con));
		}
	$db_con->set_charset("utf8");
	return $db_con;
}

function db_getPage($db_con, $title_clean, $lang) {
	$query = "SELECT * FROM pages WHERE lang = '$lang' AND title_clean = '$title_clean'";
	$success = mysqli_query($db_con, $query);
	if (!$success) {
		return false;
	}
	return mysqli_fetch_array($success);
}

function db_getProject($db_con, $title_clean, $lang) {
	$query = "SELECT * FROM projects WHERE lang = '$lang' AND title_clean = '$title_clean'";
	$success = mysqli_query($db_con, $query);
	if (!$success) {
		echo "Error message = ".mysqli_error($db_con); 
		return false;
	}
	return mysqli_fetch_array($success);
}

function db_getProjectList($db_con, $lang) {
	$query = "SELECT * FROM projects WHERE lang = '$lang' AND wip = 1 ORDER BY year DESC, title";
	$success = mysqli_query($db_con, $query);
	if (!$success) {
		return false;
	}
	$projectlist = array();
	while($project = mysqli_fetch_array($success)) {
		if ($lang == 'de-AT') {
			$projectlist['Laufende Arbeiten'][] = $project;
		} else {
			$projectlist['Work in Progress'][] = $project;
		}
	}
	$query = "SELECT * FROM projects WHERE lang = '$lang' AND wip = 0 ORDER BY year DESC, title";
	$success = mysqli_query($db_con, $query);
	if (!$success) {
		return false;
	}
	while($project = mysqli_fetch_array($success)) {
		$projectlist[$project['year']][] = $project;
	}	
	return $projectlist;
}

?>
