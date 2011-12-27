<!DOCTYPE html>
<html>
	<head>
		<title>admin - {$data.title}</title>
		
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
			<h1>admin - {$data.title}</h1>
		</header>
		
		<div id="content">
			<div id="form">
				<h2>form</h2>
{if $state === 'edit' && $type === 'page'}
				<form method="post" action="?title_clean={$data.title_clean}&lang={$data.lang}&state=update&type=page&amp;token={$token}">
{elseif $state === 'edit' && $type === 'project'}
				<form method="post" action="?title_clean={$data.title_clean}&lang={$data.lang}&state=update&type=project&amp;token={$token}">
{elseif $state === 'create' && $type === 'page'}
				<form method="post" action="?title_clean={$data.title_clean}&lang={$data.lang}&state=insert&type=page&amp;token={$token}">
{elseif $state === 'create' && $type === 'project'}
				<form method="post" action="?title_clean={$data.title_clean}&lang={$data.lang}&state=insert&type=project&amp;token={$token}">
{/if}
					<input class="title" name="title" type="text" value="{$data.title}" placeholder="Title" required />
					<input class="title_clean" name="title_clean" type="text" value="{$data.title_clean}" placeholder="title_clean" required />
					<input class="lang" name="lang" type="text" value="{$data.lang}" placeholder="lang" required />
{if $type === 'page'}
					<input class="downloadlink" name="downloadlink" type="text" value="{$data.downloadlink}" placeholder="downloadlink" />
{else}
					<input class="year" name="year" type="text" value="{$data.year}" placeholder="year" required />
					<input class="wip" name="wip" type="text" value="{$data.wip}" placeholder="wip" required />
					<input class="tags" name="tags" type="text" value="{$data.tags}" placeholder="tags" required />
					<input class="description" name="description" type="text" value="{$data.description}" placeholder="description" required />
{/if}
					<textarea class="content" name="content" 
							rows="18" placeholder="type here" required>{$data.content}</textarea>
							
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
