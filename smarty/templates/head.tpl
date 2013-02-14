<!DOCTYPE html>
<html lang="{$lang}">
	<head>
{if isset($title)}
		<title>Peter Grassberger - {$title}</title>
{else}
		<title>Peter Grassberger</title>
{/if}
		
		<meta charset="utf-8" />
		<link rel="stylesheet" type="text/css" href="{$baseUrl}css/style.css" />
		
		<!--
			Using rel="profile" to add xhtml meta data profiles (XMDP)
			to this HTML5 document as recomended here: 
			http://microformats.org/wiki/rel-profile
			http://microformats.org/wiki/profile-uris#.28X.29HTML_5_.2F_XHTML_2
		-->
		<link rel="profile" href="http://gmpg.org/xfn/11" />
		<link rel="profile" href="http://microformats.org/profile/rel-license" />
		
		<!-- sitemp http://microformats.org/wiki/rel-sitemap -->
		<link rel="sitemap" type="application/xml" href="{$baseUrl}sitemap.php" />
		
		<link rel="alternate" type="application/rss+xml" title="Seiten und Portfolio updates" href="{$baseUrl}rss.php" />
		
		<!-- author/me -->
		<link rel="me" href="http://profiles.google.com/114748353184495722818" />
		<meta name="author" content="Peter Grassberger" />
		<link rel="author" type="text/plain" href="/humans.txt" />
		<link rel="me" hreflang="de-AT" href="http://petergrassberger.at" />
		<link rel="me" hreflang="en" href="http://petergrassberger.com" />
		
		<link rel="me" href="http://twitter.com/#!/PeterTheOne" />
		<link rel="me" href="http://www.facebook.com/petergrassberger" />
		<link rel="me" href="http://www.xing.com/profile/Peter_Grassberger2" />
		<link rel="me" href="http://github.com/PeterTheOne" />
		<link rel="me" href="http://bitbucket.org/PeterTheOne" />
		<link rel="me" href="http://www.last.fm/user/PeterTheOne" />
		<link rel="me" href="http://www.flickr.com/people/petertheone/" />
		<link rel="me" href="http://vimeo.com/user2440058" />
		<link rel="me" href="http://www.youtube.com/user/PeterTheOne" />
		<link rel="me" href="http://petertheone.deviantart.com/" />
		
		
		<!-- google site verification for petergrassberger.at -->
		<meta name="google-site-verification" content="c921Y27A8rKcXqF4SX7ezQcQwMtMM0pMQxUJQLL3Se0" />
		<!-- google site verification for petergrassberger.com -->
		<meta name="google-site-verification" content="F8q0FAvDVXjAwv7N7Xe4N5H5LLA6Hnznw20uoe0Ehqs" />
		
		<!-- TODO: load js at the end of the page? -->
		
		<!--
			SyntaxHighlighter by: Alex Gorbatchev
			http://alexgorbatchev.com/SyntaxHighlighter/
		-->
		<script type="text/javascript" src="{$baseUrl}js/lib/shCore.js"></script>
		<script type="text/javascript" src="{$baseUrl}js/lib/shBrushJava.js"></script>
		
		<link href="{$baseUrl}css/lib/shCore.css" rel="stylesheet" type="text/css" />
		<link href="{$baseUrl}css/lib/shThemeDefault.css" rel="stylesheet" type="text/css" />
		
		<script type="text/javascript">
			SyntaxHighlighter.all();
		</script>
		
		<!-- jQuery from google api with local fallback -->
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
		<script>window.jQuery || document.write('<script src="js/lib/jquery-1.7.1.min.js"><\/script>')</script>
	</head>
