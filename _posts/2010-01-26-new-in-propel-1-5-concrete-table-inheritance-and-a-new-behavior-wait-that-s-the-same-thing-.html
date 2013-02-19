---
layout: post
title: ! 'New In Propel 1.5: Concrete Table Inheritance And A New Behavior. Wait,
  That''s The Same Thing.'
published: true
---
<p>Inheritance is very common in the Object-Oriented world, much less in the database world. Yet, being able to extend a model allows for clean code organization and more complex logic. Some RDBMS, like PostgreSQL, offer <a href="http://www.postgresql.org/docs/8.1/static/ddl-inherit.html">native inheritance</a>. Starting with Propel 1.5, Propel gives the same ability to every compatible storage (including MySQL, Oracle, SQLite, and MSSQL) through the new <span style="font-family: courier new,monospace;">concrete_inheritance</span> behavior.<p /> <strong>Understanding Inheritance Design Patterns</strong><p />Propel has offered <a href="http://propel.phpdb.org/trac/wiki/Users/Documentation/1.5/Inheritance#SingleTableInheritance">Single Table Inheritance</a> for a long time. This feature allows several model classes to extend one another, but all the records are persisted in a single table. For complex inheritance patterns, this leads to tables with a lot of columns - and a lot of NULL values in the records.<p /> Another common strategy for implementing inheritance in an ORM is called <a href="http://www.martinfowler.com/eaaCatalog/concreteTableInheritance.html">Concrete Table Inheritance</a>. In this case, each child class has its own table for storage, and the inheritance adds the columns of the parent class to each of the child classes. It's this implementation that Propel 1.5 introduces - with a twist.<p /> <strong>Tip</strong>: If you want to know more about table inheritance and other design patterns, I highly recommend the reading of "<a href="http://martinfowler.com/books.html#eaa">Patterns Of Enterprise Application Architecture</a>", by Martin Fowler.<br /> <!--more--><br /><strong>The Concrete Table Inheritance Behavior</strong><p />In the following example, the `article` and `video` tables use this behavior to inherit the columns and foreign keys of their parent table, `content`:<p />[code]
 &lt;table name="content"&gt;
&nbsp; &lt;column name="id" type="INTEGER" primaryKey="true" autoIncrement="true"/&gt;
&nbsp; &lt;column name="title" type="VARCHAR" size="100"/&gt;
 &nbsp; &lt;column name="category_id" required="false" type="INTEGER" /&gt;
&nbsp; &lt;foreign-key foreignTable="category" onDelete="cascade"&gt;
&nbsp;&nbsp;&nbsp; &lt;reference local="category_id" foreign="id" /&gt;
 &nbsp; &lt;/foreign-key&gt;
&lt;/table&gt;
&lt;table name="category"&gt;
&nbsp; &lt;column name="id" required="true" primaryKey="true" autoIncrement="true" type="INTEGER" /&gt;
 &nbsp; &lt;column name="name" type="VARCHAR" size="100" primaryString="true" /&gt;
&lt;/table&gt;
&lt;table name="article"&gt;
&nbsp; &lt;behavior name="concrete_inheritance"&gt;
 &nbsp;&nbsp;&nbsp; &lt;parameter name="extends" value="content" /&gt;
&nbsp; &lt;/behavior&gt;
&nbsp; &lt;column name="body" type="VARCHAR" size="100"/&gt;
&lt;/table&gt;
&lt;table name="video"&gt;
 &nbsp; &lt;behavior name="concrete_inheritance"&gt;
&nbsp;&nbsp;&nbsp; &lt;parameter name="extends" value="content" /&gt;
&nbsp; &lt;/behavior&gt;
&nbsp; &lt;column name="resource_link" type="VARCHAR" size="100"/&gt;
 &lt;/table&gt;
[/code]<p />The behavior copies the columns of the parent table to the child tables. That means that the generated `Article` and `Video` models have a `Title` property and a `Category` relationship:<p /> [code]
// create a new Category
$cat = new Category();
$cat-&gt;setName('Movie');
$cat-&gt;save();
// create a new Article
$art = new Article();
$art-&gt;setTitle('Avatar Makes Best Opening Weekend in the History');
 $art-&gt;setCategory($cat);
$art-&gt;setContent('With $232.2 million worldwide total, Avatar had one of the best-opening weekends in the history of cinema.');
$art-&gt;save();
// create a new Video
$vid = new Video();
 $vid-&gt;setTitle('Avatar Trailer');
$vid-&gt;setCategory($cat);
$vid-&gt;setResourceLink('<a href="http://www.avatarmovie.com/index.html">http://www.avatarmovie.com/index.html</a>')
$vid-&gt;save();
 [/code]<p /><strong>Model Inheritance</strong><p />If Propel stopped there, the `concrete_inheritance` behavior would only provide a shorcut to avoid repeating tags in the schema. But the behaviors uses PHP's inheritance system to make the models of the child tables extends the model of the parent table: the `Article` and `Video` classes actually extend the `Content` class:<p /> [code]
class Content extends BaseContent
{
&nbsp; public function getCategoryName()
&nbsp; {
&nbsp;&nbsp;&nbsp; return $this-&gt;getCategory()-&gt;getName();
&nbsp; }
}
echo $art-&gt;getCategoryName(); // 'Movie'
 echo $vid-&gt;getCategoryName(); // 'Movie'
[/code]<p />Imagine how convenient this class extension is to avoid repeating code across similar classes. And contrary to the Single Table Inheritance pattern, the Concrete Table Inheritance scales without problems to dozens of tables with very different columns.<p /> The fact that the behavior system, introduced in Propel 1.4, provides the best implementation for the Concrete Table Inheritance, shows how powerful behaviors can be. Propel keeps getting new features without adding too much complexity to the core.<p /> <strong>One More Thing</strong><p />Usually, ORMs stop the Concrete Table Inheritance implementation there. But not Propel. The `concrete_inheritance` behavior does not only copy the <em>table structure</em>, it also copies <em>data</em>.<p /> Every time you save an `Article` or a `Video` object, Propel saves a copy of the `title` and `category_id` columns in a `Content` object. Consequently, retrieving objects regardless of their child type becomes very easy:<p /> [code]
$conts = ContentQuery::create()-&gt;find();
foreach ($conts as $content) {
&nbsp; echo $content-&gt;getTitle() . "(". $content-&gt;getCategoryName() ")/n";
}
// Avatar Makes Best Opening Weekend in the History (Movie)
 // Avatar Trailer (Movie)
[/code]<p />The resulting relational model is denormalized - in other terms, data is copied across tables - but the behavior takes care of everything for you. That allows for very effective read queries on complex inheritance structures.<p /> Check out the brand new <a href="http://propel.phpdb.org/trac/wiki/Users/Documentation/1.5/Inheritance#ConcreteTableInheritance">Inheritance Documentation</a> for more details on using and customizing this behavior.</p>
