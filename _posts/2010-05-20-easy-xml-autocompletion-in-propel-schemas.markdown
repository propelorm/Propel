---
layout: post
title: Easy XML autocompletion in Propel schemas
published: true
---
<p>Propel offers a rich XML schema to describe and validate the <code>schema.xml</code> syntax. In IDEs offering autocompletion (like NetBeans or Eclipse), this can be a great time saver, because the IDE can suggest the possible elements and attributes at the right time, and validate the syntax progressively.</p>
<p>To enable XML autocompletion, you must add two attributes to the <code>&lt;database&gt;</code> tag of your schema:</p>
<p><code>&lt;database name="my_connection_name" defaultIdMethod="native"<br /> xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"<br /> xsi:noNamespaceSchemaLocation="http://www.propelorm.org/xsd/1.5/database.xsd" &gt;<br /></code></p>
<p>This works for all versions of Propel down to 1.3.</p>
<p>Propel is definitely the most IDE-friendly ORM out there, and its helps you to write good quality code at lightspeed.</p>
