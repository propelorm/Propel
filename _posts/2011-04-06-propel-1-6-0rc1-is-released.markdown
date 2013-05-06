---
layout: post
title: Propel 1.6.0RC1 Is Released
published: true
---
<p>Propel 1.6.0 stable is not far away, for today the first Release Candidate was just published. From now on, no new feature will be added to the 1.6.0 version. And if no major bug appears, this Release Candidate will be the next 1.6.0 stable.</p>
<h3>Changelog</h3>
<p>As compared to the last beta, this release contains mostly bugfixes. A few new features found their way at the last moment:</p>
<ul>
<li>Generated setters and filters now share the same capabilities. For instance, you can now set a boolean column to &lsquo;yes&rsquo;, or filter a temporal column on the &lsquo;now&rsquo; string. The phpDoc blocks of generated setters and filters were rewritten to reflect these changes, so check the generated classes for details. </li>
<li>Propel now supports <a href="http://www.propelorm.org/changeset/2247">Table Subqueries</a>, a.k.a. &ldquo;Inline Views&rdquo;, thanks to a patch by lvanderree. </li>
<li>Behaviors can now modify existing methods even if no hook is called in the builders, thanks to a new service class called <code>PropelPHPParser</code>. This class can remove a method, replace a method by another one, or add a new method before or after an existing one. See the <a href="http://www.propelorm.org/wiki/Documentation/1.6/Behaviors#ReplacingorRemovingExistingMethods">Behavior Documentation</a> for details.</li>
<li>The built-in <a href="http://www.propelorm.org/wiki/Documentation/1.6/Runtime-Introspection">Runtime Introspection</a> classes are now a little smarter: <code>TableMap</code> objects have the knowledge of the primary string column.<!--more--></li>
</ul>
<h3>Backwards Compatibility</h3>
<p>As the previous beta, Propel 1.6.0RC1 is backwards compatible with Propel 1.5. Just upgrade Propel, rebuild your model, and you&rsquo;re good to go.</p>
<p>Propel 1.6.0 is the next 1.5 release, since the 1.5.6 was the last release in the 1.5 branch. So if you want to prepare the next upgrade of your Propel 1.5 application, you should test this release.</p>
<h3>Installing</h3>
<p>As usual, Propel is available in all the formats you can dream of:</p>
<p><em>Subversion tag</em></p>
<div class="CodeRay">
  <div class="code"><pre>&gt; svn checkout http://svn.propelorm.org/tags/1.6.0RC1</pre></div>
</div>

<p><em>Git clone</em></p>
<div class="CodeRay">
  <div class="code"><pre>&gt; git clone git://github.com/Xosofox/propel.git</pre></div>
</div>

<p><em>PEAR</em></p>
<div class="CodeRay">
  <div class="code"><pre>&gt; sudo pear config-set preferred_state beta
&gt; sudo pear upgrade propel/propel_generator
&gt; sudo pear upgrade propel/propel_runtime</pre></div>
</div>

<p><em>Download</em></p>
<ul>
<li><a href="http://files.propelorm.org/propel-1.6.0RC1.tar.gz">http://files.propelorm.org/propel-1.6.0RC1.tar.gz</a> (Linux)</li>
<li><a href="http://files.propelorm.org/propel-1.6.0RC1.zip">http://files.propelorm.org/propel-1.6.0RC1.zip</a> (Windows)</li>
</ul>
<h3>Send Us Your Feedback</h3>
<p>Please test and report any problem you may encounter with this release. Don&rsquo;t hesitate to report documentation problems, phpDoc inaccuracies, good or bad surprises you got with that release.</p>
