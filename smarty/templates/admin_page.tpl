<!DOCTYPE html>
<html>
	<head>
{if $state == 'edit'}
		<title>admin - edit: {$data.title}</title>
{else}
		<title>admin - create</title>
{/if}
		
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
		
		<!-- jQuery from google api with local fallback -->
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
		<script>window.jQuery || document.write('<script src="js/lib/jquery-1.7.1.min.js"><\/script>')</script>
		<!-- plugin by Ted Devito: http://teddevito.com/demos/textarea.html -->
		<script src="js/lib/jquery.textarea.js" type="text/javascript"></script>
		<script src="js/admin_script.js" type="text/javascript"></script>
	</head>
	<body>
		<header>
{if $state == 'edit'}
			<h1>admin - edit: {$data.title}</h1>
{else}
			<h1>admin - create</h1>
{/if}
			<a href="admin.php">back to overview</a>
		</header>
		
		<div id="content">
			<div id="form">
				<h2>form</h2>
				<form method="post" action="admin_{$state}.php?type={$type}&title_clean={$data.title_clean}&lang={$data.lang}">
					<input class="title" name="title" type="text" value="{$data.title}" placeholder="Title" required />
					<input class="title_clean" name="title_clean" type="text" value="{$data.title_clean}" placeholder="title_clean" required />
					<input class="lang" name="lang" type="text" value="{$data.lang}" placeholder="lang" required />
{if $type === 'page'}
					<input class="downloadlink" name="downloadlink" type="text" value="{$data.downloadlink}" placeholder="downloadlink" />
{else}
					<input class="year" name="year" type="text" value="{$data.year}" placeholder="year" required />
					<input class="wip" name="wip" type="text" value="{$data.wip}" placeholder="wip" required />
					<input class="tags" name="tags" type="text" value="{$data.tags}" placeholder="tags" required />
					<input class="description" name="description" type="text" value="{$data.description}" placeholder="description" lang="{$data.lang}" required />
{/if}
					<textarea class="content" name="content" rows="18" 
							placeholder="type here" 
							lang="{$data.lang}" required>{$data.content}</textarea>
					<input class="token" name="token" type="hidden" value="{$token}" />
					<button type="submit">submit</button>
				</form>
			</div>
			<div id="preview">
				<h2>preview</h2>
				<h2 id="preview-title"></h2>
				<div id="preview-content">
				</div>
			</div>
		</div>
	</body>
</html>
