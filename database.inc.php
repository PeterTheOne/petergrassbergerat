<?php

include_once("config.inc.php");

function db_connect() {
	$db_con = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWD, DB_DBNAME);
		//TODO: error handling..
		if (!$db_con) {
			die('Could not connect: ' . mysqli_error($db_con));
		}
	$db_con->set_charset("utf8");
	return $db_con;
}

function db_getPage($db_con, $lang, $title_clean) {
	$lang = mysqli_real_escape_string($db_con, $lang);
	$title_clean = mysqli_real_escape_string($db_con, $title_clean);
	$query = "SELECT * FROM pages WHERE lang = '$lang' AND title_clean = '$title_clean'";
	$success = mysqli_query($db_con, $query);
	if (!$success) {
		return false;
	}
	return mysqli_fetch_array($success);
}

function db_getPageList($db_con, $lang = '') {
	$query = 'SELECT * FROM pages ';
	if ($lang !== '') {
		$lang = mysqli_real_escape_string($db_con, $lang);
		$query .= "WHERE lang = '$lang' ";
	}
	$query .= 'ORDER BY title';	
	$success = mysqli_query($db_con, $query);
	if (!$success) {
		return false;
	}
	$pagelist = array();
	while($page = mysqli_fetch_array($success)) {
		$pagelist[] = $page;
	}	
	return $pagelist;
}

function db_getProject($db_con, $lang, $title_clean) {
	$lang = mysqli_real_escape_string($db_con, $lang);
	$title_clean = mysqli_real_escape_string($db_con, $title_clean);
	$query = "SELECT * FROM projects WHERE lang = '$lang' AND title_clean = '$title_clean'";
	$success = mysqli_query($db_con, $query);
	if (!$success) {
		echo "Error message = ".mysqli_error($db_con); 
		return false;
	}
	return mysqli_fetch_array($success);
}

function db_getProjectList($db_con, $lang = '') {
	$query = 'SELECT * FROM projects ';
	if ($lang !== '') {
	$lang = mysqli_real_escape_string($db_con, $lang);
		$query .= "WHERE lang = '$lang' ";
	}
	$query .= 'ORDER BY year DESC, title';
	$success = mysqli_query($db_con, $query);
	if (!$success) {
		return false;
	}
	$projectlist = array();
	while($project = mysqli_fetch_array($success)) {
		if ($project['wip']) {
			if ($lang == 'de-AT') {
				$projectlist['Laufende Arbeiten'][] = $project;
			} else {
				$projectlist['Work in Progress'][] = $project;
			}
		} else {
			$projectlist[$project['year']][] = $project;
		}
	}	
	return $projectlist;
}

function checkExists($db_con, $langNot, $site, $subsite) {
	if ($site === 'portfolio' && 
			$subsite === '') {
		$result = db_getProjectList($db_con, $langNot);
	} else {
		if ($subsite === '') {
			$result = db_getPage($db_con, $langNot, $site);
		} else {
			$result = db_getProject($db_con, $langNot, $subsite);
		}
	}
	return $result;
}

?>
