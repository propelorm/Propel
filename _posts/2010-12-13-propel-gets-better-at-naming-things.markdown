---
layout: post
title: Propel Gets Better at Naming Things
published: true
---
<p>Have you ever considered Propel as a lame English speaker? Due to its poor pluralizer, Propel used to be unable to create proper getter methods for one-to-many relationships when dealing with foreign objects named &lsquo;Child&rsquo;, &lsquo;Category&rsquo;, &lsquo;Wife&rsquo;, or &lsquo;Sheep&rsquo;.</p>
<p>Starting with Propel 1.6, Propel adds a new pluralizer class named <code>StandardEnglishPluralizer</code>, which should take care of most of the irregular plural forms of your domain class names. This new pluralizer is disabled by default for backwards compatibility reasons, but it&rsquo;s very easy to turn it on in your <code>build.properties</code> file:</p>
<div class="CodeRay">
  <div class="code"><pre>propel.builder.pluralizer.class = builder.util.StandardEnglishPluralizer</pre></div>
</div>

<p>Rebuild your model, and voila: ActiveRecord objects can now retrieve foreign objects using <code>getChildren()</code>, <code>getCategories()</code>, <code>getWives()</code>, and&hellip; <code>getSheep()</code>.</p>
<p>Thanks to Paul Hanssen for the patch.&nbsp;</p>
