---
layout: post
title: ! 'Yet Another Propel Behavior: Sluggable'
published: true
---
<p>The development on the Propel 1.5 branch keeps up at a good pace. Today, a new addition was made to the list of the available Propel behaviors: <strong>sluggable</strong>.&nbsp;</p>
<div>It does exactly what you would expect: automatically compose a unique slug for every object that you save. The slug can be used to provide friendly URLs:</div>
<div><span style="font-family: Verdana, Arial, Bitstream Vera Sans, Helvetica, sans-serif; font-size: 13px;">
</span><div class="CodeRay">
  <div class="code"><pre>$post1 = new Post();
$post1-&gt;setTitle('How Is Life On Earth?');
$post1-&gt;setContent('Lorem Ipsum...');
$post1-&gt;save();
echo $post1-&gt;getSlug(); // '/posts/how-is-life-on-earth' </pre></div>
</div>

<div class="CodeRay">
  <div class="code"><pre>Once your objects have slugs, it is very easy to find the object matching a given slug - for instance, from an URL:</pre></div>
</div>

<div class="CodeRay">
  <div class="code"><pre>$post = PostQuery::create()-&gt;findOneBySlug('/posts/how-is-life-on-earth');</pre></div>
</div>

<div class="CodeRay">
  <div class="code"><pre>As for other behaviors, it it dead simple to initialize. Just add the sluggable behavior tag in your schema, rebuild your model, and you're ready to go:</pre></div>
</div>

<div class="CodeRay">
  <div class="code"><pre>&lt;table name=&quot;post&quot;&gt;
  &lt;column name=&quot;id&quot; required=&quot;true&quot; primaryKey=&quot;true&quot; autoIncrement=&quot;true&quot; type=&quot;INTEGER&quot; /&gt;
  &lt;column name=&quot;title&quot; type=&quot;VARCHAR&quot; required=&quot;true&quot; primaryString=&quot;true&quot; /
  &lt;column name=&quot;content&quot; type=&quot;LONGVARCHAR&quot;/&gt;
  &lt;behavior name=&quot;sluggable&quot;&gt;
    &lt;parameter name=&quot;slug_pattern&quot; value=&quot;/posts/{Title}&quot; /&gt;
  &lt;/behavior&gt;
&lt;/table&gt;</pre></div>
</div>

<div class="CodeRay">
  <div class="code"><pre>Make sure you read the sluggable documentation to see all the available settings to customize this brand new behavior.</pre></div>
</div>

<p />

</div>
