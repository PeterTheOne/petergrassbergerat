			<header>
				<nav id="top-nav">
					<ul>
{if $lang == 'de-AT'}
						<li>
{if isset($translateURL)}
							<a rel="me" hreflang="en" href="{$translateURL}">en</a>
{else}
							<a class="broken" rel="me" hreflang="en" href="http://petergrassberger.com">en</a>
{/if}
						</li>
						<li>
							<a href="{$baseUrl}about/">impressum</a>
						</li>
{else}
						<li>
{if isset($translateURL)}
							<a rel="me" hreflang="de-AT" href="{$translateURL}">de</a>
{else}
							<a class="broken" rel="me" hreflang="en" href="http://petergrassberger.at">de</a>
{/if}
						</li>
						<li>
							<a href="{$baseUrl}about/">about</a>
						</li>
{/if}
					</ul>
				</nav>
				<h1><a href="{$baseUrl}">Peter Grassberger</a></h1>
			</header>