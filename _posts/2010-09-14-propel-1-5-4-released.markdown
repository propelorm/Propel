---
layout: post
title: Propel 1.5.4 Released
published: true
---
<p>Here comes our monthly bugfix release. KRavEN and ddalmais helped improving the MSSQL and Oracle adapters - PDO is really not doing things properly alone. In addition to a few other bugfixes, an important improvement made it trivial to hydrate Propel objects based on an arbitrary SQL statement:</p>
<div class="CodeRay">
  <div class="code"><pre>// prepare and execute an arbitrary SQL statement
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

<p>You can find the <a href="http://www.propelorm.org/wiki/Documentation/1.5/CHANGELOG">full changelog</a> in the Propel 1.5 documentation.</p>
<p>To upgrade, use your favorite distribution:</p>
<ul>
<li>
<p>Subversion tag</p>
<div class="CodeRay">
  <div class="code"><pre>&gt; svn checkout http://svn.propelorm.org/tags/1.5.4</pre></div>
</div>

</li>
<li>
<p>PEAR package</p>
<div class="CodeRay">
  <div class="code"><pre>&gt; sudo pear upgrade propel/propel-generator
&gt; sudo pear upgrade propel/propel-runtime</pre></div>
</div>

</li>
<li>
<p>Download</p>
<ul>
<li><a href="http://files.propelorm.org/propel-1.5.4.tar.gz">http://files.propelorm.org/propel-1.5.4.tar.gz</a> (Linux)</li>
<li><a href="http://files.propelorm.org/propel-1.5.4.zip">http://files.propelorm.org/propel-1.5.4.zip</a> (Windows)</li>
</ul>
</li>
</ul>
