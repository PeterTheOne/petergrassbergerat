<!DOCTYPE html>
<html>
	<head>
		<title>admin - overview</title>
		
		<meta charset="utf-8" />
		<meta name="author" content="Peter Grassberger" />
		<link rel="stylesheet" type="text/css" href="css/admin-style.css" />
		
		<!--
			Using rel="profile" to add xhtml meta data profiles (XMDP)
			to this HTML5 document as recomended here: 
			http://microformats.org/wiki/rel-profile
			http://microformats.org/wiki/profile-uris#.28X.29HTML_5_.2F_XHTML_2
		-->
		<link rel="profile" href="http://gmpg.org/xfn/11" />
		<link rel="profile" href="http://microformats.org/profile/rel-license" />
		
		<link rel="me" href="https://profiles.google.com/114748353184495722818" />
		<link rel="me" hreflang="de-AT" href="http://petergrassberger.at" />
		<link rel="me" hreflang="en" href="http://petergrassberger.com" />
		
		<script src="jquery-1.7.1/jquery-1.7.1.min.js" type="text/javascript"></script>
		<!-- plugin by Ted Devito: http://teddevito.com/demos/textarea.html -->
		<script src="jquery.textarea/jquery.textarea.js" type="text/javascript"></script>
		<script src="js/script.js" type="text/javascript"></script>
	</head>
	<body>
		<header>
			<h1>admin - overview</h1>
		</header>
		
		<div id="content">
			<article>
{if isset($info)}
				<p>
					{$info}
				</p>
{/if}
				<a href="?state=logout">logout</a>
				<h2>Pages</h2>
				<ul>
{foreach $pagelist as $page}
					<li>
						<a href="?state=edit&amp;type=page&amp;title_clean={$page.title_clean}&amp;lang={$page.lang}">
							{$page.title} ({$page.lang})
						</a>
						- 
						<a href="?state=delete&amp;type=page&amp;title_clean={$page.title_clean}&amp;lang={$page.lang}">
							delete
						</a>
					</li>
{foreachelse}
					<li>no entries</li>
{/foreach}
				</ul>
				
				<h2>Portfolio</h2>
				<ul>
{foreach $projectlist as $years}
{strip}
					<li>
						{$years@key}
						<ul>
{foreach $years as $project}
{strip}
							<li>
								<a href="?state=edit&amp;type=project&amp;title_clean={$project.title_clean}&amp;lang={$project.lang}">
									{$project.title} ({$project.lang})
								</a>
								- 
								<a href="?state=delete&amp;type=project&amp;title_clean={$project.title_clean}&amp;lang={$project.lang}">
									delete
								</a>
							</li>
{/strip}
{/foreach}
						</ul>
					</li>
{/strip}
{foreachelse}
					<li>no entries</li>
{/foreach}
				</ul>
			</article>
		</div>
	</body>	
</html>
