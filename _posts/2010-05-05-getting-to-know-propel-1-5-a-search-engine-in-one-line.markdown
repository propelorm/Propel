---
layout: post
title: ! 'Getting To Know Propel 1.5: A Search Engine In One Line'
published: true
---
<p>The ability to reuse elements of a query was dramatically improved with Propel 1.5. A great example of this new flexibility is how easy it is to build a full-text search engine. Or, to use the 1.5 vocabulary, a <em>text filter</em>. Let’s see how to allow full-text search to a Bookstore with a single line.</p>
<h3>The Naive Approach</h3>
<p>A full-text search engine for books should return results where the search input appears in the book title, or in the book summary. Databases support simple regular expression comparison, so the fastest way to implement the search engine looks like:</p>
<div class="CodeRay">
  <div class="code"><pre>class BookQuery extends BaseBookQuery{  public function filterByText($text)  {    $pattern = '%' . $text . '%';    return $this      -&gt;where('Book.Title like ?', $pattern)      -&gt;orWhere('Book.Summary like ?', $pattern);  }}</pre></div>
</div>

<p><!--more-->The new <code>filterByText()</code> method uses <code>where()</code> and <code>orWhere()</code>, which are part of the <a href="http://www.propelorm.org/wiki/Documentation/1.5/ModelCriteria#RelationalAPI">relational API</a>, the secondary set of methods offered by <code>ModelCriteria</code>. It also returns the current object, so it can be chained together with other query methods. Now, adding a full-text search to a query is really a one-liner:</p>
<div class="CodeRay">
  <div class="code"><pre>$books = BookQuery::create()  -&gt;filterByText('pride')  -&gt;orderByTitle()  -&gt;find();</pre></div>
</div>

<p>The <code>$books</code> collection gets hydrated from the results of the following SQL query:</p>
<div class="CodeRay">
  <div class="code"><pre>SELECT book.* from `book`WHERE (book.TITLE LIKE '%pride%' OR book.SUMMARY LIKE '%pride%')ORDER BY book.TITLE ASC;</pre></div>
</div>

<p>You can use the new <code>filterByText()</code> method to <em>count</em> books matching the string, or to look for <em>authors</em> of books matching the string:</p>
<div class="CodeRay">
  <div class="code"><pre>$authors = AuthorQuery::create()  -&gt;useBookQuery()    -&gt;filterByText('pride')  -&gt;endUse()  -&gt;orderByLastName()  -&gt;find();</pre></div>
</div>

<p><strong>Tip</strong>: If you’re a symfony user, you can even use the new filter method as an <em>admin generator filter</em> with <a href="http://www.symfony-project.org/plugins/sfPropel15Plugin">sfPropel15Plugin</a>. Just add the <code>text</code> filter to your list, and you’re done:</p>
<div class="CodeRay">
  <div class="code"><pre># in modules/book/config/generator.ymlconfig:  filter:    display: [text]</pre></div>
</div>

<h3>Using An Index</h3>
<p>The previous approach is naive, because it doesn’t scale. When the book table reaches more than a few thousand rows, a SQL query using <code>LIKE</code> and <code>OR</code> is likely to hit the slow query limit. The usual workaround is to build a table of searchable words, and to use this (indexed) table for full text searches.</p>
<p>So let’s add a <code>book_index</code> table, related to the <code>book</code> table by a <code>book_id</code> foreing key. The index table also features a <code>word</code> column, with an index.</p>
<div class="CodeRay">
  <div class="code"><pre>&lt;table name=&quot;book_index&quot; phpName=&quot;BookIndex&quot;&gt;  &lt;column name=&quot;id&quot; type=&quot;INTEGER&quot; required=&quot;true&quot; primaryKey=&quot;true&quot; autoIncrement=&quot;true&quot; /&gt;  &lt;column name=&quot;book_id&quot; required=&quot;true&quot; type=&quot;INTEGER&quot; /&gt;  &lt;foreign-key foreignTable=&quot;book&quot; onDelete=&quot;cascade&quot;&gt;    &lt;reference local=&quot;book_id&quot; foreign=&quot;id&quot; /&gt;  &lt;/foreign-key&gt;  &lt;column name=&quot;word&quot; type=&quot;VARCHAR&quot; required=&quot;true&quot; primaryString=&quot;true&quot; /&gt;  &lt;index&gt;    &lt;index-column name=&quot;word&quot; /&gt;  &lt;/index&gt;&lt;/table&gt;</pre></div>
</div>

<p>This table should fill up automatically each time a new <code>Book</code> is added. The simplest way to do this is to use a <code>postSave()</code> hook in the <code>Book</code> ActiveRecord class:</p>
<div class="CodeRay">
  <div class="code"><pre>class Book extends BaseBook{  public function postSave(PropelPDO $con = null)  {    // delete previous words from this book    BookIndexQuery::create()      -&gt;filterByBook($this)      -&gt;delete($con);    // build the list of words for this book    $titleWords = preg_split('/\W/', $this-&gt;getTitle(), null, PREG_SPLIT_NO_EMPTY);    $summaryWords = preg_split('/\W/', $this-&gt;getSummary(), null, PREG_SPLIT_NO_EMPTY);    $words = array_unique(array_merge($titleWords, $summaryWords));    // Save the words for this book    foreach ($words as $word) {      $index = new BookIndex();      $index-&gt;setBook($this);      $index-&gt;setWord($word);      $index-&gt;save($con);    }  }}</pre></div>
</div>

<p><strong>Tip</strong>: All the database operations inside <code>postSave()</code> use the connection object (<code>$con</code>), to guarantee <a href="http://www.propelorm.org/wiki/Documentation/1.5/Transactions">transactional integrity</a> and better performance.</p>
<p>Now the <code>BookQuery::filterByText()</code> method is even easier to write:</p>
<div class="CodeRay">
  <div class="code"><pre>class BookQuery extends BaseBookQuery{  public function filterByText($text)  {    return $this      -&gt;useBookIndexQuery()        -&gt;filterByWord($text)      -&gt;endUse();  }}</pre></div>
</div>

<p>And the full-text search engine scales. Besides, the syntax to use the engine didn’t change:</p>
<div class="CodeRay">
  <div class="code"><pre>$books = BookQuery::create()  -&gt;filterByText('pride')  -&gt;orderByTitle()  -&gt;find();</pre></div>
</div>

<p>Query methods also bring the benefit of easy refactoring, since the actual implementation of a filter is encapsulated inside a method.</p>
<h3>Using A Search Engine</h3>
<p>The new approach won’t give very accurate results on a text search - don’t use it for a real world application. Most notably, there is no notion of relevancy that could be used to order the results, placing the books most likely to match the query at the top. Also, the index table fills up with lots of useless (because non-discriminating) words like “the”, or “and”. And a search for “man” won’t return any book featuring “men”. And the index soon grows very large, so that even a solid MySQL server can’t handle it with reasonable response time.</p>
<p>This is because there is much more to full-text search than just building an index. From <a href="http://en.wikipedia.org/wiki/Stop_words">stop words</a> to <a href="http://en.wikipedia.org/wiki/Stemming">stemming</a>, the domain of text search is very complex. You can safely assume that <em>you won’t be able to build a good quality search engine on your own</em> - and that means that you should use an existing solution instead.</p>
<p>And there are a lot of cheap search solutions, including many open-source ones. One could use PostreSQL’s excellent <a href="http://www.postgresql.org/docs/8.4/static/textsearch.html">Full Text Search capabilities</a>, or <a href="http://framework.zend.com/manual/en/zend.search.lucene.html">Zend_Search_Lucene</a>, or even <a href="http://www.google.com/cse/">Google</a>, to provide the search feature for a bookstore. It doesn’t really matter for the present exercise. Let’s just assume that the external search engine supports queries through a Web Service.</p>
<p>Such search engines often return results in XML, including references to the unique identifiers of the indexed documents. In case of a bookstore, the search engine result would probably feature book ids, among other data like an excerpt from the matching content, or the matching accuracy. That would make the <code>filterByText()</code> method look like:</p>
<div class="CodeRay">
  <div class="code"><pre>class BookQuery extends BaseBookQuery{  protected static $searchUri = 'http://myengine.mydomain.com/search?q=';  public function filterByText($text)  {    $sxe = new SimpleXMLElement(self::$searchUri . $text, NULL, true);    $bookIds = array();    foreach ($xse-&gt;results-&gt;book as $book) {      $bookIds []= (int) $book-&gt;id;    }    return $this      -&gt;filterById($bookIds);  }}</pre></div>
</div>

<p><code>BookQuery::filterById()</code> accepts an array of primary keys, which translates into a SQL IN (). So the <code>filterByText()</code> method still returns a modified <code>BookQuery</code> matching the <code>$text</code> pattern, even though the actual searching took place somewhere else. And the search API for the books didn’t change:</p>
<div class="CodeRay">
  <div class="code"><pre>$books = BookQuery::create()  -&gt;filterByText('pride')  -&gt;orderByTitle()  -&gt;find();</pre></div>
</div>

<h3>Conclusion</h3>
<p>Even if the code samples presented here are more a proof-of-concept than real life implementations, this is a good example of the flexibility of the <a href="http://www.propelorm.org/wiki/Documentation/1.5/WhatsNew#NewQueryAPI">new Query API</a> introduced in Propel 1.5. Not only can you build complex logic inside a simple query method, you can also reuse this logic very easily, without adding complexity to the public API of the model. And if you can build a search engine in a few minutes, imagine the wonders that can come out if you spend as much time with Propel as you used to do with SQL…</p>
