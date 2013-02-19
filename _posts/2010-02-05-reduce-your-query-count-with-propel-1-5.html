---
layout: post
title: Reduce Your Query Count With Propel 1.5
published: true
---
<p>Propel 1.5 offers a lot of new tools to help you reduce your query count. If you thought that the generated `doSelectJoin()` methods of Propel 1.2 were a great help, you're going to love Propel 1.5.<p /><strong>Instance Pool</strong><p /> But first, let's remind those of you who are not familiar with ORM terminology of one important notion: ''hydration''. Hydration is the process of populating a PHP object from a row in a database query result. In Propel, every generated model object offers a `hydrate()` method just for that purpose. <p /> This hydration process can take some time, so Propel stores all the hydrated objects in an internal registry, called the ''instance pool''. If you try to hydrate the same row twice in a script, then Propel returns the object hydrated the first time, that it has kept in the instance pool. <p /> Even better, if you need to retrieve a row using its primary key, and if an object was already hydrated from that row, Propel won't even execute the database query, but instead it will return the requested object from the instance pool. This is a precious time saver for queries like the following:<p /> [code]
$books = BookQuery::create()-&gt;find();&nbsp;&nbsp;&nbsp;&nbsp; // one database query
$authors = AuthorQuery::create()-&gt;find(); // one database query
foreach&nbsp;&nbsp;&nbsp; ($books as $book) {
&nbsp; echo $book-&gt;getAuthor()-&gt;getName();&nbsp;&nbsp;&nbsp;&nbsp; // no query, since all the author rows are already hydrated
 }
[/code]<p />Object Instance Pool has been in Propel since version 1.3. Chances are that you alredy benefit from it.<p /><strong>Collection Relation Population</strong><p />But the previous example does not correspond to any real use case. In practice, you usually retrieve a subset of all the Authors and Books, and you can never be sure that the instance pool will contain the related object you need according to the query you make.<p /> Fortunately, Propel offers a quick way to hydrate the objects related to a list of objects. This feature uses the fact that Propel queries return a `PropelCollection` object and not an array. That means that you can call methods on the results of a query. Starting with Propel 1.5, the `PropelObjectCollection` class offers a `populateRelation()` method that will change your life:<br /> <!--more--><br />[code]
$books = BookQuery::create()-&gt;find();&nbsp;&nbsp;&nbsp;&nbsp; // one database query
$books-&gt;populateRelation('Author');&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; // one database query
foreach&nbsp;&nbsp;&nbsp; ($books as $book) {
&nbsp; echo $book-&gt;getAuthor()-&gt;getName();&nbsp;&nbsp;&nbsp;&nbsp; // no query, since the necessary author rows are already hydrated
 }
[/code]<p />The difference from the previous example is that `populateRelation()` retrieves and hydrates only the `Author` objects related to the `Book` objects present in the `$books` collection. So `populateRelation()` needs a single effective query to reduce the query count.<p /> And the greatest thing about `populateRelation()` is that it also works the other way around, that means for one-to-many relationships:<p />[code]
$authors = AuthorQuery::create()-&gt;find(); // one database query
 $authors-&gt;populateRelation('Book');&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; // one database query
foreach&nbsp;&nbsp;&nbsp; ($authors as $author) {
&nbsp; foreach ($author-&gt;getBooks() as $book) { // no query, since the necessary book rows are already hydrated
 &nbsp;&nbsp;&nbsp; echo $book-&gt;getTitle(), $author-&gt;getName();&nbsp;&nbsp;&nbsp;&nbsp; // no query either
&nbsp; }
}
[/code]<p />No more scripts with a query count proportional to the number of results! `PropelObjectCollection::populateRelation()` will keep the query count reasonnable in all situations, in all database models.<p /> <strong>Query With Class</strong><p />There is one limitation, though. `populateRelation()` only allows to hydrate relations of the main object. You can't hydrate, for instance, relations of a relation. For that purpose, you will need to add one line in your Query, and it's called `joinWith()`.<p /> `joinWith()` expects a composed relation name ('Start.End') and can be called several times in a query. It tells the query to also hydrate the related objects on a many-to-one relationship. That makes the following query possible:<p /> [code]
$books = BookQuery::create()
&nbsp; -&gt;joinWith('Book.Author')
&nbsp; -&gt;joinWith('Book.Publisher')
&nbsp; -&gt;joinWith('Publisher.Group')
&nbsp; -&gt;find(); // one database query
 foreach ($books as $book) {
&nbsp; echo $book-&gt;getAuthor()-&gt;getName();&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; // no query
&nbsp; echo $book-&gt;getPublisher()-&gt;getName();&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; // no query
&nbsp; echo $book-&gt;getPublisher()-&gt;getGroup()-&gt;getName(); // no query
 }
[/code]<p />`joinWith()` doesn't work with one-to-many relationships, because it would require some very heavy code to handle the LIMIT clause in a query. And since `populateRelation()` deals with most of the use cases, Propel keeps its codebase lightweight and fast by not implementing `joinWith()` on one-to-many relationships.<p /> <strong>Query With Column</strong><p />Sometimes you don't need to hydrate a full object in addition to the main object. If you only need one additional column, the `withColumn()` method is a good alternative to `joinWith()`:<p /> [code]
$book = PropelQuery::from('Book')
&nbsp; -&gt;join('Book.Author')
&nbsp; -&gt;withColumn('Author.Name', 'AuthorName')
&nbsp; -&gt;findOne();
$authorName = $book-&gt;getAuthorName();
 [/code]<p />Propel adds the 'with' column to the SELECT clause of the query, and uses the second argument of the `withColumn()` call as a column alias. This additional column is later available as a 'virtual' column, i.e. using a getter that does not correspond to a real column. You don't actually need to write the `getAuthorName()` method ; Propel uses the magic `__call()` method of the generated `Book` class to catch the call to a virtual column.<p /> `withColumn()` is also of great use to add calculated columns:<p />[code]
$authors = PropelQuery::from('Author')
&nbsp; -&gt;leftJoin('Author.Book')
&nbsp; -&gt;withColumn('COUNT(Book.Id)', 'NbBooks')
 &nbsp; -&gt;groupBy('Author.Id')
&nbsp; -&gt;find();
foreach ($authors as $author) {
&nbsp;&nbsp;&nbsp; echo $author-&gt;getName() . ': ' . $author-&gt;getNbBooks() . " books\n";
}
[/code]<p />With a single SQL query, you can have both a list of objects and an additional column for each object. This makes `withColumn()` a great query saver.<p /> Of course, you can call `withColumn()` multiple times to add more than one virtual column to the resulting objects.<p /><strong>Conclusion</strong><p />Instance pooling, collection relation population, `joinWith()`, and `withColumn()` are the new weapons that Propel 1.5 gives you to fight for your application performance. You will find them very easy to use, and you will soon wonder how you could program without them in the past!</p>
