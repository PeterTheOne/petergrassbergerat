<!DOCTYPE html>
<html>
	<head>
		<title>admin - login</title>
		
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
			<h1>admin - login</h1>
		</header>
		
		<div id="content">
{if isset($info)}
			<p>
				{$info}
			</p>
{/if}
			<h2>form</h2>
			<form method="post" action="admin_login.php">
				<input class="username" name="username" type="text" placeholder="username" required />
				<input class="password" name="password" type="password" placeholder="password" required />
				<input class="token" name="token" type="hidden" value="{$token}" />
				<button type="submit">submit</button>
			</form>
		</div>
	</body>	
</html>
