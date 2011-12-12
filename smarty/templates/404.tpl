<!DOCTYPE html>
<html>
{include file="head.tpl"}
	<body>
		<div id=all>
{include file="header.tpl"}
{include file="nav.tpl"}
			<div id="content">
				<article>
{if $lang eq 'de-AT'}
					<h2>404 - Nicht Gefunden</h2>
					<p>Diese Seite konnte nicht gefunden werden.</p>
{else}
					<h2>404 - Not Found</h2>
					<p>This Page could not be found.</p>
{/if}
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
