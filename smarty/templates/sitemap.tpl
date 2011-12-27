<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
	<url>
		<loc>{$url}</loc>
	</url>
{foreach $pagelist as $page}
	<url>
		<loc>{$url}/{$page.title_clean}</loc>
	</url>
{/foreach}
{foreach $projectlist as $years}
{foreach $years as $project}
	<url>
		<loc>{$url}/portfolio/{$project.title_clean}</loc>
	</url>
{/foreach}
{/foreach}
</urlset>
