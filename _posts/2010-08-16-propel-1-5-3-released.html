---
layout: post
title: Propel 1.5.3 Released
published: true
---
<p>Even during the summer, the Propel team is at work for improving your favorite ORM. With 19 bug fixes and 7 enhancements, here comes the latest minor release of the 1.5 branch. Propel 1.5.3 has an impressive <a href="http://www.propelorm.org/wiki/Documentation/1.5/CHANGELOG">changelog</a>, but it&rsquo;s still the same story: it&rsquo;s backwards compatible, more robust, performs better, and offers a handful of new features.</p>

<p>What&rsquo;s new in this release is that more and more people actively contribute to the Propel core. Whether by opening bug reports, writing unit tests, patches or documentation, they all helped to deliver a great release in a very short timeframe. Don&rsquo;t hesitate to thank them all in the comments ; if you don&rsquo;t already follow the <a href="http://www.propelorm.org/timeline">project timeline</a>, you can find their names in the changelog.</p>

<p>Let&rsquo;s look closer at some of the enhancements brought by Propel 1.5.3.</p>

<h3><code>ModelCriteria::select()</code></h3>

<p>You&rsquo;re now used to retrieving ActiveRecord objects or collections using the generated Query classes. But what if you just need one or two calculated columns? In that case, hydrating an entire object is overkill. Propel 1.5.3 introduces the <code>ModelCriteria::select()</code> query modifier. It tells the query to return an array of columns rather than ActiveRecord objects. It&rsquo;s very intuitive and easy to use: just specify which columns you need in an array passed as argument to <code>select()</code>, terminate the query with <code>find()</code>, and you&rsquo;re done:</p>

<div class="CodeRay">
  <div class="code"><pre>$books = BookQuery::create()
  -&gt;join('Author')
  -&gt;select(array('Id', 'Title', 'Author.LastName'))
  -&gt;find();
// array(
//   array('Id' =&gt; 123, 'Title' =&gt; 'Pride and Prejudice', 'Author.LastName' =&gt; 'Austen'),
//   array('Id' =&gt; 456, 'Title' =&gt; 'War and Peace', 'Author.LastName' =&gt; 'Tolstoi')
// )</pre></div>
</div>


<p>You can mix columns from the main object and from joined classes &ndash; Propel will deal with any column name present in the query. If you need only one row, terminate with <code>findOne()</code> instead of <code>find()</code>, and you&rsquo;ll get an array instead of an array of arrays:</p>

<div class="CodeRay">
  <div class="code"><pre>$books = BookQuery::create()
  -&gt;join('Author')
  -&gt;select(array('Id', 'Title', 'Author.LastName'))
  -&gt;findOne();
// array('Id' =&gt; 123, 'Title' =&gt; 'Pride and Prejudice', 'Author.LastName' =&gt; 'Austen')</pre></div>
</div>


<p>If you need only a single column, use a column name instead of an array of column names as argument to <code>select()</code>. Propel will the be smart enough to return scalars rather than arrays:</p>

<div class="CodeRay">
  <div class="code"><pre>$books = BookQuery::create()
  -&gt;select('Title')
  -&gt;find();
// array('Pride and Prejudice', 'War and Peace')

$books = BookQuery::create()
  -&gt;select('Title')
  -&gt;findOne();
// 'Pride and Prejudice'</pre></div>
</div>


<p>There is more to <code>select()</code> than the few examples illustrated here &ndash; fortunately, this new feature is extensively documented. Head to the <a href="http://www.propelorm.org/wiki/Documentation/1.5/ModelCriteria#GettingColumnsInsteadOfObjects">ModelCriteria reference</a> for details.</p>

<h3><code>ModelCriteria::groupByClass()</code></h3>

<p>If you need to add all the columns of a model in a GROUP BY clause (PostgreSQL forces you to do that when you use aggregate functions in addition to a model object), use the new <code>groupByClass()</code> shortcut: with a model class name as sole argument, it will expand into one GROUP BY for each column:</p>

<div class="CodeRay">
  <div class="code"><pre>$authors = AuthorQuery::create() 
  -&gt;join('Author.Book') 
  -&gt;withColumn('COUNT(Book.Id)', 'NbBooks')
  -&gt;groupByClass('Author') 
  -&gt;find();
// groupByClass('Author') translates to SQL as:
// GROUP BY author.ID, author.FIRST_NAME, author.LAST_NAME</pre></div>
</div>


<p>Check the documentation for this new feature in the <a href="http://www.propelorm.org/wiki/Documentation/1.5/ModelCriteria#AddingColumns">ModelCriteria reference</a>.</p>

<h3>Behaviors Can Now Create Classes</h3>

<p>Behaviors are no longer limited to the existing model classes (ActiveRecord, Peer, Query and TableMap classes). Starting with Propel 1.5.3, you can let a behavior write an entire new class based on the model. This can be very useful to create proxy classes for your model classes, for instance to integrate Propel with Zend_AMF in order to build a Flex front-end.</p>

<p>Check the how-to for this feature in the <a href="http://www.propelorm.org/wiki/Documentation/1.5/Behaviors#AddingNewClasses">behaviors documentation</a>.</p>

<h3>Various Optimizations</h3>

<p>Propel 1.5.3 benefits from various optimizations that should make your apps a little snappier at runtime, including:</p>

<ul>
<li>Nested Sets Optimization: If you added crafted an index for the left column, queries for the branch or the descendants of a given node will now take advantage of it (cf. <a href="http://www.propelorm.org/ticket/1034">#1034</a>).</li>
<li>Getters for related objects using a composite foreign key now take advantage of the instance pooling (cf. <a href="http://www.propelorm.org/ticket/1011">#1011</a>)</li>
</ul>


<h3>Upgrade</h3>

<p>How to upgrade? You have three choices &ndash; It&rsquo;s your call:</p>

<ul>
<li><p>Subversion tag</p>

<div class="CodeRay">
  <div class="code"><pre>&gt; svn checkout http://svn.propelorm.org/tags/1.5.3</pre></div>
</div>
</li>
<li><p>PEAR package</p>

<div class="CodeRay">
  <div class="code"><pre>&gt; sudo pear upgrade propel/propel-generator
 &gt; sudo pear upgrade propel/propel-runtime</pre></div>
</div>
</li>
<li><p>Download</p>

<ul>
<li><a href="http://files.propelorm.org/propel-1.5.3.tar.gz">http://files.propelorm.org/propel-1.5.3.tar.gz</a> (Linux)</li>
<li><a href="http://files.propelorm.org/propel-1.5.3.zip">http://files.propelorm.org/propel-1.5.3.zip</a> (Windows)</li>
</ul>
</li>
</ul>
