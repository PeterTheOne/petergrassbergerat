<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
	<channel>
		<title>Peter Grassberger - RSS feed</title>
		<link>{$baseUrl}</link>
		<atom:link href="{$baseUrl}/rss.php" rel="self" type="application/rss+xml" />
{if $lang == 'de-AT'}
		<description>Seiten und Portfolio updates von {$baseUrl}</description>
{else}
		<description>Page and Portfolio updates from {$baseUrl}</description>
{/if}
		<language>{$lang}</language>
		<copyright>Creative Commons BY-NC-ND http://creativecommons.org/licenses/by-nc-nd/3.0/</copyright>
		<managingEditor>petertheone@gmail.com (Peter Grassberger)</managingEditor>
		<webMaster>petertheone@gmail.com (Peter Grassberger)</webMaster>
		<docs>http://rssboard.org/rss-specification</docs>
{foreach $pageprojectlist as $pageproject}
{if $pageproject.type == 'page'}
		<item>
			<title>{$pageproject.title}</title>
			<link>{$baseUrl}{$pageproject.title_clean}/</link>
			<guid isPermaLink="true">{$baseUrl}{$pageproject.title_clean}</guid>
		</item>
{else}
		<item>
			<title>{$pageproject.title}</title>
			<link>{$baseUrl}portfolio/{$pageproject.title_clean}</link>
			<guid isPermaLink="true">{$baseUrl}portfolio/{$pageproject.title_clean}</guid>
		</item>
{/if}
{/foreach}
	</channel>
</rss>
