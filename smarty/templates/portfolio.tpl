{include file="head.tpl" title='Portfolio'}
	<body>
		<div id=all>
{include file="header.tpl"}
{include file="nav.tpl"}
			<div id="content">
				<article>
					<h2>Portfolio</h2>
					
					{foreach $projectlist as $years}
					{strip}
					{if $years@key == 'wip' && $lang == 'de-AT'}
					<h3>Laufende Arbeiten</h3>
					{else if $years@key == 'wip' && $lang == 'en'}
					<h3>Work in Progress</h3>
					{else}
					<h3>{$years@key}</h3>
					{/if}
					<ul class="time">
					{foreach $years as $project}
					{strip}
						<li>
							<a href="/portfolio/{$project.title_clean}/">
								<h4>{$project.title}</h4>
								<span>{$project.tags}</span>
								<p>{$project.description}</p>
							</a>
						</li>
					{/strip}
					{/foreach}
					</ul>
					{/strip}
					{/foreach}
				</article>
			</div>
{include file="aside.tpl"}
		</div><!--id=all-->
	</body>
</html>
