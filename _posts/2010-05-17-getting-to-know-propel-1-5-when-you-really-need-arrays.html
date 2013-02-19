---
layout: post
title: ! 'Getting To Know Propel 1.5: When You Really Need Arrays'
published: true
---
<p>Web applications spend a large share of their code transforming arrays of data. PHP is a wonderful language for that, because it offers a lot of array manipulation functions. But web developers are actually required to translate business logic into a program - not to mess up with arrays. In fact, web developers should spend the least possible amount of time dealing with arrays, because this time is lost. A piece of code transforming an array is usually not very reusable, it doesn&rsquo;t carry any logic, and it&rsquo;s a pain to test and maintain.</p>
<p>Propel, as other ORMs, advocates the use of objects rather than arrays. But it turns out that you sometimes need an array representation of your model objects. Propel makes these situations painless by offering the shorcuts you need at the right time.<!--more--></p>
<h3>From ActiveRecord Objects To Arrays</h3>
<p>One of the first goals of Propel is to replace the data structure of a database record, represented as an array by PDO, with an ActiveRecord object. Instead of accessing the columns of a record using an array interface, ActiveRecord objects offer getter and setter methods. The objects promote encapsulation, hide some columns from the end user, and can offer new, &lsquo;virtual&rsquo; columns, calculated from other columns or even data from other tables. The Propel Guide <a href="http://www.propelorm.org/wiki/Documentation/1.5/BasicCRUD">explains this concept in detail</a>, so there should be nothing new there:</p>
<p><script src="https://gist.github.com/352c760b320128ee0871.js"></script></p>
<p>However, it is sometimes useful to get an array representation of an ActiveRecord object. Whether for debugging purposes, or to dump data for later reuse, arrays are sometimes simpler to deal with than objects. The conversion is very straightforward with Propel:</p>
<p><script src="https://gist.github.com/b5d4a679e957a48862c6.js"></script></p>
<p>Note that if you hydrated a model object together with its relations, then <code>toArray()</code> can be even more powerful than that. Just set the third argument to <code>true</code> to also convert relations to arrays:</p>
<p><script src="https://gist.github.com/2f0a465195abb93dfa13.js"></script></p>
<p>You can also populate an empty Model object from an associative array by calling <code>fromArray()</code>. That&rsquo;s a quick way to set several properties an once; it&rsquo;s especially useful if you provide a form to edit a model object using phpNames as input names.</p>
<p><script src="https://gist.github.com/dcee9b14b4f2b7d66ff7.js"></script></p>
<h3>PropelCollections Give You The Array You Need</h3>
<p>Propel 1.5 introduces <code>PropelCollections</code>, which are a wonderful way of not messing up with arrays. At first sight, a collection looks like an array, and behaves like an array. It also provides additional abilities, already illustrated in a <a href="http://propel.posterous.com/propel-gets-collections">previous article</a> in this very blog:</p>
<p><script src="https://gist.github.com/c6c77f8b05beead02492.js"></script></p>
<p>But now, what if you actually need an array, for a special purpose? For instance, an <a href="http://docs.jquery.com/Plugins/Autocomplete">autocomplete field using jQuery</a> requires an action returning an associative array of book titles, indexed by primary key. Propel makes it trivial to transform a Collection object into such an array:</p>
<p><script src="https://gist.github.com/4eac73ab13c1b7711070.js"></script></p>
<p>The first argument of <code>toKeyValue()</code> is the name of the column to use for the array index, the second is the name of the column to use for the array value. In fact, <code>toKeyValue()</code> is a little smarter than that. With no argument, it returns an associative array indexed by primary key, and uses the ActiveRecord <code>__toString()</code> method. So, provided the <code>title</code> column is declared as <code>primaryString</code> in the <code>book</code> schema, you can get the same associative array by calling:</p>
<p><script src="https://gist.github.com/3bd3c16f403beff64988.js"></script></p>
<p>If you need more than just a key and a value, then <code>PropelCollection::toArray()</code> is probably the method you need. It turns a collection into an array of associative arrays, much like the ones you get when calling individually <code>toArray()</code> on an ActiveRecord object:</p>
<p><script src="https://gist.github.com/93c455cbfeacfac69046.js"></script></p>
<h3>Reindexing A Collection</h3>
<p>Propel collection are indexed incrementally, for performance reasons. That means that the first element in a collection always uses 0 as index, the second uses 1, and so on. It makes methods like <code>isFirst()</code>, <code>isLast()</code>, or <code>isOdd()</code> fast and efficient, but it forbids the use of a custom index.</p>
<p>Fortunately, <code>PropelCollection::getArrayCopy()</code> accepts a column name as first argument, and returns an array of Model objects indexed by the chosen column:</p>
<p><script src="https://gist.github.com/8190328c961194e7d8e1.js"></script></p>
<p><code>toArray()</code> also accepts a column name as first argument to choose a custom index column:</p>
<p><script src="https://gist.github.com/8314fa7ffe772ee9e6d4.js"></script></p>
<p>Whether you need to sort the results according to one of the columns of a model, or to get a list of ActiveRecord objects indexed by primary key, the ability to reindex an existing collection will save you a lot of coding.</p>
<h3>Query From An Array Of Conditions</h3>
<p>In a previous example, a list of books was created based on a &lsquo;title&rsquo; request parameter. But you may want to provide a set of widgets to filter a list of books on several fields. In this case, the request may contain several parameters, each corresponding to a given column.</p>
<p><script src="https://gist.github.com/48a27abc52da4a8392c2.js"></script></p>
<p>It&rsquo;s easy to create a query using an associative array of filters:</p>
<p><script src="https://gist.github.com/ba90427032d20fa61bbd.js"></script></p>
<p><code>filterByArray()</code> expects an associative array of filter names and values, and turns it into a list of <code>filterByXXX()</code> calls. The previous line is the equivalent to:</p>
<p><script src="https://gist.github.com/e1e364130bf3e4708102.js"></script></p>
<p><code>filterByArray()</code> can even accept additional filters not based on model columns, provided that you added a custom <code>filterByXXX()</code> method. For instance, if you add the following field to the book search form:</p>
<p><script src="https://gist.github.com/00b9126d2e20edaac17f.js"></script></p>
<p>Then all it takes to make it work is to implement a <code>BookQuery::filterByAuthor()</code> method:</p>
<p><script src="https://gist.github.com/7bea12532237a9ca3a2e.js"></script></p>
<h3>Raw Results From A Query</h3>
<p>Even though Propel is fully Object-Oriented, there are times when hydrating an ActiveRecord object is just overkill. In cases you need a single scalar value resulting from a database query, there is often no interest to attach it to a model object. In this case, you can still use the methods of the generated query objects, by using the &lsquo;statement&rsquo; formatter to get a raw PDO statement as a result instead of a model object.</p>
<p><script src="https://gist.github.com/3e2b5b535e6b717a53f6.js"></script></p>
<p>If you need several columns and several rows, but still not attached to an ActiveRecord object, you can do the same, and call <code>fetchAll()</code> on the result statement to get an array to iterate on:</p>
<p><script src="https://gist.github.com/2cc2052dd880553f8e55.js"></script></p>
<p>But if you end up doing too much of this kind of query, watch out: you&rsquo;re probably missing the point of actually using an ORM. The following code sample provides the same functionality as the previous one; it is not more expensive to run, yet it is much more object-oriented, and much easier to write:</p>
<p><script src="https://gist.github.com/a4d91a2be4bede1e8c00.js"></script></p>
<p><span style="font-size: 15px; font-weight: bold;">Conclusion</span></p>
<p>If you come from a PDO background, it probably feels more natural to use arrays instead of objects. Propel makes it easy to use an array as an input for an ActiveRecord object or a Query, or to output results as arrays. But try to reduce the use of arrays to a strict minimum, or you will spend too much time transforming these arrays, instead of dealing with the business logic of your web application. And you may miss some of the best features of Propel, which are only enabled by an Object-Oriented approach.</p>
