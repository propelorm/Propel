---
layout: post
title: ! 'Many-to-many Relationships: Check!'
published: true
---
<p>Propel users used to complain about the lack of support for many-to-many relationships in the generated model classes. Writing a custom getter by hand for this kind of relationship wasn't so hard, but since people kept doing so repeatedly, it became clear that Propel had to support it. After all, the purpose of an ORM is to automate repetitive tasks and to make the life of a developer easier.</p>
<p>Starting with Propel 1.5, many-to-many relationships are first class citizens in Propel. In order to declare them, you must set the `isCrossRef` attribute to `true` in the `&lt;table&gt;` element of the cross-reference table (or "junction" table).&nbsp;For instance, if the `user` and `group` tables are related by a many-to-many relationship, this happens through the rows of a `user_group` table:</p>
<p>[code]
&lt;table name="user"&gt;
&nbsp;&nbsp;&lt;column name="id" type="INTEGER" primaryKey="true" autoIncrement="true"/&gt;
&nbsp;&nbsp;&lt;column name="name" type="VARCHAR" size="32"/&gt;
&lt;/table&gt;
&lt;table name="group"&gt;
&nbsp;&nbsp;&lt;column name="id" type="INTEGER" primaryKey="true" autoIncrement="true"/&gt;
&nbsp;&nbsp;&lt;column name="name" type="VARCHAR" size="32"/&gt;
&lt;/table&gt;
&lt;table name="user_group" isCrossRef="true"&gt;
&nbsp;&nbsp;&lt;column name="user_id" type="INTEGER" primaryKey="true"/&gt;
&nbsp;&nbsp;&lt;column name="group_id" type="INTEGER" primaryKey="true"/&gt;
&nbsp;&nbsp;&lt;foreign-key foreignTable="user"&gt;
&nbsp;&nbsp; &nbsp;&lt;reference local="user_id" foreign="id"/&gt;
&nbsp;&nbsp;&lt;/foreign-key&gt;
&nbsp;&nbsp;&lt;foreign-key foreignTable="group"&gt;
&nbsp;&nbsp; &nbsp;&lt;reference local="group_id" foreign="id"/&gt;
&nbsp;&nbsp;&lt;/foreign-key&gt;
&lt;/table&gt;
[/code]</p>
<p>From both sides of the relation, a many-to-many relationship is seen as a one-to-many relationship ; besides, Propel takes care of creating and retrieving instances of the middle class, so you never actually need to deal with them. That means that manipulating many-to-many relationships is nothing new if you already deal with one-to-many relationships:<!--more--></p>
<p>[code]
// create and relate objects as if they shared a one-to-many relationship
$user = new User();
$user-&gt;setName('John Doe');
$group = new Group();
$group-&gt;setName('Anonymous');
// relate $user and $group
$user-&gt;addGroup($group);
// save the $user object, the $group object, and a new instance of the UserGroup class
$user-&gt;save();
// retrieve objects as if they shared a one-to-many relationship
$groups = $user-&gt;getGroups();
// the model query also features a smart filter method for the relation
$groups = GroupQuery::create()
&nbsp;&nbsp;-&gt;filterByUser($user)
&nbsp;&nbsp;-&gt;find();
[/code]</p>
<p>So besides the `isCrossRef` attribute, there is nothing to learn - Propel avoids to introduce new conventions when existing ones fit a new use case.</p>
