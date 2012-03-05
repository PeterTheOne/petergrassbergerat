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
{if isset($error)}
				<p class="error">
					{$error}
				</p>
{/if}
{if isset($info)}
				<p class="info">
					{$info}
				</p>
{/if}
				<a href="admin_logout.php">logout</a>
				<h2>Pages</h2>
				<a href="admin_create.php?type=page">create</a>
				<table class="admin_overview">
{foreach $pagelist as $page}
					<tr>
						<td>
							<a href="admin_edit.php?type=page&amp;title_clean={$page.title_clean}&amp;lang={$page.lang}">
								{$page.title}
							</a>
						</td>
						<td>
							<a href="admin_edit.php?type=page&amp;title_clean={$page.title_clean}&amp;lang={$page.lang}">
								{$page.lang}
							</a>
						</td>
						<td>
							<a href="admin_delete.php?type=page&amp;title_clean={$page.title_clean}&amp;lang={$page.lang}">
								delete
							</a>
						</td>
					</tr>
{foreachelse}
					<tr>no entries</tr>
{/foreach}
				</table>
				
				<h2>Portfolio</h2>
				<a href="admin_create.php?type=project">create</a>
				<table class="admin_overview">
{foreach $projectlist as $years}
					<tr><td>{$years@key}</td></tr>
{foreach $years as $project}
					<tr>
						<td>
							<a href="admin_edit.php?type=project&amp;title_clean={$project.title_clean}&amp;lang={$project.lang}">
								{$project.title}
							</a>
						</td>
						<td>
							<a href="admin_edit.php?type=project&amp;title_clean={$project.title_clean}&amp;lang={$project.lang}">
								{$project.lang}
							</a>
						</td>
						<td>
							<a href="admin_delete.php?type=project&amp;title_clean={$project.title_clean}&amp;lang={$project.lang}">
								delete
							</a>
						</td>
					</tr>
{/foreach}
{foreachelse}
					<tr>no entries</tr>
{/foreach}
				</table>
			</article>
		</div>
	</body>
</html>
