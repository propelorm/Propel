---
layout: post
title: ! 'Don''t Do This At Home #3: Terminated Query Methods'
published: true
---
<p>Can you spot the problem in the following snippet?</p>
<p><script src="https://gist.github.com/def1be217cd6931629c4.js"></script></p>
<p><!--more-->Firstly, this is a "termination method", which means that it doesn't return the query. Every termination method should take the connection as argument, otherwise you can't guarantee <a href="http://www.propelorm.org/documentation/06-transactions.html">transactional integrity</a>.</p>
<p>Secondly, this method hydrates all the Book entities satifying the queries, but just needs the first one. That's why you should use <code>findOne()</code> instead of <code>find()-&gt;getFirst()</code>, since <code>findOne()</code> includes a call to <code>limit(1)</code>.</p>
<p><strong>Update</strong>: There is a good reason why this just can't work (see the comments below). The following advice is therefore irrelevant, but is left published to testify that sometimes, code review is hard.</p>
<p>So the method should look like:</p>
<p><script src="https://gist.github.com/b7ab0bc93828a64cb032.js"></script></p>
<p>But there is more. As already mentioned in this blog, <a href="http://propel.posterous.com/design-your-queries-like-a-boss">you should never include the termination method in a custom query method</a>. What if you need to count the number of books satisfying the filters instead of returning the first result? You would have to refactor the filter part into another method, so let's do that in the first place:</p>
<p><script src="https://gist.github.com/642a6d177885b39f86c5.js"></script></p>
<p>Does this oblige you to write the call to <code>findOne()</code> manually? No, because <code>ModelCriteria::__call()</code> <a href="http://www.propelorm.org/reference/model-criteria.html#using_magic_query_methods">magically proxies</a> a call from <code>findOneByXXX($value, $con)</code> to <code>filterByXXX($value)-&gt;findOne($con)</code>. So the following still works:</p>
<p><script src="https://gist.github.com/d28038cacf7acd3a8484.js"></script></p>
<p>Finally, <code>Id</code> is actually the primary key of the <code>book</code> table. <code>findPk()</code> is more efficient than <code>findOne()</code>, because it takes advantage of the instance pooling and of the <a href="http://propel.posterous.com/propel-16-is-faster-than-ever">recent SQL optimizations introduced in Propel 1.6.3</a>. So the custom query method should be reduced to:</p>
<p><script src="https://gist.github.com/1afd569896d8e8d850c0.js"></script></p>
<p>And used as follows:</p>
<p><script src="https://gist.github.com/c8caf49468af851f4706.js"></script></p>
<p>Some purists would argue that the controller code should be as small as possible. My opinion is that it's not longer to write <code>-&gt;withPublishedComment()-&gt;findPk($id, $con)</code> than <code>-&gt;findOneByIdWithPublishedComment($id, $con)</code>, and it is more readable. But it comes down to a matter of coding style.</p>
