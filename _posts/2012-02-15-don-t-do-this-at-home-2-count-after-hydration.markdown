---
layout: post
title: ! 'Don''t Do This At Home #2: Count After Hydration'
published: true
---
<p>Can you spot the problem in the following snippet?</p>
<p><script src="https://gist.github.com/bebcdcd21761858c07a0.js"></script></p>
<p>The problem is that, if all you need is the number of books, you just wasted a lot of memory.<!--more--> <code>find()</code> issues a SQL <code>SELECT</code> query, iterates over the resultset, and populates ("hydrates") a new <code>book</code> object for each row. If there are 10,000 results to the query, Propel hydrates 10,000 Model objects... for nothing, since all you need is the number of results.</p>
<p>The good way to count the number of results of a query is to use the <code>ModelCriteria::count()</code> method:</p>
<p><script src="https://gist.github.com/a349db5405518d177c0f.js"></script></p>
<p>Propel also uses <code>count()</code> internally when you call <code>paginate()</code>, so that only the results in the current page get hydrated.</p>
