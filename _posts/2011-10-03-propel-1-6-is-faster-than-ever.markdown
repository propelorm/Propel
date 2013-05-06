---
layout: post
title: Propel 1.6 Is Faster Than Ever
published: true
---
<p>The upcoming Propel release, dubbed Propel 1.6.3, is faster than ever. Special optimizations were introduced in the Propel generator to make your entities blazingly fast when performing persistence actions.</p>
<h3>What Is Fast?</h3>
<p>ORMs have a bad reputation concerning performance. But complex models are often slow because the underlying database queries are slow. Before blaming the ORM, you must measure the share of the database in the processing time of a script. A slow SQL query can't be faster using an ORM.</p>
<p>That's why we, the Propel developers, measure the Propel performance relative to reference queries. Propel is not slow or fast per se. A Propel query is n times slower than the same query without an ORM, and we work hard to keep the n factor low.<!--more--></p>
<p>In fact, our target is to keep the Propel ORM under a factor 4. That means that using Propel should not be more than four times slower than bare PDO.</p>
<p>In addition, Propel provides tools to minimize database queries, as explained in <a href="http://propel.posterous.com/reduce-your-query-count-with-propel-15">a previous post</a>. Instance pooling, joined hydration, lazy loading, lazy hydration, all those features avoid a return trip to the database in many cases - and they can make Propel faster than raw PDO.</p>
<h3>What Is Slow?</h3>
<p>Four times slower than PDO can seem like a lot slower. It is not. Think about all the things that Propel has to do when you call <code>save()</code> on an entity:</p>
<ul>
<li>Check if there are related objects to save (e.g. saving an <code>Author</code> also saves all related <code>Book</code>s)</li>
<li>Determine if the object is new, to choose between an INSERT or an UPDATE statement (we'll consider an INSERT for the rest of this example)</li>
<li>List the properties of the entity that were modified</li>
<li>Craft a SQL query string using the names of the columns in the database matching the modified properties in the object</li>
<li>Prepare the values for the query (rewind resources, format dates, etc.)</li>
<li>Find the connection to use (and if there is a master and a slave, find the master connection)</li>
<li>Start a database transaction</li>
<li>If the table uses a sequence, get the value to use as primary key</li>
<li>Bind the values of each modified properties to the query string using the PDO type corresponding to the column (e.g. <code>PDO::PARAM_BOOL</code> for a boolean column, <code>PDO::PARAM_NULL</code> for a null value, etc.)</li>
<li>Execute the insertion query against the database</li>
<li>If the table doesn't use sequences, retrieve the inserted id</li>
<li>Save the related objects in the same transaction</li>
<li>Commit the transaction</li>
<li>Mark the entity as not new and not modified anymore</li>
<li>Add the entity to the instance pool</li>
<li>Return the number of modified objects (including all the related objects)</li>
</ul>
<p>That's a lot of object manipulations, and the actual <code>INSERT</code> SQL query is only one step in this long recipe.</p>
<h3>What Is Faster?</h3>
<p>In previous Propel versions, a lot of the operations described above were executed at runtime. Crafting the SQL query string, choosing the PDO type for a modified column, getting the primary key value either before the main query (for tables using sequences) or after (for tables using autoincrement), etc. There were really many things to do each time an object was saved.</p>
<p>In the next Propel version, all the computation necessary for these operations is now done at build time. The generated <code>save()</code> method actually does exactly what is described above, in the fastest PHP code possible. If you wrote a generic PHP script to do all the steps just described, it would probably be slower than Propel. Because the Propel generated classes aren't generic: they are specific to your model, and take away all the heavy duty stuff to introspect your schema, and map the object oriented world with the relational world.</p>
<p>Upgrade to the Propel master, rebuild your model, and take a look at the generated code for the <code>save()</code> method. You will understand all that it does, even without knowing how Propel works. It's because the <code>save()</code> method contains nothing Propel-specific. It's pure PHP and PDO. It's pure speed.</p>
<h3>How Much Faster Is It?</h3>
<p>In Propel 1.6.2, the "n" factor of the insert operation was around 10. It used to take ten times longer to persist an entity using Propel than executing the insert statement with PDO alone. The "n" factor is now well under nine. It's also well under the target four.</p>
<p>We use a test project called <a href="http://code.google.com/p/php-orm-benchmark/">php-orm-benchmark</a>, released under the MIT license, to compare the speeds of some basic scenarios. In addition to a simple insertion, we measure the n factor for a database retrieval using a primary key, the execution of a complex query, the raw hydration of an object based on a resultset, and a joined hydration. Here are the results:</p>
<table>
 
<tr>
<th>&nbsp;</th> <th>insert&nbsp;</th> <th>find pk&nbsp;</th> <th>complex&nbsp;</th> <th>hydrate&nbsp;</th> <th>with&nbsp;</th>
</tr>
 

<tr>
<td>PDO</td>
<td>111</td>
<td>109</td>
<td>95</td>
<td>106</td>
<td>99</td>
</tr>
<tr>
<td>Propel 1.4 &nbsp;</td>
<td>1260</td>
<td>502</td>
<td>123</td>
<td>311</td>
<td>303</td>
</tr>
<tr>
<td>Propel 1.5</td>
<td>1050</td>
<td>522</td>
<td>165</td>
<td>414</td>
<td>602</td>
</tr>
<tr>
<td>Propel 1.6</td>
<td>363</td>
<td>198</td>
<td>176</td>
<td>423</td>
<td>466</td>
</tr>

</table>
<p>The tests repeats the basic scenarios enough times to reach an approximate score of 100 for PDO. That's the reference. So a score of 300 means that, compared to PDO, the library has an overhead of factor 3 for this scenario. According to the load of the server used for testing, results may vary of about 10%. So any difference of less than 10% is not significant.</p>
<p>What this chart shows is that simple operations used to be quite heavy in Propel. An insertion, or a Pk find, are very fast database operations. The Propel overhead for these operations was important - even if that didn't mean Propel was slow. If the raw SQL score (using PDO) was of 10ms for an insertion, the same operation would take 100ms with Propel. That's not slow per se, but it's slower than PDO.</p>
<p>The chart also shows that the Propel overhead becomes much less noticeable when the SQL query is complex. That's because the Propel overhead depends on the number of objects to hydrate, while the SQL query time depends on the complexity of the query (and the pertinence of the indices).</p>
<p>Finally, the chart shows that the latest version of Propel 1.6 reaches the sweet spot of the "4 factor", and even goes beyond.</p>
<h3>Are The Other ORMs Faster?</h3>
<p>You will ask this question, so we'd better give you the answer. Here is how Doctrine compares with Propel on these tests:</p>
<table>
 
<tr>
<th>&nbsp;</th> <th>insert&nbsp;</th> <th>find pk&nbsp;</th> <th>complex&nbsp;</th> <th>hydrate&nbsp;</th> <th>with&nbsp;</th>
</tr>
 

<tr>
<td>PDO</td>
<td>111</td>
<td>109</td>
<td>95</td>
<td>106</td>
<td>99</td>
</tr>
<tr>
<td>Doctrine 1.2</td>
<td>2187</td>
<td>3425</td>
<td>545</td>
<td>2276</td>
<td>2365</td>
</tr>
<tr>
<td>Doctrine 1.2 with cache &nbsp;</td>
<td>2508</td>
<td>1500</td>
<td>665</td>
<td>1481</td>
<td>933</td>
</tr>
<tr>
<td>Doctrine 2</td>
<td>151</td>
<td>709</td>
<td>160</td>
<td>800</td>
<td>488</td>
</tr>

</table>
<p>As already noted in a <a href="http://propel.posterous.com/how-fast-is-propel-15">previous benchmark</a>, Doctrine 1.2 is VERY slow. The overhead varies between 6 and 25, which is a lot. As for Doctrine 2, it performs a lot better. In fact, it even outperforms Propel in the insertion scenario - because Doctrine 2 has a special feature to accelerate mass insertion. So a scenario made to test raw insertion speed, and repeated to make the duration significant, becomes a mass insertion, and therefore takes advantage of the Doctrine optimization.</p>
<p>Overall, Doctrine 2 performs very well, and keeps the ORM overhead under a factor 8 all the time.</p>
<h3>The Benefits of Code Generation</h3>
<p>We could boost the Propel results even further by using a cache engine, just like Doctrine does. But having to use cache brings a lot of worries: you have to think about naming the cached queries, and invalidating the cache when the underlying data changes. Unfortunately, these two are <a href="http://martinfowler.com/bliki/TwoHardThings.html">the hardest things in Computer Science</a>, according to Phil Karton. So if you can avoid using a cache engine, by all means, do it.</p>
<p>Propel's raw speed is enough to remove the need of a cache engine. That's because Propel uses code generation to prepare the base entity and query classes for runtime. The philosophy of code generation was only pushed a little further in Propel 1.6, so you can now use the Propel ORM without any afterthought about performance.</p>
