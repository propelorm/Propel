---
layout: post
title: Propel 1.6 Gets Versionable Behavior - With A Twist
published: true
---
<p><a href="http://www.propelorm.org/wiki/Documentation/1.6/WhatsNew">Propel 1.6</a> ships with a great new behavior. Once enabled on a table, the <code>versionable</code> behavior stores a copy of the ActiveRecord object in a separate table each time it is saved. This allows to keep track of the changes made on an object, whether to review modifications, or revert to a previous state.</p>
<p>The classic Wiki example is a good illustration:</p>
<div class="CodeRay">
  <div class="code"><pre>&lt;table name=&quot;wiki_page&quot;&gt;
  &lt;column name=&quot;id&quot; required=&quot;true&quot; primaryKey=&quot;true&quot; autoIncrement=&quot;true&quot; type=&quot;INTEGER&quot; /&gt;
  &lt;column name=&quot;title&quot; type=&quot;VARCHAR&quot; required=&quot;true&quot; /&gt;
  &lt;column name=&quot;body&quot; type=&quot;LONGVARCHAR&quot; /&gt;
  &lt;behavior name=&quot;versionable&quot; /&gt;
&lt;/table&gt;</pre></div>
</div>

<p>After rebuild, the <code>WikiPage</code> model has versioning abilities:<!--more--></p>
<div class="CodeRay">
  <div class="code"><pre>$page = new WikiPage();

// automatic version increment
$page-&gt;setTitle('Propel');
$page-&gt;setBody('Propel is a CRM built in PHP');
$page-&gt;save(); 
echo $page-&gt;getVersion(); // 1
$page-&gt;setBody('Propel is an ORM built in PHP5');
$page-&gt;save();
echo $page-&gt;getVersion(); // 2

// reverting to a previous version
$page-&gt;toVersion(1);
echo $page-&gt;getBody(); // 'Propel is a CRM built in PHP'
// saving a previous version creates a new one
$page-&gt;save();
echo $page-&gt;getVersion(); // 3

// checking differences between versions
print_r($page-&gt;compareVersions(1, 2));
// array(
//   'Body' =&gt; array(
//      1 =&gt; 'Propel is a CRM built in PHP',
//      2 =&gt; 'Propel is an ORM built in PHP5'
//    ),
// );

// deleting an object also deletes all its versions
$page-&gt;delete();</pre></div>
</div>

<p>The <code>versionable</code> behavior offers audit log functionality, so you can track who made a modification, when, and why:</p>
<div class="CodeRay">
  <div class="code"><pre>$page = new WikiPage();
$page-&gt;setTitle('PEAR');
$page-&gt;setBody('PEAR is a framework and distribution system for reusable PHP components');
$page-&gt;setVersionCreatedBy('John Doe');
$page-&gt;setVersionComment('First draft');
$page-&gt;save();
// do more modifications...

// list all modifications
foreach ($page-&gt;getAllVersions() as $pageVersion) {
  echo sprintf(&quot;'%s', Version %d, updated by %s on %s (%s)\n&quot;,
    $pageVersion-&gt;getTitle(),
    $pageVersion-&gt;getVersion(),
    $pageVersion-&gt;getVersionCreatedBy(),
    $pageVersion-&gt;getVersionCreatedAt(),
    $pageVersion-&gt;getVersionComment(),
  );
}
// 'PEAR', Version 1, updated by John Doe on 2010-12-21 22:53:02 (First draft)
// 'PEAR', Version 2, updated by ...</pre></div>
</div>

<p>If it was just for that, the <code>versionable</code> behavior would already be awesome. Versioning is a very common feature, and there is no doubt that this behavior will replace lots of boilerplate code. Consider the fact that it&rsquo;s very configurable, <a href="http://www.propelorm.org/wiki/Documentation/1.6/Behaviors/versionable">fully documented</a>, and unit tested, and there is no reason to develop your own versioning layer.</p>
<p>But there is more.</p>
<p>The <code>versionable</code> behavior also works on <strong>relationships</strong>.</p>
<p>If the <code>WikiPage</code> has one <code>Category</code>, and if the <code>Category</code> model also uses the <code>versionable</code> behavior, then each time a <code>WikiPage</code> is saved, it saves the version of the related <code>Category</code> it is related to, and it is able to restore it:</p>
<div class="CodeRay">
  <div class="code"><pre>$category = new Category();
$category-&gt;setName('Libraries');
$page = new WikiPage();
$page-&gt;setTitle('PEAR');
$page-&gt;setBody('PEAR is a framework and distribution system for reusable PHP components');
$page-&gt;setCategory($category);
$page-&gt;save(); // version 1

$page-&gt;setTitle('PEAR - PHP Extension and Application Repository');
$page-&gt;save(); // version 2

$category-&gt;setName('PHP Libraries');
$page-&gt;save(); // version 3

$page-&gt;toVersion(1);
echo $page-&gt;getTitle(); // 'PEAR'
echo $page-&gt;getCategory()-&gt;getName(); // 'Libraries'
$page-&gt;toVersion(3);
echo $page-&gt;getTitle(); // 'PEAR - PHP Extension and Application Repository'
echo $page-&gt;getCategory()-&gt;getName(); // 'PHP Libraries'</pre></div>
</div>

<p>Now the versioning is not limited to a single class anymore. You can even design a fully versionable "application" - it all depends on your imagination.</p>
<p>This feature is unique to Propel, and that&rsquo;s our very Christmas gift to you.</p>
