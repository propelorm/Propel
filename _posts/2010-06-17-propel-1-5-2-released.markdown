---
layout: post
title: Propel 1.5.2 Released
published: true
---
<p>A little more than a month after the previous minor release, the Propel team is proud to announce version 1.5.2. This version is backwards compatible with the 1.5 branch, fixes more than 20 bugs, and adds a handful of features. The <a href="http://www.propelorm.org/wiki/Documentation/1.5/CHANGELOG">detailed changelog</a> is available in the Propel 1.5 documentation, so let’s focus on the enhancements.<!--more--></p>
<h3>Namespace support</h3>
<p>Propel can now generate Model classes using Namespaces, so your Propel Models will integrate smoothly into any PHP 5.3 application. This feature is optional, so there will be no change for your PHP 5.2 applications. Propel can even use the namespace to distribute model classes into subdirectories, so PHP5.3-flavored autoloaders will play well with your Propel classes.</p>
<p>This feature is documented in a new tutorial called <a href="http://www.propelorm.org/wiki/Documentation/1.5/Namespaces">“How to Use PHP 5.3 Namespaces”</a>, available in the ‘Docs’ tab of the Propel website.</p>
<div class="CodeRay">
  <div class="code"><pre>&lt;?phpuse Bookstore\AuthorQuery;$author = AuthorQuery::create()  -&gt;useBookQuery()    -&gt;filterByPrice(array('max' =&gt; 10))  -&gt;endUse()  -&gt;findOne();</pre></div>
</div>

<h3>Aggregate Column Behavior</h3>
<p>If you follow this blog, you may have discovered <a href="http://propel.posterous.com/getting-to-know-propel-15-keeping-an-aggregat">How to keep an aggregate column up-to-date</a> and <a href="http://propel.posterous.com/getting-to-know-propel-15-writing-a-behavior">How to write a behavior</a> based on the previous example. Well, it turns out that the code showcased in these tutorials is very useful, so we’ve added to the Propel core behaviors, together with unit tests and <a href="http://www.propelorm.org/wiki/Documentation/1.5/Behaviors/aggregate_column">complete documentation</a>. Now you won’t ever have to worry about denormalizing your model for better performance and simpler queries.</p>
<div class="CodeRay">
  <div class="code"><pre>&lt;table name=&quot;post&quot;&gt;  &lt;column name=&quot;id&quot; type=&quot;INTEGER&quot; required=&quot;true&quot; primaryKey=&quot;true&quot; autoIncrement=&quot;true&quot; /&gt;  &lt;column name=&quot;title&quot; type=&quot;VARCHAR&quot; required=&quot;true&quot; primaryString=&quot;true&quot; /&gt;  &lt;behavior name=&quot;aggregate_column&quot;&gt;    &lt;parameter name=&quot;name&quot; value=&quot;nb_comments&quot; /&gt;    &lt;parameter name=&quot;foreign_table&quot; value=&quot;comment&quot; /&gt;    &lt;parameter name=&quot;expression&quot; value=&quot;COUNT(id)&quot; /&gt;  &lt;/behavior&gt;&lt;/table&gt;</pre></div>
</div>

<h3><code>ModelCriteria::findOneOrCreate()</code></h3>
<p>This new method does exactly what its name says: it issues a <code>findOne()</code> query, and if the database returns no result, then it creates a record and populates it using the filters/conditions applied to the query. This is particularly useful for cross-reference tables in many-to-many relationships. This method is documented in the <a href="http://www.propelorm.org/wiki/Documentation/1.5/ModelCriteria#CreatingAnObjectBasedonaQuery">ModelCriteria Reference</a>.</p>
<div class="CodeRay">
  <div class="code"><pre>&lt;?php$bookTag = BookTagQuery::create()  -&gt;filterByBook($book)  -&gt;filterByTag('crime')  -&gt;findOneOrCreate();</pre></div>
</div>

<h3>Simple Templating Engine For Behaviors</h3>
<p>If you’ve dug into core behaviors, you may have found the code hard to read and debug. Thanks to a simple templating engine, future behaviors will offer a much cleaner syntax, without the need to worry about escaping dollars and quotes. The new <code>aggregate_column</code> is <a href="http://www.propelorm.org/browser/branches/1.5/generator/lib/behavior/aggregate_column/templates">built using this technique</a>, which you can learn in the new <a href="http://www.propelorm.org/wiki/Documentation/1.5/Writing-Behavior#UsingaTemplateForGeneratedCode">How to Write a Behavior Tutorial</a>.</p>
<div class="CodeRay">
  <div class="code"><pre>/** * Computes the value of the aggregate column &lt;?php echo $column-&gt;getName() ?&gt;  * * @param PropelPDO $con A connection object * * @return mixed The scalar result from the aggregate query */public function compute&lt;?php echo $column-&gt;getPhpName() ?&gt;(PropelPDO $con){  $stmt = $con-&gt;prepare('&lt;?php echo $sql ?&gt;');&lt;?php foreach ($bindings as $key =&gt; $binding): ?&gt;  $stmt-&gt;bindValue(':p&lt;?php echo $key ?&gt;', $this-&gt;get&lt;?php echo $binding ?&gt;());&lt;?php endforeach; ?&gt;  $stmt-&gt;execute();  return $stmt-&gt;fetchColumn();}</pre></div>
</div>

<h3>Query Comments</h3>
<p>If you need to ‘tag’ a query in order to be able to find it later in your logs, the best way is to add an SQL comment to it. It’s now easy thanks to the <code>ModelCriteria::setComment()</code> method. It works for SELECT queries as well as DELETE and UPDATE queries. You will find an example in the <a href="http://www.propelorm.org/wiki/Documentation/1.5/ModelCriteria#AddingAComment">ModelCriteria Reference</a>.</p>
<div class="CodeRay">
  <div class="code"><pre>&lt;?phpAuthorQuery::create()  -&gt;setComment('Author Deletion')  -&gt;filterByName('Leo Tolstoy')  -&gt;delete($con);// The comment ends up in the generated SQL query// DELETE /* Author Deletion */ FROM `author` WHERE author.NAME = 'Leo Tolstoy'</pre></div>
</div>

<h3>Miscellaneous</h3>
<p>The Model autoloader has been refactored to separate the autoloading of core Propel classes and Model classes. That should produce a small speed bump, and an easier integration of Propel into third-party libraries.</p>
<p>Also, exceptions thrown while executing a query should now be more useful, since the complete SQL query is now included in the <code>PropelException</code> message.</p>
<p>The XSD for the <code>schema.xml</code> has been greatly completed; it makes <a href="http://propel.posterous.com/easy-xml-autocompletion-in-propel-schemas">writing a schema with an IDE</a> extremely fast an error-proof.</p>
<h3>Upgrade</h3>
<p>You can see the enhancements of the 1.5.2 release as an incentive to upgrade quickly. Propel keeps getting better every day, and we hope that this short release cycle will help you to motivate your fellow developers to adopt Propel.</p>
<p><strong>Subversion tag</strong></p>
<div class="CodeRay">
  <div class="code"><pre>&gt; svn checkout http://svn.propelorm.org/tags/1.5.2</pre></div>
</div>

<p><strong>PEAR package</strong></p>
<div class="CodeRay">
  <div class="code"><pre>&gt; sudo pear upgrade propel/propel-generator&gt; sudo pear upgrade propel/propel-runtime</pre></div>
</div>

<p><strong>Download</strong></p>
<p><a href="http://files.propelorm.org/propel-1.5.2.tar.gz">http://files.propelorm.org/propel-1.5.2.tar.gz</a></p>
<p><a href="http://files.propelorm.org/propel-1.5.2.zip">http://files.propelorm.org/propel-1.5.2.zip</a></p>
