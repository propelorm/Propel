---
layout: post
title: ! 'Don''t Do This At Home #1: Update Many Rows By Looping on save()'
published: true
---
<p>Can you spot the problem in the following snippet?</p>
<p><script src="https://gist.github.com/d62b3172814a9e80f04f.js"></script></p>
<p>The problem is that this program hydrates a lot of <code>Book</code> objects for nothing, just to update them afterwards. It's a waste of time and memory. <!--more-->The good way to do this is to use the <code>BookQuery::update()</code> method, as follows:</p>
<p><script src="https://gist.github.com/57be123d7c150729d585.js"></script></p>
<p>This second version takes almost no memory, and most important: the execution time doesn't depend on the number of records concerned by the change.</p>
<p>Note that you may need to use the first version, for instance if the model uses a behavior, and if this behavior has <code>preSave()</code> or <code>postSave()</code> hooks. In that case, instead of falling back to the first example, use the third argument of the <code>update()</code> method. This will let your code reviewer know that you did that on purpose:</p>
<p><script src="https://gist.github.com/0a81b48230240e3d45c8.js"></script></p>
