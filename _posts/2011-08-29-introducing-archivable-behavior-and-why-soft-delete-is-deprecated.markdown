---
layout: post
title: Introducing archivable behavior, and why soft_delete is deprecated
published: true
---
<p>Propel 1.6 introduces a new behavior called <code>archivable</code>. It gives model objects the ability to be copied to an archive table. By default, the behavior archives objects on deletion, acting as a replacement of the <code>soft_delete</code> behavior. So why is it exciting?</p>
<h3>The idea behind the <code>soft_delete</code> behavior</h3>
<p>The <a href="http://propelorm.github.com/propel-docs/behaviors/soft-delete"><code>soft_delete</code> behavior</a> promises to "override the deletion methods of a model object to make them 'hide' the deleted rows but keep them in the database." In order to achieve this soft deletion, the behavior adds a <code>deleted_at</code> column to the table it is applied on, and sets this column to the current date whenever an object is deleted:<!--more--></p>
<div class="CodeRay">
  <div class="code"><pre>$book-&gt;delete();
// translates into MySQL as
// UPDATE book SET book.deleted_at = CURTIME() WHERE book.id = 123;</pre></div>
</div>

<p>Also, it modifies the read queries to ignore all objects having a not null <code>deleted_at</code> column value, so only not deleted objects show up.</p>
<div class="CodeRay">
  <div class="code"><pre>$books = BookQuery::create()-&gt;find();
// SELECT * FROM book WHERE book.deleted_at IS NULL;</pre></div>
</div>

<p>It makes it easy to recover deleted objects (by setting the <code>deleted_at</code> date to NULL again), and to select the soft deleted objects for further treatment.</p>
<h3><code>soft_delete</code> behavior shortcomings</h3>
<p>It looks smart at first sight, but in reality the <code>soft_delete</code> behavior is flawed. If you have been using it a lot, you may have already spotted some of it shortcomings:</p>
<ul>
<li><strong>Performance</strong>: Deleted objects are still present in the table. The table size increases even when you delete objects to clean it up. So read queries become slower when the number of records in the table grows. Adding new indexes to the table to optimize queries can be counter-productive if you forget to put the <code>deleted_at</code> column in the indexes, because every query includes a snippet looking like <code>WHERE deleted_at IS NULL</code>.</li>
<li><strong>Doesn't work all the time</strong>: Read queries sometimes return objects that have been soft deleted. This usually happens when using <code>paginate()</code>, or <code>joinWith()</code>. It all comes down to the algorithm used to "hide" deleted objects: add a <code>WHERE deleted_at IS NULL</code> condition to the next query. It doesn't work if there is more than one query (which is the case for <code>paginate()</code>) or for joined tables (which is the case for <code>joinWith()</code>).</li>
<li><strong>Not compatible with unique constraints</strong>: Whether you have a non-autoincremental primary key, or a unique index, the addition of a <code>delete_at</code> column breaks you model. </li>
<li><strong>Not consistent across database vendors</strong>: If two tables have the <code>soft_delete</code> behavior, and share a foreign key with <code>ON DELETE CACADE</code>, you may expect that soft deleting in the main table also soft deletes related records in the second table. It works on SQLite, or if you set <code>propel.emulateForeignKeyConstraints</code> to true in the <code>build.properties</code>. But it doesn't work by default on MySQL for instance.</li>
</ul>
<p>There are more shortcomings in some very specific use cases. For instance, <a href="https://github.com/propelorm/Propel/issues/48">should the <code>postDelete()</code> event be fired after a soft deletion</a>? One could say yes, one could say no, and both would be right in their particular use case.</p>
<p>All these shortcomings are not specific to the Propel implementation. In fact, <a href="http://www.doctrine-project.org/documentation/manual/1_1/hu/behaviors:core-behaviors:softdelete">the <code>soft_delete</code> behavior for Doctrine 1</a>, <a href="http://www.symfony-project.org/plugins/sfPropelParanoidBehaviorPlugin">the <code>paranoid</code> behavior for Propel in symfony 1</a>, <a href="https://github.com/doctrine/mongodb-odm-softdelete">various</a> <a href="http://codeutopia.net/blog/2010/12/04/how-to-create-doctrine-1-style-soft-delete-in-doctrine-2/">attempts</a> at doing the same for Doctrine2, and the source of them all, <a href="https://github.com/technoweenie/acts_as_paranoid">the <code>acts_as_paranoid</code> behavior for Rails ActiveRecord</a> all have the same flaws. The shortcomings come from the principle of the added <code>deleted_at</code> column, and can't be fixed by implementation.</p>
<p>I'll write that again: the <code>soft_delete</code> behavior can't be fixed. It's a leaky abstraction. To achieve the same functionality, the paradigm must change.</p>
<h3>Introducing the <code>archivable</code> behavior</h3>
<p>If you want to be able to recover deleted objects, a better idea would be to put these into another repository. In database terms, this translates to "copy records to another table". This is the idea behind <code>archivable</code>. It provides a new ActiveRecord method, <code>archive()</code>, to persist a copy of the current object into an archive table.</p>
<div class="CodeRay">
  <div class="code"><pre>$book = new Book();
$book-&gt;setTitle('War and Peace');
$book-&gt;save();
// INSERT INTO book (title) VALUES ('War and Peace');
$book-&gt;archive();
// INSERT INTO book_archive (id, title) VALUES (123, 'War and Peace');</pre></div>
</div>

<p>Here, the <code>archive()</code> method first checks the existence of a <code>book_archive</code> record with the same primary key. If found, it updates it; if not found, it inserts a new one. In both cases, the <code>book_archive</code> record columns copy the values from the <code>book</code> record.</p>
<p>The <code>archive()</code> method is easy to trigger before the deletion; as a matter of fact, the <code>archivable</code> behavior does that on <code>delete()</code>:</p>
<div class="CodeRay">
  <div class="code"><pre>$book-&gt;delete();
// INSERT INTO book_archive (id, title) VALUES (123, 'War and Peace');
// DELETE FROM book WHERE book.id = 123;</pre></div>
</div>

<p>Even if <code>delete()</code> triggers <code>archive()</code>, it is easy to bypass the <code>archivable</code> and do a hard delete:</p>
<div class="CodeRay">
  <div class="code"><pre>// hard delete a book
$book-&gt;deleteWithoutArchive()</pre></div>
</div>

<p>None of the shortcomings described above plague the <code>archivable</code> behavior, because a call to <code>delete()</code> actually deletes records from the main table. So the <code>archivable</code> behavior can be seen as a fixed <code>soft_delete</code> behavior providing the same functionality.</p>
<p>The <code>archivable</code> behavior landed in the Propel 1.6 branch last week, and will be bundled in the next stable version (1.6.2). As for every behavior included in the Propel core, it is heavily (unit) tested, and fully documented (see the <a href="http://propelorm.github.com/propel-docs/behaviors/archivable"><code>archivable</code> behavior documentation</a>).</p>
<h3>More than soft deletion</h3>
<p>The merit of a good idea is that it offers more benefits than just the reason for its creation. <code>archivable</code> covers the requirements of <code>soft_delete</code>, and much more.</p>
<p>First, you can archive an object without deleting it. Whether you need a copy for important actions, or to get a backup for dangerous operations, archiving is often a good idea.</p>
<div class="CodeRay">
  <div class="code"><pre>// archive an existing book
$book-&gt;archive();
// the book still exists in the main table
echo $book-&gt;isDeleted(); // false</pre></div>
</div>

<p>Of course, you probably already have a backup of the whole database somewhere to avoid data loss. But <code>archivable</code> provides a more atomic way to archive data, and to recover it:</p>
<div class="CodeRay">
  <div class="code"><pre>// find the archived version of an existing book
$archivedBook = $book-&gt;getArchive();
// populate a book based on an archive
$book = new Book();
$book-&gt;populateFromArchive($archivedBook);
// restore an object to the state it had when last archived
$book-&gt;restoreFromArchive();</pre></div>
</div>

<p>The Query class also gets an <code>archive()</code> method to archive a set of objects:</p>
<div class="CodeRay">
  <div class="code"><pre>// archive all books by Leo Tolstoi
BookQuery::create()
  -&gt;useAuthorQuery()
    -&gt;filterByName('Leo Tolstoi')
  -&gt;endUse()
  -&gt;archive();</pre></div>
</div>

<p>You can override the <code>archive()</code> method to add custom logic, for instance to also archive related objects:</p>
<div class="CodeRay">
  <div class="code"><pre>class Book extends BaseBook
{
  public function archive(PropelPDO $con = null)
  {
    // archive the book reviews
    BookReviewQuery::create()
      -&gt;filterByBook($this)
      -&gt;archive($con);
    // archive the current object
    return parent::archive($con);
  }
}</pre></div>
</div>

<p>This method is called both when archiving a single object, and when archiving a set of object through the Query's <code>archive()</code> method.</p>
<p>You can even store the archive table in another database than the one containing the main table. The archive database can be in another server, or even in another datacenter, providing additional security to your archived data.</p>
<h3>Why did you delete objects in the first place?</h3>
<p>"Archive" is a better abstraction than "soft delete". But not all the time. Why did you want to keep deleted objects in the first place? You probably used the "delete" verb because your model didn't provide a better verb to suit your requirements. In that case, you should enhance your model to give it the ability you need, and leave <code>delete()</code> alone. In other terms, improve your domain model. Udi Dahan, who often blogs about Service-Oriented Architectures and Domain Models, <a href="http://www.udidahan.com/2009/09/01/dont-delete-just-dont/">puts it this way</a>:</p>
<blockquote class="posterous_medium_quote">
<p>Orders aren&rsquo;t deleted &ndash; they&rsquo;re cancelled. There may also be fees incurred if the order is canceled too late.</p>
<p>Employees aren&rsquo;t deleted &ndash; they&rsquo;re fired (or possibly retired). A compensation package often needs to be handled.</p>
<p>Jobs aren&rsquo;t deleted &ndash; they&rsquo;re filled (or their requisition is revoked).</p>
</blockquote>
<p>So there are many ways to circumvent the <code>soft_delete</code> shortcomings, and probably not good reason to keep using this behavior. For the better, <code>soft_delete</code> is now deprecated in Propel 1.6.</p>
