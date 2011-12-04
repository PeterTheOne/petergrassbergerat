<!DOCTYPE html>
<html lang="{$lang}">
	<head>
{if isset($title)}
		<title>Peter Grassberger - {$title}</title>
{else}
		<title>Peter Grassberger</title>
{/if}
		
		<meta charset="utf-8" />
		<meta name="author" content="Peter Grassberger" />
		<link rel="stylesheet" type="text/css" href="http://petergrassberger.at/style.css" />
		
{if $lang eq 'de-AT'}
		<link rel="me" hreflang="en" href="http://petergrassberger.com/" />
		<meta name="google-site-verification" content="c921Y27A8rKcXqF4SX7ezQcQwMtMM0pMQxUJQLL3Se0" />
{else}
		<link rel="me" hreflang="de-AT" href="http://petergrassberger.at/" />
		<meta name="google-site-verification" content="F8q0FAvDVXjAwv7N7Xe4N5H5LLA6Hnznw20uoe0Ehqs" />
{/if}
		
		<!--
			SyntaxHighlighter by: Alex Gorbatchev
			http://alexgorbatchev.com/SyntaxHighlighter/
		-->
		<script type="text/javascript" src="syntaxhighlighter_3.0.83/scripts/shCore.js"></script>
		<script type="text/javascript" src="syntaxhighlighter_3.0.83/scripts/shBrushJava.js"></script>
		
		<link href="syntaxhighlighter_3.0.83/styles/shCore.css" rel="stylesheet" type="text/css" />
		<link href="syntaxhighlighter_3.0.83/styles/shThemeDefault.css" rel="stylesheet" type="text/css" />
		
		<script type="text/javascript">
			SyntaxHighlighter.all();
		</script>
	</head>
