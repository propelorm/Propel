---
layout: post
title: Propel 1.6.0 Beta 2 Released
published: true
---
<p>The Propel team has the pleasure to announce the immediate availability of the second and last beta release of the 1.6 branch. This is mostly a bugfix release over the beta 1 released a <a href="http://propel.posterous.com/get-ready-for-propel-16-the-beta-1-is-release">month and a half ago</a>, but it also adds a few features:</p>
<ul>
<li><code>ModelCriteria::_or()</code>, a <a href="http://www.propelorm.org/wiki/Documentation/1.6/WhatsNew#EasierORinQueries">new and better way to add OR to your conditions</a></li>
<li>Virtual Foreign Keys, a.k.a <a href="http://www.propelorm.org/wiki/Documentation/1.6/WhatsNew#Model-OnlyRelationships">Model-Only Relationships</a></li>
<li>Support for <a href="http://www.propelorm.org/wiki/Documentation/1.6/Schema#OracleVendorInfo">Oracle Tablespaces</a> in schemas</li>
<li>A&nbsp;<a href="http://www.propelorm.org/ticket/1286">service class</a>&nbsp;allowing behaviors to replace existing methods in generated classes</li>
<li>A <a href="http://www.propelorm.org/wiki/Documentation/1.6/Writing-Behavior#SpecifyingaPriorityForBehaviorExecution">priority system for behaviors</a>, to solve behavior conflicts</li>
</ul>
<p>As the previous beta, Propel 1.6.0 Beta 2 is backwards compatible with Propel 1.5. Just upgrade Propel, rebuild your model, and you're good to go.</p>
<h3>Subversion tag</h3>
<div class="CodeRay">
  <div class="code"><pre>&gt; svn checkout http://svn.propelorm.org/tags/1.6.0BETA2</pre></div>
</div>

<h3>PEAR</h3>
<div class="CodeRay">
  <div class="code"><pre>&gt; sudo pear config-set preferred_state beta
&gt; sudo pear upgrade propel/propel_generator
&gt; sudo pear upgrade propel/propel_runtime</pre></div>
</div>

<h3>Download</h3>
<ul>
<li><a href="http://files.propelorm.org/propel-1.6.0BETA2.tar.gz">http://files.propelorm.org/propel-1.6.0BETA2.tar.gz</a> (Linux)</li>
<li><a href="http://files.propelorm.org/propel-1.6.0BETA2.zip">http://files.propelorm.org/propel-1.6.0BETA2.zip</a> (Windows)</li>
</ul>
<p>Don&rsquo;t hesitate to send us feedback on the developers mailing-list, or by opening tickets on the Propel Trac. Since this is the last beta, it is also your last chance to contribute to the 1.6 branch if you want to add a feature. We want to release a RC soon, so don't wait.</p>
