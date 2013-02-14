{include file="head.tpl" title='Blog'}
	<body>
		<div id=all>
{include file="header.tpl"}
{include file="nav.tpl"}
			<div id="content">
                <h2>Blog</h2>
                {foreach $blogPostList as $blogPost}
{include file="blogpost.tpl"}
                {foreachelse}

                {if $lang == 'de-AT'}
                <article>
                    <h3>Leer</h3>
                    <p>Es gibt noch keine Blog Beiträge</p>
                </article>
                {else $lang == 'en'}
                <article>
                    <h3>Empty</h3>
                    <p>There are no blog posts yet.</p>
                </article>
                {/if}
                {/foreach}
			</div>
{include file="aside.tpl"}
		</div><!--id=all-->
	</body>
</html>
