{include file="head.tpl" title='Portfolio'}
	<body>
		<div id=all>
{include file="header.tpl"}
{include file="nav.tpl"}
			<div id="content">
				<article>
					<h2>Portfolio</h2>

                {if $wipProjectList}
                {if $lang == 'de-AT'}
                    <h3>Laufende Arbeiten</h3>
                {else $lang == 'en'}
                    <h3>Work in Progress</h3>
                {/if}
                {/if}

                <ul class="time">
                {foreach $wipProjectList as $project}
                        <li>
                            <a href="/portfolio/{$project.title_clean}/">
                                <h4>{$project.title}</h4>
                                <span>{$project.tags}</span>
                                <p>{$project.description}</p>
                            </a>
                        </li>
                {/foreach}
                </ul>

                {$lastYear = -9999}
                <ul class="time">
                {foreach $yearsProjectList as $project}
                {if $lastYear == -9999}
                    <h3>{$project.year}</h3>
                    <ul class="time">
                {elseif $lastYear != $project.year}
                    </ul>

                    <h3>{$project.year}</h3>
                    <ul class="time">
                {/if}
                {$lastYear = $project.year}
                        <li>
                            <a href="/portfolio/{$project.title_clean}/">
                                <h4>{$project.title}</h4>
                                <span>{$project.tags}</span>
                                <p>{$project.description}</p>
                            </a>
                        </li>
                {/foreach}
                    </ul>
				</article>
			</div>
{include file="aside.tpl"}
		</div><!--id=all-->
	</body>
</html>
