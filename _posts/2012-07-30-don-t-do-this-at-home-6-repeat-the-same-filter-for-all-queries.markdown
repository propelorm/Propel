---
layout: post
title: ! 'Don''t Do This At Home #6: Repeat The Same Filter For All Queries'
published: true
---
<p>Don't Do This At Home #6: Repeat The Same Filter For All Queries</p>
<p>Can you spot the problem in the following snippet?</p>
<p><script src="https://gist.github.com/db709f0e56f69e9dcf86.js"></script></p>
<p><!--more-->The <code>published()</code> filter appears in all other filters. Ok, that was easy, it's the title of the post! But how to do it better?</p>
<p>Why not create a specialized query class filtering on published artivcles by default? All the frontend controllers should use this <code>FrontendArticleQuery</code> instead of the more generic <code>ArticleQuery</code>. It would look like this:</p>
<p><script src="https://gist.github.com/2cb0d022d1aab2c1c2b5.js"></script></p>
<p>This method can add more default filters (for instance, to restrict to articles linked to an author, articles arleady indexed by a search engine, etc.) Now, everytime you call <code>FrontendArticleQuery::create()</code>, you know you only retreive published articles. No need to add this filter to other filters anymore.</p>
