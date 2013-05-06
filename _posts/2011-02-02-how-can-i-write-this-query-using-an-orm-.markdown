---
layout: post
title: How Can I Write This Query Using An ORM?
published: true
---
<p>The Propel mailing lists often shows that typical question: How can I write this complicated query using the new Query syntax? The answer is not as simple as referring to the right section in the extensive <a href="http://www.propelorm.org/wiki/Documentation/1.5/ModelCriteria">Query documentation</a>, because most of the times the Query object is not the solution. And actually, the true answer is complicated, because it implies a deep understanding of the Object Relational Mapper approach. Let&rsquo;s see through a few examples how various answers can lead to a better usage of ORMs.<!--more--></p>
<h3>Answer #1: You Don&rsquo;t Need An ORM</h3>
<p>A recent post on the <a href="https://groups.google.com/forum/#!topic/propel-users/aZxqr48pnV4">propel-users</a> mailing list asked for the Propel version of the following query:</p>
<div class="CodeRay">
  <div class="code"><pre>SELECT COUNT(t1.user) AS users, t1.choice AS lft, t2.choice AS rgt
FROM Choices t1 iNNER JOIN Choices t2 ON (t1.user = t2.user)
WHERE t1.choice IN (...) AND t2.choice IN (...)
GROUP BY t1.choice, t2.choice;</pre></div>
</div>

<p>This query is not object-oriented, it&rsquo;s purely relational, so it doesn&rsquo;t need an Object-Relational Mapping. The best way to execute this query inside an ORM is to skip the ORM and use PDO directly:</p>
<div class="CodeRay">
  <div class="code"><pre>&lt;?php
$con = Propel::getConnection();
$query = 'SELECT COUNT(t1.user) AS users, t1.choice AS lft, t2.choice AS rgt
  FROM choice t1 iNNER JOIN choice t2 ON (t1.user = t2.user)
  WHERE t1.choice IN (?, ?) AND t2.choice IN (?, ?)
  GROUP BY t1.choice, t2.choice';
$stmt = $con-&gt;prepare($query);
$stmt-&gt;bindValue(1, 'foo');
$stmt-&gt;bindValue(2, 'bar');
$stmt-&gt;bindValue(3, 'baz');
$stmt-&gt;bindValue(4, 'foz');
$res = $stmt-&gt;execute();</pre></div>
</div>

<p>Hints of a purely relational query are:</p>
<ul>
<li>The SELECT part cherry-picks some columns of the main table</li>
<li>The SELECT part aggregates data from several tables</li>
<li>The selected columns use vendor-specific SQL functions</li>
<li>The query joins tables through columns that don&rsquo;t share a foreign key</li>
<li>The query is long and makes several joins</li>
<li>The query uses GROUP BY or HAVING</li>
<li>The user posts the query, but has no idea of the corresponding object model</li>
</ul>
<p>That&rsquo;s the most common answer to the &ldquo;How Can I Write&hellip;&rdquo; question. It is not a bad thing to resort to a direct database query inside a project using an ORM when it&rsquo;s the right tool for the job. If Propel makes the code much more complex to write, not reusable, or painfully slow, then don&rsquo;t use it. Be pragmatic.</p>
<h3>Answer #2: You Don&rsquo;t Need a Query Object</h3>
<p>Some queries appear closer to the object-oriented world but still very complex. The WHERE and JOIN parts still look very long, there may even be a subselect, but the user selects all the columns of the main table, and expects ActiveRecord objects as a result. For instance:</p>
<div class="CodeRay">
  <div class="code"><pre>// find all the books not reviewed by :name
SELECT * FROM book
WHERE id NOT IN (SELECT book_review.book_id FROM book_review
 INNER JOIN author ON (book_review.author_id=author.ID)
 WHERE author.last_name = :name);</pre></div>
</div>

<p>Crafting this query using Propel&rsquo;s Query objects (whether <code>Criteria</code> or <code>ModelCriteria</code>) would take a long time, or might even be close to impossible. But the query is already there, so why use the Query objects? To get Model objects as a result? You don&rsquo;t need Query objects for that, just use a <a href="http://www.propelorm.org/wiki/Documentation/1.5/BasicCRUD#UsingCustomSQL">formatter object</a>:</p>
<div class="CodeRay">
  <div class="code"><pre>&lt;?php
// prepare and execute an arbitrary SQL statement
$con = Propel::getConnection(BookPeer::DATABASE_NAME);
$sql = &quot;SELECT * FROM book WHERE id NOT IN &quot;
    .&quot;(SELECT book_review.book_id FROM book_review&quot;
    .&quot; INNER JOIN author ON (book_review.author_id=author.ID)&quot;
    .&quot; WHERE author.last_name = :name)&quot;;
$stmt = $con-&gt;prepare($sql);
$stmt-&gt;execute(array(':name' =&gt; 'Austen'));

// hydrate Book objects with the result
$formatter = new PropelObjectFormatter();
$formatter-&gt;setClass('Book');
$books = $formatter-&gt;format($stmt);</pre></div>
</div>

<p>Once again, if you already have a working query and if there is no possible reuse, PDO can be the right tool for the job. Propel can hydrate Model objects based on a PDO resultset, so all you need is a <code>PropelObjectFormatter</code>. Yan can even hydrate objects from several tables in a row (in a similar fashion to what Propel does with <code>with()</code>) using a properly configured Formatter object.</p>
<p>That&rsquo;s closer to the ORM philosophy, because you eventually deal with objects. But the Query itself is everything but object-oriented.</p>
<h3>Answer #3: You don&rsquo;t Need A Full Query</h3>
<p>Sometimes the query is just long, and users find it tedious to use Query methods instead of plain SQL. The problem often reveals a bad usage of the Query objects prior to that. For instance, consider the following query:</p>
<div class="CodeRay">
  <div class="code"><pre>SELECT * FROM book
LEFT JOIN author ON (book.AUTHOR_ID=author.ID)
WHERE book.TITLE like '%war%'
AND book.PRICE &lt; 10
AND book.PUBLISHED_AT &lt; now()
AND author.FAME &gt; 10;</pre></div>
</div>

<p>If someones asks for a Propel query version of the SQL query, it&rsquo;s probably because the job of adding simple methods to the Query class wasn&rsquo;t executed before. It&rsquo;s very likely that a previous query in the same project looked like:</p>
<div class="CodeRay">
  <div class="code"><pre>// find cheap books
SELECT * FROM book
WHERE book.PRICE &lt; 10;</pre></div>
</div>

<p>And another one looked like:</p>
<div class="CodeRay">
  <div class="code"><pre>// find published books
SELECT * FROM book
WHERE book.PUBLISHED_AT &lt; now();</pre></div>
</div>

<p>And maybe even one like:</p>
<div class="CodeRay">
  <div class="code"><pre>// find books by famous authors
SELECT * FROM book
LEFT JOIN author ON (book.AUTHOR_ID=author.ID)
WHERE author.FAME &gt; 10;</pre></div>
</div>

<p>You get the point: little pieces of the query are reusable, and may even have been written previously. The proper way to handle these cases would be to improve the <code>BookQuery</code> class little by little, as follows:</p>
<div class="CodeRay">
  <div class="code"><pre>&lt;?php
class BookQuery extends BaseBookQuery
{
  public function cheap($maxPrice = 10)
  {
    return $this-&gt;filterByPrice(array('max' =&gt; $maxPrice));
  }

  public function published()
  {
    return $this-&gt;filterByPublishedAt(array('max' =&gt; time()));
  }

  public function writtenByFamousAuthors($fameTreshold = 10)
  {
    return $this
      -&gt;leftJoin('Book.Author')
      -&gt;where('Author.Fame &gt; ?', $fameTreshold);
  }
}</pre></div>
</div>

<p>Now writing the query becomes trivial:</p>
<div class="CodeRay">
  <div class="code"><pre>&lt;?php
$books = BookQuery::create()
  -&gt;filterByTitle('%war%')
  -&gt;cheap()
  -&gt;published()
  -&gt;writtenByFamousAuthors();</pre></div>
</div>

<p>And since filtering on a word in the book title may be a common need, this ability should be added to the <code>BookQuery</code> class:</p>
<div class="CodeRay">
  <div class="code"><pre>&lt;?php
class BookQuery extends BaseBookQuery
{
  // ...
  public function titleContainsWord($word)
  {
    return $this-&gt;filterByTitle('%' . $word . '%');
  }</pre></div>
</div>

<p>Now the query is even easier to write, and more readable as well:</p>
<div class="CodeRay">
  <div class="code"><pre>&lt;?php
$books = BookQuery::create()
  -&gt;titleContainsWord('war')
  -&gt;cheap()
  -&gt;published()
  -&gt;writtenByFamousAuthors();</pre></div>
</div>

<p>The idea is to add meaningful methods to the Query class piece by piece, so you never have to bake complex SQL. By doing so, you will realize that the Query classes contains more and more of your business logic, while the database only contains data. That&rsquo;s a step further in the ORM paradigm.</p>
<h3>Answer #4: You Need More Than One Query</h3>
<p>Computer Science taught you to minimize queries, so if you were a good student, you might end up with queries looking like the following:</p>
<div class="CodeRay">
  <div class="code"><pre>// find all books written by Alexandre Dumas, fils, and Alexandre Dumas, père
SELECT * FROM book
LEFT JOIN author ON book.AUTHOR_ID=author.ID
WHERE author.LAST_NAME = 'Dumas';</pre></div>
</div>

<p>But in the Object-Oriented world, it&rsquo;s not a Bad Thing to execute several queries in a row. It may even make your code a lot clearer:</p>
<div class="CodeRay">
  <div class="code"><pre>&lt;?php
$dumasAuthors = AuthorQuery::create()
  -&gt;filterByLastName('Dumas');
  -&gt;find();
$books = BookQuery::create()
  -&gt;filterByAuthor($dumasAuthors) // ok, it's only possible in Propel 1.6 :)
  -&gt;find();</pre></div>
</div>

<p>By doing so, you move some logic away from the database (the join) and back to the PHP code (filtering by objects). You may pay the expense of an additional trip to the database, but in the end your model logic is more decoupled, and fully object-oriented. And depending on the indices present in the tables, some PHP logic and two SQL queries may be faster to execute than an single SQL query with all the logic.</p>
<p>Propel makes it even better: you can keep the single SQL query while actually using two query objects by <a href="http://www.propelorm.org/wiki/Documentation/1.5/Relationships#UsingRelationshipsInAQuery">embedding queries</a>. That&rsquo;s exactly what the <code>useXXXQuery()</code> methods allow:</p>
<div class="CodeRay">
  <div class="code"><pre>&lt;?php
$books = BookQuery::create()
  -&gt;useAuthorQuery()
    -&gt;filterByLastName('Dumas')
  -&gt;endUse()
  -&gt;find();</pre></div>
</div>

<p>Combining several query objects allows for very complex queries in a very reusable way.</p>
<h3>Answer #5: You Don&rsquo;t Use The Right Query</h3>
<p>While we are at separating queries, maybe part of the logic of a complex query can be moved to another <em>write</em> query. Let&rsquo;s see an example:</p>
<div class="CodeRay">
  <div class="code"><pre>// show all Dumas authors, together with the number of books they wrote
SELECT author.*, count(book.ID) as nb_books
FROM author LEFT JOIN book ON (author.ID = book.AUTHOR_ID)
WHERE author.LAST_NAME = 'Dumas'
GROUP BY author.ID;</pre></div>
</div>

<p>The <code>count()</code> might be expensive, especially on a large <code>book</code> table. It may be a better idea to denormalize the <code>author</code> table to add a <code>nb_books</code> column, updated each time a book is added or removed for a given author. Once again, this might sound counterintuitive to serious Computer Science students, but it&rsquo;s a very common technique in the ORM world.</p>
<p>Propel makes this kind of denormalization a piece of cake thanks to the <a href="http://www.propelorm.org/wiki/Documentation/1.5/Behaviors/aggregate_column"><code>aggregate_column</code> behavior</a>. In fact, you don&rsquo;t even have to worry about keeping the column up to date. Just set it up in the schema, and you&rsquo;re good to go:</p>
<div class="CodeRay">
  <div class="code"><pre>&lt;table name=&quot;author&quot;&gt;
  &lt;column name=&quot;id&quot; type=&quot;INTEGER&quot; required=&quot;true&quot; primaryKey=&quot;true&quot; autoIncrement=&quot;true&quot; /&gt;
  &lt;column name=&quot;first_name&quot; type=&quot;VARCHAR&quot; /&gt;
  &lt;column name=&quot;last_name&quot; type=&quot;VARCHAR&quot; required=&quot;true&quot; primaryString=&quot;true&quot; /&gt;
  &lt;behavior name=&quot;aggregate_column&quot;&gt;
    &lt;parameter name=&quot;name&quot; value=&quot;nb_books&quot; /&gt;
    &lt;parameter name=&quot;foreign_table&quot; value=&quot;book&quot; /&gt;
    &lt;parameter name=&quot;expression&quot; value=&quot;COUNT(id)&quot; /&gt;
  &lt;/behavior&gt;
&lt;/table&gt;
&lt;table name=&quot;book&quot;&gt;
  &lt;column name=&quot;id&quot; type=&quot;INTEGER&quot; required=&quot;true&quot; primaryKey=&quot;true&quot; autoIncrement=&quot;true&quot; /&gt;
  &lt;column name=&quot;title&quot; type=&quot;VARCHAR&quot; required=&quot;true&quot; primaryString=&quot;true&quot; /&gt;
  &lt;column name=&quot;author_id&quot; type=&quot;INTEGER&quot; /&gt;
  &lt;foreign-key foreignTable=&quot;author&quot; onDelete=&quot;cascade&quot;&gt;
    &lt;reference local=&quot;author_id&quot; foreign=&quot;id&quot; /&gt;
  &lt;/foreign-key&gt;
&lt;/table&gt;</pre></div>
</div>

<p>Now use the Query object to retrieve <code>Author</code> objects, and the number of books comes free of charge:</p>
<div class="CodeRay">
  <div class="code"><pre>&lt;?php
$authors = AuthorQuery::create()
  -&gt;filterByLastName('Dumas')
  -&gt;find();
foreach ($authors as $author) {
  echo $author-&gt;getFirstName(), ': ', $author-&gt;getNbBooks(), &quot;\n&quot;;
}</pre></div>
</div>

<p>Web applications often execute much more <em>read</em> queries than <em>write</em> queries. If a read query is complex and expensive in execution time, then you might consider simplifying it by adding more data at write time.</p>
<h3>Conclusion</h3>
<p>Propel offers a lot of ways to deal with complex queries. But if there is one thing to remember, it&rsquo;s that in an ORM world you should think about <strong>objects</strong>, not <strong>SQL</strong>. If you come up with a complex SQL query to translate, it means you&rsquo;ve probably taken the problem upside down. Put your business logic in the right place (in ActiveRecord or Query classes), and you&rsquo;ll quickly forget about the pain of complex SQL queries.</p>
