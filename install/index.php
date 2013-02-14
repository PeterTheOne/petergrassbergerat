<?php

// INCLUDES

include_once("../config.inc.php");
include_once("../database.inc.php");

// INIT

$db_con = db_connect();

// RUN

// table pages
$r = mysqli_query($db_con, '
	CREATE TABLE IF NOT EXISTS pages (
		id int(11) NOT NULL AUTO_INCREMENT,
		last_change timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
		title varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
		title_clean varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
		lang varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
		downloadlink varchar(300) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
		content text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
		PRIMARY KEY (id)
	) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
');
if ($r) {
	echo "1. table pages created<br />";
} else {
	die('Could not connect: ' . mysqli_error($db_con));
}


// table projects
$r = mysqli_query($db_con, '
	CREATE TABLE IF NOT EXISTS projects (
		id int(11) NOT NULL AUTO_INCREMENT,
		last_change timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
		title varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
		title_clean varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
		lang varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
		year int(4) NOT NULL,
		wip tinyint(1) NOT NULL,
		tags varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
		description varchar(1000) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
		content text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
		PRIMARY KEY (id)
	) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
');


if ($r) {
    echo "2. table projects created<br />";
} else {
    die('Could not connect: ' . mysqli_error($db_con));
}
// table projects
// TODO: how to create tags (M-M) and or categories?
// TODO: how to create versioncontrol..
// TODO: Entity–attribute–value model (EAV)
$r = mysqli_query($db_con, '
	CREATE TABLE IF NOT EXISTS posts (

		/* functional */
		id int(11) NOT NULL AUTO_INCREMENT,
		published tinyint(1) NOT NULL,
		version int(11) NOT NULL,
		isCurrentVersion tinyint(1) NOT NULL,

		/* essential content */
		title varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
		title_clean varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
		content text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,

		/* meta data */
		description varchar(1000) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
		tags varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
		/*categories*/
		language varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
		isFamilyFriendly tinyint(1) NOT NULL,
		wordcount int(11) NOT NULL,

		/* datetime */
		datetimeCreated timestamp NOT NULL,
		datetimeModified timestamp NOT NULL,
		datetimePublished timestamp NOT NULL,

		/* images */
		image varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
		thumbnailUrl varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,

		PRIMARY KEY (id)
	) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
');
if ($r) {
    echo "3. table posts created<br />";
} else {
    die('Could not connect: ' . mysqli_error($db_con));
}

?>