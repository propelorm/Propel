---
layout: post
title: How Fast Is Propel 1.5?
published: true
---
<div>With the addition of very powerful features like formatters, collections and the new query object, you may expect the next version of Propel to be slower than its predecessor. Let&rsquo;s benchmark the two to see the actual differences.</div>
<p />
<h3>Benchmark Scenarios</h3>
<p />
<div>To check the speed difference, I set up a few test scenarios emphasizing various parts of the Propel runtime code:</div>
<div>
<ul>
<li>&nbsp;Scenario 1: Create a new Model object, set its columns, and save it. Tests Model object speed, and INSERT SQL generation.</li>
<li>&nbsp;Scenario 2: Lookup a record by its primary key. Tests basic query and hydration.</li>
<li>&nbsp;Scenario 3: Lookup a record using a complex query. Tests object query speed.</li>
<li>&nbsp;Scenario 4: Lookup 5 records on a simple criterion. Tests hydration speed.</li>
<li>&nbsp;Scenario 5: Lookup a record and hydrate it together with its related record in another table. Tests join hydration speed.</li>
</ul>
</div>
<div>The code for the implementation of these scenarios in Propel 1.4 and Propel 1.5 can be found at <a href="http://code.google.com/p/php-orm-benchmark/">http://code.google.com/p/php-orm-benchmark/</a>. Each test is run several times to average the execution time. The result is the number of milliseconds necessary for each test, so the lower it is, the faster the scenario executes.</div>
<p />
<div>I ran the benchmarks using PHP 5.3 and SQLite in memory as a backend on a MacBookPro. I&rsquo;m aware that it is not a "real" production server, and that its I/O system is probably slower than that of a true web server. However, the results are so distinct that I doubt that an EC2 instance would show a different winner.</div>
<p />
<h3>Propel 1.4 vs Propel 1.5</h3>
<p />
<div>Without further ado, let&rsquo;s jump to the results:</div>
<div>&nbsp;<!--more--></div>
<div class="CodeRay">
  <div class="code"><pre>| Insert | findPk | complex| hydrate|  with  |
                       |--------|--------|--------|--------|--------|
     Propel14TestSuite |    986 |    416 |    123 |    280 |    286 |
Propel15aLa14TestSuite |    966 |    407 |    128 |    277 |    282 |</pre></div>
</div>

<p />
<div>Both test suites use the Criteria and Peer syntax; the first uses Propel 1.4 as a backend, while the second uses Propel 1.5. So with the exact same code, Propel 1.5 is slightly faster than Propel 1.4. This is caused by a few optimizations added to Propel 1.5 in the SQL code generation process. In production, the speed difference is barely noticeable, except when saving a large number of objects. Nonetheless, it's reassuring to know that upgrading an exisiting application to Propel 1.5 will not degrade performance.</div>
<p />
<div>But what's more interesting is how the Propel 1.5 new Query API compares with Criteria and Peer methods. This is what the following chart compares:</div>
<p />
<div class="CodeRay">
  <div class="code"><pre>| Insert | findPk | complex| hydrate|  with  |
                  |--------|--------|--------|--------|--------|
Propel14TestSuite |    986 |    416 |    123 |    280 |    286 |
Propel15TestSuite |    966 |    567 |    164 |    376 |    398 |</pre></div>
</div>

<p />
<div>Except for insertions, Propel 1.5 with the Query API is slower than Propel 1.4 by 30% to 50%. This sounds quite normal, considering the added intelligence in the query object, and the new intermediates in the hydration process (formatter, collection). If you want to take advantage of the powerful new features, you have to accept a certain decrease in performance, probably compensated by the added flexibility.</div>
<p />
<div>Also, the main bottleneck in most web applications is the number of queries executed in order to display a single page. Propel 1.5 provides new tools to reduce the query count, including joined hydration of one-to-many relationships, additional column hydration, and collection relation population (see <a href="http://propel.posterous.com/reduce-your-query-count-with-propel-15">http://propel.posterous.com/reduce-your-query-count-with-propel-15</a> for details). So when used wisely, the new Query API in Propel 1.5 will allow your code to run faster than with Propel 1.4.</div>
<p />
<div>Tip: PHP 5.3 and Moore's law make web servers much faster today than two years ago. On up-to-date hardware, Propel 1.5 feels just like Propel 1.3 used to feel when it was released.</div>
<p />
<h3>Propel 1.5 vs. Doctrine 1.2</h3>
<p />
<div>Since the new Propel 1.5 query syntax makes it slower than Propel 1.4, is it still worth using Propel rather than Doctrine? Let&rsquo;s write a Doctrine 1.2 implementation of the benchmark scenarios and compare the results with Propel.&nbsp;</div>
<p />
<div>The Doctrine code, available at <a href="http://code.google.com/p/php-orm-benchmark/">http://code.google.com/p/php-orm-benchmark/</a>, was reviewed by Roman Borschel, who is one of the Doctrine core developers. I also asked him to provide an implementation in Doctrine 2 (which is not yet in Alpha), and this should come shortly.</div>
<p />
<div>Here are the benchmark results:</div>
<p />
<div class="CodeRay">
  <div class="code"><pre>| Insert | findPk | complex| hydrate|  with  |
                     |--------|--------|--------|--------|--------|
   Propel14TestSuite |    986 |    416 |    123 |    280 |    286 |
   Propel15TestSuite |    966 |    567 |    164 |    376 |    398 |
 Doctrine12TestSuite |   1779 |   2738 |    467 |   1628 |   1914 |</pre></div>
</div>

<p />
<div>Propel 1.5, like Propel 1.4, is still much faster at runtime than Doctrine 1.2 &ndash; between 2x and 5x. Doctrine uses runtime introspection while Propel relies on code generation, so that explains the difference. Doctrine also provides an extensive query language called DQL that requires parsing, and that has a cost. The generated `filterByXXX()` methods in Propel Query classes execute much faster, because they don&rsquo;t require any introspection.</div>
<p />
<div>Note that I&rsquo;m aware that the two ORMs don't provide the same exact features. But both are first class ORMs, and provide powerful tools to reduce the query count, which is the main performance bottleneck in Model code.</div>
<p />
<h3>Enter The Query Cache</h3>
<p />
<div>When I asked him to review the Doctrine code, Roman Borschel suggested that I enable the Query Cache in the Doctrine benchmark. This cache removes much of the performance hog in the query parsing, so that runtime code just deals with hydration.</div>
<p />
<div>Query cache is a smart technique that can speed up Propel as well. In fact, Propel 1.5 provides the same feature, as a behavior. So I ran the benchmarks once more, this time comparing Propel 1.5 with query cache, and Doctrine 1.2 with query cache:</div>
<p />
<div class="CodeRay">
  <div class="code"><pre>| Insert | findPk | complex| hydrate|  with  |
                             |--------|--------|--------|--------|--------|
           Propel14TestSuite |    986 |    416 |    123 |    280 |    286 |
           Propel15TestSuite |    966 |    567 |    164 |    376 |    398 |
  Propel15WithCacheTestSuite |    965 |    459 |    182 |    372 |    337 |
         Doctrine12TestSuite |   1779 |   2738 |    467 |   1628 |   1914 |
Doctrine12WithCacheTestSuite |   2059 |   1205 |    553 |    984 |    763 |</pre></div>
</div>

<p />
<div>The query cache is very effective in improving Doctrine&rsquo;s runtime performance &ndash; at least in these tests. The setup used for the tests uses a static array as the backend for the query cache. In a real application, this cache would use an APC or Memcache backend, much slower than a PHP array.</div>
<p />
<div>The query cache also provides a noticeable boost to Propel 1.5. It&rsquo;s not enough to put it on par with Propel 1.4, though.</div>
<p />
<div>And most important: the query cache sometimes degrades performance. In the &ldquo;complex&rdquo; scenario, both Propel and Doctrine perform worse using a query cache than without. Also, the query cache makes insertions even slower with Doctrine, while it was already a weak point.</div>
<p />
<h3>Propel vs. PDO</h3>
<p />
<div>If losing performance is a major drawback for you, perhaps you should bypass the ORM entirely. Not for all queries, of course, but for the really critical ones. ORMs are notably slow, because they add object-oriented code on top of database operations. PHP is fast for string operations, but it's not the fastest language for objects.</div>
<p />
<div>So if you have to execute a lot of database queries in a small amount of time, don't blame Propel 1.5, but rather use PDO directly. Propel makes it easy to execute a raw SQL query using the database connections defined in your settings. Check the following benchmark comparing ORMs to raw PDO:</div>
<p />
<div class="CodeRay">
  <div class="code"><pre>| Insert | findPk | complex| hydrate|  with  |
                             |--------|--------|--------|--------|--------|
                PDOTestSuite |    102 |    106 |    103 |    106 |    100 |
           Propel14TestSuite |    986 |    416 |    123 |    280 |    286 |
           Propel15TestSuite |    966 |    567 |    164 |    376 |    398 |
  Propel15WithCacheTestSuite |    965 |    459 |    182 |    372 |    337 |
         Doctrine12TestSuite |   1779 |   2738 |    467 |   1628 |   1914 |
Doctrine12WithCacheTestSuite |   2059 |   1205 |    553 |    984 |    763 |</pre></div>
</div>

<p />
<div>As you can see, the number of iterations of each scenario was adjusted so that PDO scores about 100 each time. This new benchmark demonstrates that, for very fast SQL queries like insertion or search by PK, the cost of an ORM is very important (up to 27x slower than raw PDO for Doctrine). On the contrary, for slow and complex SQL queries, the speed drop caused by an ORM isn&rsquo;t that big, because the bottleneck is then the SQL code, not the ORM.</div>
<p />
<h3>Conclusion</h3>
<p />
<div>Propel 1.5 is faster than Propel 1.4 if you use the exact same code. It&rsquo;s a little slower if you use the new features, like collections, generated query classes, and formatters. It's more efficient if you use the new tools to reduce your query count. And it&rsquo;s still much faster than Doctrine 1.2.</div>
<p />
<div>There is room for improvement &ndash; the Propel development team spends a considerable amount of time profiling the Propel code and improving it. There are also techniques to make your Model code perform faster ; you can use query cache, for instance.</div>
<p />
<div>But the bottomline is that an ORM provides coding tools that will speed up your development and help you write more efficient queries in no time. If you really need brute speed, you should bypass the ORM completely and use PDO in selected queries. So the performance of an ORM should not be your main element of choice.</div>
<p />
<div>The code used for this benchmark is available at <a href="http://code.google.com/p/php-orm-benchmark/">http://code.google.com/p/php-orm-benchmark/</a>.&nbsp;</div>
