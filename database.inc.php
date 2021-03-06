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

function db_hasErrors($db_con, $result) {
	if(!$result){
		if (PRINT_DB_ERRORS) {
			echo "<p>error: " . mysqli_error($db_con) . "</p>";
		}
		return true;
	}
	return false;
}

function db_getPage($db_con, $lang, $title_clean) {
	$lang = mysqli_real_escape_string($db_con, $lang);
	$title_clean = mysqli_real_escape_string($db_con, $title_clean);
	$query = "SELECT * FROM pages WHERE lang = '$lang' AND title_clean = '$title_clean'";
	$result = mysqli_query($db_con, $query);
	if (db_hasErrors($db_con, $result)) {
		return false;
	}
	return mysqli_fetch_array($result);
}

function db_getProject($db_con, $lang, $title_clean) {
	$lang = mysqli_real_escape_string($db_con, $lang);
	$title_clean = mysqli_real_escape_string($db_con, $title_clean);
	$query = "SELECT * FROM projects WHERE lang = '$lang' AND title_clean = '$title_clean'";
	$result = mysqli_query($db_con, $query);
	if (db_hasErrors($db_con, $result)) {
		return false;
	}
	return mysqli_fetch_array($result);
}

function db_getPageList($db_con, $lang = '', $order = '') {
	$lang = mysqli_real_escape_string($db_con, $lang);
	$query = '
		SELECT 
			*, 
			DATE(last_change) as last_change_date
		FROM 
			pages 
	';
	if ($lang !== '') {
		$query .= "
			WHERE 
				lang = '$lang' 
		";
	}
	if ($order == '') {
		$query .= '
			ORDER BY 
				title
		';
	} else {
		$query .= 'ORDER BY ';
		for ($i = 0; $i < sizeof($order); $i++) {
			if ($i != 0) {
				$query .= ", ";
			}
			$query .= $order[$i];
		}
	}
	$result = mysqli_query($db_con, $query);
	if (db_hasErrors($db_con, $result)) {
		return false;
	}
	$pagelist = array();
	while($page = mysqli_fetch_array($result)) {
		$pagelist[] = $page;
	}
	return $pagelist;
}

function db_getProjectList($db_con, $lang = '', $order = '') {
	$lang = mysqli_real_escape_string($db_con, $lang);
	$query = '
		SELECT 
			*, 
			DATE(last_change) as last_change_date
		FROM 
			projects 
	';
	if ($lang !== '') {
		$query .= "
			WHERE 
				lang = '$lang' 
		";
	}
	if ($order == '') {	
		$query .= '
			ORDER BY 
				wip DESC, 
				year DESC, 
				title
		';
	} else {
		$query .= 'ORDER BY ';
		for ($i = 0; $i < sizeof($order); $i++) {
			if ($i != 0) {
				$query .= ", ";
			}
			$query .= $order[$i];
		}
	}
	$result = mysqli_query($db_con, $query);
	if (db_hasErrors($db_con, $result)) {
		return false;
	}
	$projectlist = array();
	while($project = mysqli_fetch_array($result)) {
		if ($project['wip']) {
			$projectlist['wip'][] = $project;
		} else {
			$projectlist[$project['year']][] = $project;
		}
	}
	return $projectlist;
}

function db_getPageProjectList($db_con, $lang = '', $order = '') {
	$lang = mysqli_real_escape_string($db_con, $lang);
	$query = "
		SELECT 
			id, 
			last_change, 
			title, 
			title_clean, 
			lang, 
			content, 
			'page' as type, 
			DATE(last_change) as last_change_date
		FROM 
			pages
	";
	if ($lang !== '') {
		$query .= "
			WHERE 
				lang = '$lang' 
		";
	}
	$query .= "
		UNION ALL 
		SELECT 
			id, 
			last_change, 
			title, 
			title_clean, 
			lang, 
			content, 
			'project' as type, 
			DATE(last_change) as last_change_date
		FROM 
			projects
	";
	if ($lang !== '') {
		$query .= "
			WHERE 
				lang = '$lang' 
		";
	}
	if ($order == '') {
		$query .= '
			ORDER BY 
				title, 
				title
		';
	} else {
		$query .= 'ORDER BY ';
		for ($i = 0; $i < sizeof($order); $i++) {
			if ($i != 0) {
				$query .= ", ";
			}
			$query .= $order[$i];
		}
	}
	$result = mysqli_query($db_con, $query);
	if (db_hasErrors($db_con, $result)) {
		return false;
	}
	$pagelist = array();
	while($page = mysqli_fetch_array($result)) {
		$pagelist[] = $page;
	}
	return $pagelist;
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
	$result = mysqli_query($db_con, $query);
	if(db_hasErrors($db_con, $result)) {
		return false;
	}
	return $result;
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
	$result = mysqli_query($db_con, $query);
	if(db_hasErrors($db_con, $result)) {
		return false;
	}
	return $result;
}

function db_insertPage($db_con, $lang, $title_clean, $title, $downloadlink, 
		$content) {
	// sanitize
	$lang = mysqli_real_escape_string($db_con, $lang);
	$title_clean = mysqli_real_escape_string($db_con, $title_clean);
	$title = mysqli_real_escape_string($db_con, $title);
	$downloadlink = mysqli_real_escape_string($db_con, $downloadlink);
	$content = mysqli_real_escape_string($db_con, $content);
	
	// write query
	$query = "
		INSERT INTO 
			pages (
				last_change, 
				lang, 
				title_clean, 
				title, 
				downloadlink, 
				content
			)
		VALUES (
			NOW(), 
			'$lang', 
			'$title_clean', 
			'$title', 
			'$downloadlink', 
			'$content'
		)
	";
	
	// send query
	$result = mysqli_query($db_con, $query);
	if(db_hasErrors($db_con, $result)) {
		return false;
	}
	return $result;
}

function db_insertProject($db_con, $lang, $title_clean, $title, $year, $wip, 
		$tags, $description, $content) {
	// sanitize
	$lang = mysqli_real_escape_string($db_con, $lang);
	$title_clean = mysqli_real_escape_string($db_con, $title_clean);
	$title = mysqli_real_escape_string($db_con, $title);
	$year = mysqli_real_escape_string($db_con, $year);
	$wip = mysqli_real_escape_string($db_con, $wip);
	$tags = mysqli_real_escape_string($db_con, $tags);
	$description = mysqli_real_escape_string($db_con, $description);
	$content = mysqli_real_escape_string($db_con, $content);
	
	// write query
	$query = "
		INSERT INTO 
			projects (
				last_change, 
				lang, 
				title_clean, 
				title, 
				year, 
				wip, 
				tags, 
				description, 
				content
			)
		VALUES (
			NOW(), 
			'$lang', 
			'$title_clean', 
			'$title', 
			'$year', 
			'$wip', 
			'$tags', 
			'$description', 
			'$content'
		)
	";
	
	// send query
	$result = mysqli_query($db_con, $query);
	if(db_hasErrors($db_con, $result)) {
		return false;
	}
	return $result;
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
