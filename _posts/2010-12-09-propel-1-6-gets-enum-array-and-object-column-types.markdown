---
layout: post
title: Propel 1.6 gets ENUM, ARRAY, and OBJECT Column Types
published: true
---
<p>Dealing with complex data will become easier with Propel 1.6. Complex column types have landed in the 1.6 branch, and they offer a clean interface to store and retrieve ENUM, ARRAY, and OBJECT values. Here is a quick example:</p>
<div class="CodeRay">
  <div class="code"><pre>&lt;table name=&quot;book&quot;&gt;
  &lt;column name=&quot;id&quot; type=&quot;INTEGER&quot; primaryKey=&quot;true&quot; autoIncrement=&quot;true&quot; /&gt;
  &lt;column name=&quot;title&quot; type=&quot;VARCHAR&quot; /&gt;
  &lt;column name=&quot;style&quot; type=&quot;ENUM&quot; valueSet=&quot;novel, essay, poetry&quot; /&gt;
  &lt;column name=&quot;tags&quot; type=&quot;ARRAY&quot; /&gt;
&lt;/table&gt;</pre></div>
</div>

<p>The getters and setters for the style and tags columns make it easy to work with a predefined value set, or a list of values:<!--more--></p>
<div class="CodeRay">
  <div class="code"><pre>$book = new Book();
$book-&gt;setTitle('Pride and Prejudice');
// ENUM columns only accept values from the valueSet - other values throw an Exception
$book-&gt;setStyle('novel');
// ARRAY columns accept an array of scalar values
$book-&gt;setTags(array('satire', '19th century'));
$book-&gt;addTag('England');

// These properties are persisted to the database through serialization
$book-&gt;save();
// And of course, Propel restores them seamlessly through hydration
$book = BookQuery::create()-&gt;findOneByTitle('Pride and Prejudice');
echo $book-&gt;getStyle(); // novel
echo $book-&gt;hasTag('satire'); // true
print_r($book-&gt;getTags()); // array('satire', '19th century', 'England');</pre></div>
</div>

<p>To be honest, this is a common feature for other ORMs, and Propel is quite late to support these column types. But no other ORM supports <strong>searching</strong> of records based on complex column values. Thanks to the generated <code>filterByXXX()</code> methods, this is a piece of cake for Propel:</p>
<div class="CodeRay">
  <div class="code"><pre>// find books using an ENUM column value
$books = BookQuery::create()
  -&gt;filterByStyle('novel')
  -&gt;find();
// find books using an ARRAY column value
$books = BookQuery::create()
  -&gt;filterByTag('England')
  -&gt;find();
// find books using an ARRAY column values - ALL, SOME or NONE
$books = BookQuery::create()
  -&gt;filterByTags(array('England', 'satire'), Criteria::CONTAINS_SOME)
  -&gt;find();</pre></div>
</div>

<p>And this is not restricted to database platforms that actually support these column types. With Propel, ENUM, ARRAY and OBJECT column types work on MySQL, PostgreSQL, MSSQL, SQLite, and Oracle!</p>
<p>The Propel Documentation already contains a <a href="http://www.propelorm.org/wiki/Documentation/1.6/Advanced-Column-Types">full chapter</a> describing this feature, together with example usage. And of course, these features are fully unit tested, so you can use them right away.</p>
