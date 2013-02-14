                <article itemscope itemtype="http://schema.org/BlogPosting">
                    <h3 itemprop="name headline"><a itemprop="url" href="{$blogPost.url}">{$blogPost.title}</a></h3>
                    <span itemprop="dateCreated" datetime="{$blogPost.dateCreated}T{$blogPost.timeCreated}">{$blogPost.datetimeCreated}</span>
                    Tags: <span itemprop="keywords">{$blogPost.tags}</span>
                    <p itemprop="description">{$blogPost.description}</p>
                    <div itemprop="text articleBody">
                        {$blogPost.content}
                    </div>
                </article>