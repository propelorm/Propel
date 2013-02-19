---
layout: post
title: Introducing Virtual Foreign Keys
published: true
---
<p>Starting with version 1.6, Propel models can now share relationships even though the underlying tables aren't linked by a foreign key. This ability may be of great use when using Propel on top of a legacy database.</p>
<p>For example, a <code>review</code> table designed for a MyISAM database engine is linked to a <code>book</code> table by a simple <code>book_id</code> column:</p>
<div class="CodeRay">
  <div class="code"><pre>&lt;table name=&quot;review&quot;&gt;
  &lt;column name=&quot;review_id&quot; type=&quot;INTEGER&quot; primaryKey=&quot;true&quot; required=&quot;true&quot;/&gt;
  &lt;column name=&quot;reviewer&quot; type=&quot;VARCHAR&quot; size=&quot;50&quot; required=&quot;true&quot;/&gt;
  &lt;column name=&quot;book_id&quot; required=&quot;true&quot; type=&quot;INTEGER&quot;/&gt;
&lt;/table&gt;</pre></div>
</div>

<p>To enable a model-only relationship, add a <code>&lt;foreign-key&gt;</code> tag using the <code>skipSql</code> attribute, as follows:</p>
<div class="CodeRay">
  <div class="code"><pre>&lt;table name=&quot;review&quot;&gt;
  &lt;column name=&quot;review_id&quot; type=&quot;INTEGER&quot; primaryKey=&quot;true&quot; required=&quot;true&quot;/&gt;
  &lt;column name=&quot;reviewer&quot; type=&quot;VARCHAR&quot; size=&quot;50&quot; required=&quot;true&quot;/&gt;
  &lt;column name=&quot;book_id&quot; required=&quot;true&quot; type=&quot;INTEGER&quot;/&gt;
  &lt;!-- Model-only relationship --&gt;
  &lt;foreign-key foreignTable=&quot;book&quot; onDelete=&quot;CASCADE&quot; skipSql=&quot;true&quot;&gt;
    &lt;reference local=&quot;book_id&quot; foreign=&quot;id&quot;/&gt;
  &lt;/foreign-key&gt;
&lt;/table&gt;</pre></div>
</div>

<p>Such a foreign key is not translated into SQL when Propel builds the table creation or table migration code. It can be seen as a "virtual foreign key". However, on the PHP side, the <code>Book</code> model actually has a one-to-many relationship with the <code>Review</code> model. The generated ActiveRecord and ActiveQuery classes take advantage of this relationship to offer smart getters and filters.</p>
