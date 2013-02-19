---
layout: post
title: Propel Gets Collections
published: true
---
<p>Propel 1.5 keeps bringing new features for a better developer experience and improved performance. Today, let's see the latest addition in the Propel runtime package: the PropelCollection objects.<br /><strong><br />From Arrays to Collections</strong><p /> In Propel 1.4, the results of a `doSelect()` call used to be an array of model objects. That made iteration on the results of a query very straighforward:<br />&nbsp;<br />[code]
&lt;?php
// doSelect() returns an array
 $books = BookPeer::doSelect(new Criteria()); // $books is an array of Book objects
?&gt;
There are &lt;?php echo count($books) ?&gt; books:
&lt;ul&gt;
&nbsp; &lt;?php foreach ($books as $book): ?&gt;
&nbsp; &lt;li&gt;
 &nbsp;&nbsp;&nbsp; &lt;?php echo $book-&gt;getTitle() ?&gt;
&nbsp; &lt;/li&gt;
&nbsp; &lt;?php endforeach; ?&gt;
&lt;/ul&gt;
[/code]<p />Propel 1.5 introduces a <a href="http://propel.posterous.com/propels-criteria-gets-smarter">new way to make queries on your model object</a>. It's the occasion to improve the way Propel returns the results of a query. So starting with Propel 1.5, model queries return a <strong>collection object</strong> instead of an array. <p /> First, let's see what doesn't change. You can iterate over a collection object just like you do with an array:<p />[code]
 &lt;?php
// find() returns a PropelCollection, which you can use just like an array
$books = PropelQuery::from('Book')-&gt;find(); // $books is a PropelObjectCollection of Book objects
?&gt;
There are &lt;?php echo count($books) ?&gt; books:
 &lt;ul&gt;
&nbsp; &lt;?php foreach ($books as $book): ?&gt;
&nbsp; &lt;li&gt;
&nbsp;&nbsp;&nbsp; &lt;?php echo $book-&gt;getTitle() ?&gt;
&nbsp; &lt;/li&gt;
&nbsp; &lt;?php endforeach; ?&gt;
&lt;/ul&gt;
[/code]<p />As you can see, no modification to the template code was required. `foreach()`, `count()`, `append()`, and even `unset()` can be executed on a PropelCollection object as you usually do on an array. This is because the `PropelCollection` class extends `<a href="http://www.php.net/manual/en/class.arrayobject.php">ArrayObject</a>`, one of the new <a href="http://www.php.net/manual/en/book.spl.php">SPL classes</a> introduced by PHP 5. <p /> <strong>Tip</strong>: The generated `doSelect()` methods in your Peer classes keep on returning arrays in Propel 1.5. It's only if you use the new Query API that you get Collections in return. That makes this new feature completely backwards compatible with existing Propel code.<p /> <strong>PropelCollection Abilities</strong><p />A PropelCollection is more than just an array. First of all, you can call some special methods on it. Check the following example:<p />[code]
&lt;?php if($books-&gt;isEmpty()): ?&gt;
 There are no books.
&lt;?php else: ?&gt;
There are &lt;?php echo $books-&gt;count() ?&gt; books:
&lt;ul&gt;
&nbsp; &lt;?php foreach ($books as $book): ?&gt;
&nbsp; &lt;li class="&lt;?php echo $books-&gt;isOdd() ? 'odd' : 'even' ?&gt;"&gt;
 &nbsp;&nbsp;&nbsp; &lt;?php echo $book-&gt;getTitle() ?&gt;
&nbsp; &lt;/li&gt;
&nbsp; &lt;?php if($books-&gt;isLast()): ?&gt;
&nbsp; &lt;li&gt;Do you want more books?&lt;/li&gt;
&nbsp; &lt;?php endif; ?&gt;
&nbsp; &lt;?php endforeach; ?&gt;
&lt;/ul&gt;
 &lt;?php endif; ?&gt;
[/code]<p />In this example, `isEmpty()`, `count()`, `isOdd()`, and `isLast()` are all methods of the `PropelObjectCollection` instance returned by `find()`. But there is more. The collection object offers methods allowing to alter the objects it contains:<p /> [code]
&lt;?php
foreach ($books as $book) {
&nbsp; $book-&gt;setIsPublished(true);
}
$books-&gt;save();
?&gt;
[/code]<p />Notice how the `save()` method is not called on each object, but on the collection object. This groups all the `UPDATE` queries into a single database transaction, which is faster than individual saves. A PropelCollection also allows you to delete all the objects in the collection in a single call with `delete()`, or to retrieve the primary keys with `getPrimaryKeys()`.<p /> Lastly, a `PropelCollection` can be exported to an array of arrays, so that you can easily inspect the results of a query. It is as simple as calling `toArray()` on a collection object:<p />[code]
 &lt;?php
$books = PropelQuery::from('Book')
&nbsp; -&gt;with('Book.Author')
&nbsp; -&gt;with('Book.Publisher')
&nbsp; -&gt;find();
print_r($books-&gt;toArray());
/* =&gt; array(
&nbsp;&nbsp;&nbsp; array(
 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 'Id'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; =&gt; 123,
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 'Title'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; =&gt; 'War And Peace',
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 'ISBN'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; =&gt; '3245234535',
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 'AuthorId'&nbsp;&nbsp;&nbsp; =&gt; 456,
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 'PublisherId' =&gt; 567
 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 'Author'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; =&gt; array(
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 'Id'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; =&gt; 456,
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 'FirstName'&nbsp;&nbsp; =&gt; 'Leo',
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 'LastName'&nbsp;&nbsp;&nbsp; =&gt; 'Tolstoi'
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ), 
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 'Publisher'&nbsp;&nbsp; =&gt; array(
 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 'Id'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; =&gt; 567,
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 'Name'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; =&gt; 'Penguin'
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; )
&nbsp;&nbsp;&nbsp;&nbsp; ),
&nbsp;&nbsp;&nbsp; array(
 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 'Id'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; =&gt; 535,
 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 'Title'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; =&gt; 'Pride And Prejudice',
 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 'ISBN'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; =&gt; '5665764586',
 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 'AuthorId'&nbsp;&nbsp;&nbsp; =&gt; 853,
 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 'PublisherId' =&gt; 567
 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 'Author'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; =&gt; array(
 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 'Id'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; =&gt; 853,
 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 'FirstName'&nbsp;&nbsp; =&gt; 'Jane',
 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 'LastName'&nbsp;&nbsp;&nbsp; =&gt; 'Austen'
 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ), 
 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 'Publisher'&nbsp;&nbsp; =&gt; array(
 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 'Id'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; =&gt; 567,
 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 'Name'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; =&gt; 'Penguin'
 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; )
 &nbsp;&nbsp;&nbsp;&nbsp; ),
&nbsp; ) */
[/code]<p /><strong>Using An Alternative Collection</strong><p />If what you need is actually an array of arrays, you'd better skip the collection of objects completely, and use a colleciton of arrays instead. This is easily done by specifying an alternative formatter when building the query, as follows:<p /> [code]
 &lt;?php
 $books = PropelQuery::from('Book')
 &nbsp; -&gt;with('Book.Author')
 &nbsp; -&gt;with('Book.Publisher')
&nbsp; -&gt;setFormatter(ModelCriteria::FORMAT_ARRAY)
 &nbsp; -&gt;find();
[/code]<p />Now, the result of the query is not a `PropelObjectCollection` anymore, but a `PropelArrayCollection`. The elements in the collection are associative arrays, where the keys are the column names:<p /> [code]
&lt;?php 
foreach ($books as $book) {
&nbsp; echo $book['Title'];
}
[/code]<p />And if you think that using a Collection object rather than a simple array is a bad idea regarding performance and memory consumption, try the new `PropelOnDemandCollection`. It behaves just like the `PropelObjectCollection`, except that the model objects are hydrated row-by-row and then cleaned up so that the query uses the same memory for 5 results as for 50,000:<p /> [code]
 &lt;?php
 $books = PropelQuery::from('Book')
 &nbsp; -&gt;setFormatter(ModelCriteria::FORMAT_ON_DEMAND)
&nbsp; -&gt;limit(50000)
 &nbsp; -&gt;find();
// You won't get a Fatal error for not enough memory with the following code
 foreach($books as $book) {
&nbsp; echo $book-&gt;getTitle();
}
[/code]<p />For those who want to deal with `PDOStatement` instances themselves, the `ModelCriteria::FORMAT_STATEMENT` formatter is at your disposal.<p /> <strong>Going Further</strong><p />The Formatter/Collection system in the new Propel Query architecture is very extensible, so it's very easy to write a new formatter and collection objects to package your own custom hydration logic.<p /> Of course, as usual with Propel 1.5, this feature is fully unit tested and <a href="http://propel.phpdb.org/trac/wiki/Users/Documentation/1.5/ModelCriteria#PropelCollectionMethods">already documented</a>. So you can start using it right now in the 1.5 branch.</p>
