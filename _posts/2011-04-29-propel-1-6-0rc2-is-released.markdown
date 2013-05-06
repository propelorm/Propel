---
layout: post
title: Propel 1.6.0RC2 is released
published: true
---
<p>The Second Release Candidate for the 1.6.0 version has just been released. Get it while its hot!</p>
<h3>Changelog</h3>
<ul>
<li>Fixed diff task combined with skipSQL (closes #1368)</li>
<li>Fixed issue with i18n behavior and classPrefix (closes #1365)</li>
<li>Fixed i18n behavior with namespaces (closes #1359)</li>
<li>Fixed a problem with setting DISTINCT and using LIMIT at the same time with MSSQL server (patch from KRavEN) (closes #825)</li>
<li>Fixed missing namespace in sortable behavior (closes #1358)</li>
<li>Emphasized the expected syntax of pre and post hooks </li>
<li>Fixed namespace.autoPackage when database namespace adds to the table namespace (closes #1357)</li>
<li>Fixed incompatibility between Propel and symfony over sfYaml (closes #1356)</li>
<li>Fixed packaged schemas with different namespaces (based on a patch by couac) (closes #1355)</li>
<li>Fixed PropelOMTask doesn&rsquo;t read propel.disableIdentifierQuoting from build.properties (patch from vmakinen) (closes #1353)</li>
<li>Fixed versionable behavior when defaultPhpNamingMethod is noChange (patch from niklas) (closes #1343)</li>
<li>Fixed bug in toXML() method stripping numbers from column names (closes #1352)</li>
<li>Slightly improved the soft_delete behavior doc, to emphasize the use of <code>includeDeleted()</code> over <code>disableSoftDelete()</code> (closes #1340)</li>
<li>Fixed full query logging regression (closes #1347)</li>
<li>Fixed typo in exception message thron by ModelCriteria (closes #1349)</li>
<li>Fixed regression in full query logging (closes #1344)</li>
<li>Fixed problem with reverse task and empty foreignSchema attributes</li>
</ul>
<h3>Installing</h3>
<p>As usual, Propel is available in the format you prefer:</p>
<p><em>Subversion tag</em></p>
<div class="CodeRay">
  <div class="code"><pre>&gt; svn checkout http://svn.propelorm.org/tags/1.6.0RC2</pre></div>
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
<p><a href="http://files.propelorm.org/propel-1.6.0RC2.tar.gz">http://files.propelorm.org/propel-1.6.0RC2.tar.gz</a> (Linux)</p>
<p><a href="http://files.propelorm.org/propel-1.6.0RC2.zip">http://files.propelorm.org/propel-1.6.0RC2.zip</a> (Windows)</p>
<h3>Next steps</h3>
<p>If no critical bug hits the Propel Trac in the upcoming week, this version will become the 1.6.0 stable. Please test and report any bug you may encounter.</p>
