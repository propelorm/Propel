---
layout: post
title: ! 'Propel Queries: Now With Manual Binding, Too'
published: true
---
<p>Propel is quite good at guessing the binding type to use in your queries. But sometimes you need to force a binding type which is not the one Propel would have guessed. Starting with the next minor release (1.6.4), Propel will be able to do it.<!--more--></p>
<h3>Propel Guesses Binding Types From Your Schema</h3>
<p>Consider the following query:</p>
<div class="CodeRay">
  <div class="code"><pre>$books = BookQuery::create()
    -&gt;filterByTitle('War%')
    -&gt;filterByPrice(array('max' =&gt; 20))
    -&gt;find();</pre></div>
</div>

<p>Propel translates this query into the following SQL prepared statement:</p>
<div class="CodeRay">
  <div class="code"><pre>SELECT book.* FROM book
 WHERE book.TITLE LIKE ?
   AND book.PRICE &lt; ?</pre></div>
</div>

<p>Then, when you call <code>find()</code>, Propel uses PDO to <em>bind</em> the question mark placeholders with the values used in the <code>filterByXXX()</code> methods. Propel uses the binding type of the column as declared in the schema. Continuing on the previous example, where the <code>book.TITLE</code> column is a <code>VARCHAR</code> and the <code>book.PRICE</code> column is a <code>INTEGER</code>, Propel binds the values as follows:</p>
<div class="CodeRay">
  <div class="code"><pre>$stmt = $con-&gt;prepare($sql);
$stmt-&gt;bindValue(1, 'War%', PDO::PARAM_STR); // book.TITLE is a  VARCHAR
$stmt-&gt;bindValue(2, 20, PDO::PARAM_INT);     // book.PRICE is an INTEGER
$stmt-&gt;execute();</pre></div>
</div>

<p>But what if you want to use another binding type?</p>
<h3>Cases When You Need a Custom Binding Type</h3>
<p>The <code>filterByXXX()</code> methods are always tied to a column, so for these Propel always knows what binding to use. However, when you use the <a href="http://www.propelorm.org/reference/model-criteria.html#relational_api">relational API</a>, you can create conditions on more than just columns.</p>
<p>For instance:</p>
<div class="CodeRay">
  <div class="code"><pre>$books = BookQuery::create()
  -&gt;where(&quot;LOCATE('War', Book.Title) = ?&quot;, true)
  -&gt;find();</pre></div>
</div>

<p>In this case, the binding should use <code>PDO::PARAM_BOOL</code>, and not <code>PDO::PARAM_STR</code>, which is the type Propel uses for the <code>book.TITLE</code> column, declared as <code>VARCHAR</code>.</p>
<p>Another example is when using <code>having()</code>:</p>
<div class="CodeRay">
  <div class="code"><pre>$books = BookQuery::create()
  -&gt;withColumn('SUBSTRING(Book.Title, 1, 4)', 'title_start')
  -&gt;having('title_start = ?', 'foo')
  -&gt;find();</pre></div>
</div>

<p>Here, Propel simply refuses the query, failing with a loud:</p>
<div class="CodeRay">
  <div class="code"><pre>PropelException: Cannot determine the column to bind to the parameter in clause 'title_start = ?'</pre></div>
</div>

<p>This is because the virtual column <code>title_start</code> has no intrinsec type, so Propel cannot determine which binding to use.</p>
<p>Concretely, that means that the <code>having()</code> support is somehow broken in Propel 1.6. Apart from concatenating the value to the SQL clause (and risking SQL injection), you cannot add a <code>HAVING</code> clause using <code>ActiveQuery</code>...</p>
<h3>Forcing a Custom Binding Type.</h3>
<p>...until now. A way to force a custom binding type <a href="https://github.com/propelorm/Propel/pull/182">has just made its way</a> to the <a href="https://github.com/propelorm/Propel">Propel 1.6 master branch</a> - and that means that it will be available in Propel 1.6.4.</p>
<p>It's as simple as it should be: just add the desired binding type as third parameter of either <code>where()</code> or <code>having()</code>, and you're good to go:</p>
<div class="CodeRay">
  <div class="code"><pre>// custom binding in where()
$books = BookQuery::create()
  -&gt;where(&quot;LOCATE('War', Book.Title) = ?&quot;, true, PDO::PARAM_BOOL)
  -&gt;find();

// custom binding in having()
$books = BookQuery::create()
  -&gt;withColumn('SUBSTRING(Book.Title, 1, 4)', 'title_start')
  -&gt;having('title_start = ?', 'foo', PDO::PARAM_STR)
  -&gt;find();</pre></div>
</div>

<p>No more errors, no more SQL injection risk. You're in control of the binding type when you need it.</p>
<p>More than ever, there is no limit to what you can do with Propel - and less limits to what you can do with the awesome PropelQuery API.</p>
