<?php

include_once("config.inc.php");

//TODO: error handling!

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

function db_getProject($db_con, $lang, $title_clean) {
	$lang = mysqli_real_escape_string($db_con, $lang);
	$title_clean = mysqli_real_escape_string($db_con, $title_clean);
	$query = "SELECT * FROM projects WHERE lang = '$lang' AND title_clean = '$title_clean'";
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
		$page['last_change_date'] = substr($page['last_change'], 0, 10);
		$pagelist[] = $page;
	}	
	return $pagelist;
}

function db_getProjectList($db_con, $lang = '') {
	$query = 'SELECT * FROM projects ';
	if ($lang !== '') {
		$lang = mysqli_real_escape_string($db_con, $lang);
		$query .= "WHERE lang = '$lang' ";
	}
	$query .= 'ORDER BY wip DESC, year DESC, title';
	$success = mysqli_query($db_con, $query);
	if (!$success) {
		return false;
	}
	$projectlist = array();
	while($project = mysqli_fetch_array($success)) {
		$project['last_change_date'] = substr($project['last_change'], 0, 10);
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

function db_updatePage($db_con, $lang, $title_clean, $newlang, $newtitle_clean, 
		$title, $downloadlink, $content) {
	// sanitize
	$lang = mysqli_real_escape_string($db_con, $lang);
	$title_clean = mysqli_real_escape_string($db_con, $title_clean);
	$newlang = mysqli_real_escape_string($db_con, $newlang);
	$newtitle_clean = mysqli_real_escape_string($db_con, $newtitle_clean);
	$title = mysqli_real_escape_string($db_con, $title);
	$downloadlink = mysqli_real_escape_string($db_con, $downloadlink);
	$content = mysqli_real_escape_string($db_con, $content);
	
	// write query
	$query = "
		UPDATE 
			pages 
		SET 
			last_change=NOW(), 
			lang='$newlang', 
			title_clean='$newtitle_clean', 
			title='$title', 
			downloadlink='$downloadlink', 
			content='$content' 
		WHERE 
			title_clean='$title_clean' AND 
			lang='$lang'
	";
	
	// send query
	return mysqli_query($db_con, $query);
}

function db_updateProject($db_con, $lang, $title_clean, $newlang, 
		$newtitle_clean, $title, $year, $wip, $tags, $description, $content) {
	// sanitize
	$lang = mysqli_real_escape_string($db_con, $lang);
	$title_clean = mysqli_real_escape_string($db_con, $title_clean);
	$newlang = mysqli_real_escape_string($db_con, $newlang);
	$newtitle_clean = mysqli_real_escape_string($db_con, $newtitle_clean);
	$title = mysqli_real_escape_string($db_con, $title);
	$year = mysqli_real_escape_string($db_con, $year);
	$wip = mysqli_real_escape_string($db_con, $wip);
	$tags = mysqli_real_escape_string($db_con, $tags);
	$description = mysqli_real_escape_string($db_con, $description);
	$content = mysqli_real_escape_string($db_con, $content);
	
	// write query
	$query = "
		UPDATE 
			projects 
		SET 
			last_change=NOW(), 
			lang='$newlang', 
			title_clean='$newtitle_clean', 
			title='$title', 
			year='$year', 
			wip='$wip', 
			tags='$tags', 
			description='$description', 
			content='$content' 
		WHERE 
			title_clean='$title_clean' AND 
			lang='$lang'
	";
	
	// send query
	return mysqli_query($db_con, $query);
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
