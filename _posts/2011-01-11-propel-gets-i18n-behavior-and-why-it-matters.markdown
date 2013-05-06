---
layout: post
title: Propel Gets I18n Behavior, And Why It Matters
published: true
---
<p>Propel recently got yet another behavior: the Internationalization behavior, also named <strong>i18n behavior</strong> (the numeronym is a <a href="http://en.wikipedia.org/wiki/Internationalization_and_localization">frequent abbreviation</a>). It allows Propel model objects to get translations, and is useful in multilingual applications.</p>
<p>Not only is it intuitive and dead easy to setup, it also replaces the <a href="http://www.symfony-project.org/jobeet/1_4/Propel/en/19#chapter_19_sub_propel_objects">existing symfony i18n behavior</a> without any change to the application code. And why the Symfony i18n behavior implementation has an important flaw, the new native i18n behavior does things the proper way.<!--more--></p>
<h3>Usage</h3>
<p>Consider as an e-commerce website selling home appliances across the world. This website should keep the name and description of each item separated from the other details, and keep one version for each supported language.</p>
<p>Starting with Propel 1.6, this is possible by adding a simple <code>&lt;behavior&gt;</code> tag to the table that needs internationalization:</p>
<div class="CodeRay">
  <div class="code"><pre>&lt;table name=&quot;item&quot;&gt;
  &lt;column name=&quot;id&quot; required=&quot;true&quot; primaryKey=&quot;true&quot; autoIncrement=&quot;true&quot; type=&quot;INTEGER&quot; /&gt;
  &lt;column name=&quot;name&quot; type=&quot;VARCHAR&quot; required=&quot;true&quot; /&gt;
  &lt;column name=&quot;description&quot; type=&quot;LONGVARCHAR&quot; /&gt;
  &lt;column name=&quot;price&quot; type=&quot;FLOAT&quot; /&gt;
  &lt;column name=&quot;is_in_store&quot; type=&quot;BOOLEAN&quot; /&gt;
  &lt;behavior name=&quot;i18n&quot;&gt;
    &lt;parameter name=&quot;i18n_columns&quot; value=&quot;name, description&quot; /&gt;
  &lt;/behavior&gt;
&lt;/table&gt;</pre></div>
</div>

<p>In this example, the <code>name</code> and <code>description</code> columns are moved to a new table, called <code>item_i18n</code>, which shares a many-to-one relationship with Item - one Item has many Item translations. But all this happens in the background; for the end user, everything happens as if there were only one main <code>Item</code> object:</p>
<div class="CodeRay">
  <div class="code"><pre>$item = new Item();
$item-&gt;setPrice('12.99');
$item-&gt;setName('Microwave oven');
$item-&gt;save();</pre></div>
</div>

<p>This creates one record in the <code>item</code> table with the price, and another in the <code>item_i18n</code> table with the English (default language) translation for the name. Of course, you can add more translations:</p>
<div class="CodeRay">
  <div class="code"><pre>$item-&gt;setLocale('fr_FR');
$item-&gt;setName('Four micro-ondes');
$item-&gt;setLocale('es_ES');
$item-&gt;setName('Microondas');
$item-&gt;save();</pre></div>
</div>

<p>This works both for setting AND for getting internationalized columns:</p>
<div class="CodeRay">
  <div class="code"><pre>$item-&gt;setLocale('en_EN');
echo $item-&gt;getName(); //'Microwave oven'
$item-&gt;setLocale('fr_FR');
echo $item-&gt;getName(); // 'Four micro-ondes'</pre></div>
</div>

<p><strong>Tip</strong>: The big advantage of Propel behaviors is that they use code generation. Even though it&rsquo;s only a proxy method to the <code>ItemI18n</code> class, <code>Item::getName()</code> has all the phpDoc required to make your IDE happy.</p>
<h3>Combined Hydration</h3>
<p>This new behavior also adds special capabiliies to the Query objects. The most interesting allows you to execute less queries when you need to query for an Item and one of its translations - which is common to display a list of items in the locale of the user:</p>
<div class="CodeRay">
  <div class="code"><pre>$items = ItemQuery::create()-&gt;find(); // one query to retrieve all items
$locale = 'en_EN';
foreach ($items as $item) {
  echo $item-&gt;getPrice();
  $item-&gt;setLocale($locale);
  echo $item-&gt;getName(); // one query to retrieve the English translation
}</pre></div>
</div>

<p>This code snippet requires 1+n queries, n being the number of items. But just add one more method call to the query, and the SQL query count drops to 1:</p>
<div class="CodeRay">
  <div class="code"><pre>$items = ItemQuery::create()
  -&gt;joinWithI18n('en_EN')
  -&gt;find(); // one query to retrieve both all items and their translations
foreach ($items as $item) {
  echo $item-&gt;getPrice();
  echo $item-&gt;getName(); // no additional query
}</pre></div>
</div>

<p>In addition to hydrating translations, <code>joinWithI18n()</code> sets the correct locale on results, so you don&rsquo;t need to call <code>setLocale()</code> for each result.</p>
<h3>Symfony Compatibility</h3>
<p>This behavior is entirely compatible with the i18n behavior for symfony. That means that it can generate <code>setCulture()</code> and <code>getCulture()</code> methods as aliases to <code>setLocale()</code> and <code>getLocale()</code>, provided that you add a <code>locale_alias</code> parameter. That also means that if you add the behavior to a table without translated columns, and that the translation table is present in the schema, the behavior recognizes them.</p>
<p>So the following schema is exactly equivalent to the first one in this article:</p>
<div class="CodeRay">
  <div class="code"><pre>&lt;table name=&quot;item&quot;&gt;
  &lt;column name=&quot;id&quot; required=&quot;true&quot; primaryKey=&quot;true&quot; autoIncrement=&quot;true&quot; type=&quot;INTEGER&quot; /&gt;
  &lt;column name=&quot;price&quot; type=&quot;FLOAT&quot; /&gt;
  &lt;column name=&quot;is_in_store&quot; type=&quot;BOOLEAN&quot; /&gt;
  &lt;behavior name=&quot;i18n&quot;&gt;
    &lt;parameter name=&quot;locale_alias&quot; value=&quot;culture&quot; /&gt;
  &lt;/behavior&gt;
&lt;/table&gt;
&lt;table name=&quot;item_i18n&quot;&gt;
  &lt;column name=&quot;id&quot; type=&quot;INTEGER&quot; required=&quot;true&quot; primaryKey=&quot;true&quot; /&gt;
  &lt;column name=&quot;name&quot; type=&quot;VARCHAR&quot; required=&quot;true&quot; /&gt;
  &lt;column name=&quot;description&quot; type=&quot;LONGVARCHAR&quot; /&gt;
&lt;/table&gt;</pre></div>
</div>

<p>Such a schema is almost similar to a schema built for symfony; that means that the Propel i18n behavior is a drop-in replacement for symfony&rsquo;s i18n behavior, keeping BC but improving performance and usability.</p>
<h3>Why It Matters</h3>
<p>The SQL generated by the previous query looks like the following (in MySQL):</p>
<div class="CodeRay">
  <div class="code"><pre>SELECT item.*, item_i18n.*
FROM item LEFT JOIN item_i18n ON (item.id = item_i18n.id AND item_i18n.locale = 'en_EN');</pre></div>
</div>

<p>It does NOT generate the following query:</p>
<div class="CodeRay">
  <div class="code"><pre>SELECT item.*, item_i18n.*
FROM item LEFT JOIN item_i18n ON (item.id = item_i18n.id)
WHERE item_i18n.locale = 'en_EN';</pre></div>
</div>

<p>Can you see the difference? In the last SQL query, the LEFT JOIN actually behaves like an INNER JOIN because of the WHERE clause. That means that <code>item</code> records with no <code>item_i18n</code> translation won&rsquo;t appear in the result. In the first query, even items with no translations are returned.</p>
<p>This difference is important for two reasons:</p>
<ul>
<li>Propel couldn&rsquo;t create joins with two conditions properly in version 1.5 and below. Only Propel 1.6 allows it (see <a href="http://www.propelorm.org/wiki/Documentation/1.6/WhatsNew#JoinWithSeveralConditions">What&rsquo;s New In Propel 1.6?</a>). </li>
<li>The previous i18n behavior implementation for symfony did it the wrong way, and applied the locale condition using WHERE instead of ON. That made results incomplete.</li>
</ul>
<p><strong>Tip</strong>: If you need to return only objects having translations, add <code>Criteria::INNER_JOIN</code> as second parameter to <code>joinWithI18n()</code>.</p>
<h3>Get It</h3>
<p>Just like the recently added <code>versionable</code> behavior, the <code>i18n</code> behavior is thoroughly unit-tested and <a href="http://www.propelorm.org/wiki/Documentation/1.6/Behaviors/i18n">fully documented</a>. It is ready to use in the Propel 1.6 branch, and your multilingual applications will love it.</p>
