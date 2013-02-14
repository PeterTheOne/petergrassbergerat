                <article class="blogPost" itemscope itemtype="http://schema.org/BlogPosting">
                    <h3 itemprop="name headline"><a itemprop="url" href="{$blogPost.url}">{$blogPost.title}</a></h3>
                    <span class="metaData">
                        Posted on
                        <span itemprop="dateCreated" datetime="{$blogPost.dateCreated}T{$blogPost.timeCreated}">
                            {$blogPost.datetimeCreated|date_format:"%d.%m.%Y at %H:%M"}
                        </span>
                        | Tags:
                        <span itemprop="keywords">{$blogPost.tags}</span>
                    </span>
                    <p class="description" itemprop="description">{$blogPost.description}</p>
                    <div id="articleBody" itemprop="text articleBody">
                        {$blogPost.content}
                    </div>
                </article>