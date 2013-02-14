<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
	<url>
		<loc>{$baseUrl}</loc>
		<priority>1</priority>
	</url>
{foreach $pagelist as $page}
	<url>
		<loc>{$baseUrl}{$page.title_clean}/</loc>
		<lastmod>{$page.last_change_date}</lastmod>
		<priority>0.7</priority>
	</url>
{/foreach}
{foreach $projectlist as $years}
{foreach $years as $project}
	<url>
		<loc>{$baseUrl}portfolio/{$project.title_clean}</loc>
		<lastmod>{$project.last_change_date}</lastmod>
		<priority>0.5</priority>
	</url>
{/foreach}
{/foreach}
</urlset>
