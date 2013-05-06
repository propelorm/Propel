---
layout: post
title: ! 'Get Ready For Propel 1.6: The Beta 1 Is Released.'
published: true
---
<p>Propel 1.6 has been in development for several months, and yet it has always been ready for real life use. Each added feature is backwards compatible, unit tested, and documented. But until now, only a handfull of developers dared to install it due to its &ldquo;under development&rdquo; status.</p>
<p>Guess what? Propel 1.6 is now in <strong>beta</strong> state. This means that most of the features of the final 1.6 are already there, and that it&rsquo;s almost ready for your production application. It also means that we need more feedback from beta testers to help us find and fix the last bugs before a stable release that should come within a couple months.</p>
<p>And to motivate you to upgrade, here is a brief list of highlights from the 1.6 version:</p>
<ul>
<li><a href="http://www.propelorm.org/wiki/Documentation/1.6/Migrations">Migrations</a></li>
<li><a href="http://www.propelorm.org/wiki/Documentation/1.6/Behaviors/versionable">Versionable Behavior</a></li>
<li><a href="http://www.propelorm.org/wiki/Documentation/1.6/Behaviors/i18n">I18n Behavior</a></li>
<li><a href="http://www.propelorm.org/wiki/Documentation/1.6/WhatsNew#XMLYAMLJSONCSVParsingandDumping">XML/YAML/JSON/CSV Parsing and Dumping</a></li>
<li><a href="http://www.propelorm.org/wiki/Documentation/1.6/Using-SQL-Schemas">Support For SQL Schemas</a></li>
<li><a href="http://www.propelorm.org/wiki/Documentation/1.6/Advanced-Column-Types">Advanced Column Types (ENUM, OBJECT, ARRAY)</a></li>
</ul>
<p>The updated Propel 1.6 documentation offers a more detailed <a href="http://www.propelorm.org/wiki/Documentation/1.6/WhatsNew">list of new features in Propel 1.6</a>.</p>
<p>To upgrade, use your favorite distribution:</p>
<h3>Subversion tag</h3>
<div class="CodeRay">
  <div class="code"><pre>&gt; svn checkout http://svn.propelorm.org/tags/1.6.0BETA1</pre></div>
</div>

<h3>PEAR</h3>
<div class="CodeRay">
  <div class="code"><pre>&gt; sudo pear config-set preferred_state beta
&gt; sudo pear upgrade propel/propel_generator
&gt; sudo pear upgrade propel/propel_runtime</pre></div>
</div>

<h3>Download</h3>
<ul>
<li><a href="http://files.propelorm.org/propel-1.6.0BETA1.tar.gz">http://files.propelorm.org/propel-1.6.0BETA1.tar.gz</a> (Linux)</li>
<li><a href="http://files.propelorm.org/propel-1.6.0BETA1.zip">http://files.propelorm.org/propel-1.6.0BETA1.zip</a> (Windows)</li>
</ul>
<p>As usual, don&rsquo;t forget to rebuild your object model classes using the <code>om</code> task. And don&rsquo;t hesitate to send us feedback on the developers mailing-list, or by opening tickets on the <a href="http://www.propelorm.org/timeline">Propel Trac</a>.</p>
