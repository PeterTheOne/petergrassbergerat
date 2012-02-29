<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0">
<channel>
	<title>Peter Grassberger - RSS feed</title>
{if $lang == 'de-AT'}
	<link>http://petergrassberger.at</link>
	<description>Seiten und Portfolio updates von http://petergrassberger.at</description>
{else}
	<link>http://petergrassberger.com</link>
	<description>Page and Portfolio updates from http://petergrassberger.com</description>
{/if}
	<language>{$lang}</language>
	<copyright>Creative Commons BY-NC-ND http://creativecommons.org/licenses/by-nc-nd/3.0/</copyright>
	<managingEditor>petertheone@gmail.com (Peter Grassberger)</managingEditor>
	<webMaster>petertheone@gmail.com (Peter Grassberger)</webMaster>
	<docs>http://rssboard.org/rss-specification</docs>
	
</channel>
{foreach $pageprojectlist as $pageproject}
{if $pageproject.type == 'page'}
	<item>
		<title>{$pageproject.title}</title>
		<link>{$url}/{$pageproject.title_clean}</link>
	</item>
{else}
	<item>
		<title>{$pageproject.title}</title>
		<link>{$url}/portfolio/{$pageproject.title_clean}</link>
	</item>
{/if}
{/foreach}
</rss>
