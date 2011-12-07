{include file="head.tpl"}
	<body>
		<div id=all>
{include file="header.tpl"}
{include file="nav.tpl"}
			<div id="content">
				<article>
					<h2>{$title}{if isset($downloadLink)}<span class="page-download"> - <a href="{$downloadLink}">Download</a></span>{/if}</h2>
					{$content}
				</article>
			</div>
{include file="aside.tpl"}
			<!--<footer>
				<a href="http://creativecommons.org/licenses/by-nd/3.0/at/">
					<img src="images/cc80x15.png" alt="CC BY-ND" />
				</a>
			</footer>-->
		</div><!--id=all-->
	</body>
</html>
