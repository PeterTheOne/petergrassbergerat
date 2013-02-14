<?php

include_once('../Smarty-3.1.13/libs/Smarty.class.php');

function s_init() {
	$smarty = new Smarty;

	//$smarty->force_compile =	true;
	$smarty->debugging =		false;
	$smarty->caching =			false;
	$smarty->cache_lifetime =	120;

	$smarty->template_dir =		"../smarty/templates";
	$smarty->compile_dir =		"../smarty/templates_c";
	$smarty->cache_dir =		"../smarty/cache";
	$smarty->config_dir =		"../smarty/configs";
	
	return $smarty;
}

?>
