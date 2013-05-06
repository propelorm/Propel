---
layout: post
title: ! 'Propel, PHP 5.3, and Namespaces: They Love Each Other'
published: true
---
<p>Propel was originally a PHP4 port of the Java Torque library - that was <a href="http://propel.tigris.org/source/browse/propel/www/index.html?revision=1.2&amp;view=markup">a long time ago</a>. It was ported to PHP5 quite early - about <a href="http://www.propelorm.org/browser/tags/1.1.0">five years ago</a>. Since then, Propel has been compatible with every stable PHP release. Propel 1.5 requires PHP 5.2, but also <strong>works seamlessly with PHP 5.3</strong>. That means that if PHP 5.3 is your version of choice, you can use Propel right now - no need to wait for a new version.</p>
<p>But “working” doesn’t mean “taking the best advantage of”. By maintaining backwards compatibility with PHP versions less than 5.3, Propel can’t take advantage of the greatest advances of PHP 5.3 - late static binding, anonymous functions, namespaces, closures, phar, to name only a few. Or can it?</p>
<p><!--more-->Code generation allows to modify the model classes code based on configuration. If a developer wants to use PHP 5.3 only, then why not enable some of the PHP 5.3 features in the model classes?</p>
<p>This is getting real with the recent <strong>introduction of namespaces</strong> in Propel 1.5. That’s right, even though Propel 1.5 requires only PHP 5.2, it can generate model classes using namespaces only supported since PHP 5.3. And this is very easy to enable: just add a <code>namespace</code> attribute in the <code>&lt;database&gt;</code> tag of the XML schema, rebuild the object model, and the generated classes now use the namespace:</p>
<div class="CodeRay">
  <div class="code"><pre><span class="preprocessor">&lt;?xml version=&quot;1.0&quot; encoding=&quot;UTF-8&quot; standalone=&quot;no&quot;?&gt;</span>
<span class="tag">&lt;database</span> <span class="attribute-name">name</span>=<span class="string"><span class="delimiter">&quot;</span><span class="content">bookstore</span><span class="delimiter">&quot;</span></span> <span class="attribute-name">namespace</span>=<span class="string"><span class="delimiter">&quot;</span><span class="content">Bookstore</span><span class="delimiter">&quot;</span></span> <span class="attribute-name">defaultIdMethod</span>=<span class="string"><span class="delimiter">&quot;</span><span class="content">native</span><span class="delimiter">&quot;</span></span><span class="tag">&gt;</span>
  <span class="tag">&lt;table</span> <span class="attribute-name">name</span>=<span class="string"><span class="delimiter">&quot;</span><span class="content">book</span><span class="delimiter">&quot;</span></span><span class="tag">&gt;</span>
    <span class="comment">&lt;!-- --&gt;</span>
  <span class="tag">&lt;/table&gt;</span>
  <span class="tag">&lt;table</span> <span class="attribute-name">name</span>=<span class="string"><span class="delimiter">&quot;</span><span class="content">author</span><span class="delimiter">&quot;</span></span><span class="tag">&gt;</span>
    <span class="comment">&lt;!-- --&gt;</span>
  <span class="tag">&lt;/table&gt;</span>
<span class="tag">&lt;/database&gt;</span></pre></div>
</div>

<p>Now you can use the namespaced model classes by using the fully qualified name, or by aliasing the class name. The Propel runtime autoloading works as usual:</p>
<div class="CodeRay">
  <div class="code"><pre><span class="inline-delimiter">&lt;?php</span>
<span class="comment">// use fully qualified name</span>
<span class="local-variable">$book</span> = <span class="keyword">new</span> \<span class="constant">Bookstore</span>\<span class="constant">Book</span>();

<span class="comment">// or use an alias</span>
<span class="keyword">use</span> <span class="constant">Bookstore</span>\<span class="constant">Book</span>;
<span class="local-variable">$book</span> = <span class="keyword">new</span> <span class="constant">Book</span>();
<span class="comment">// remember to use the \ namespace for core Propel classes in this case</span>
<span class="local-variable">$con</span> = \<span class="constant">Propel</span>::getConnection();
<span class="local-variable">$book</span>-&gt;save(<span class="local-variable">$con</span>);</pre></div>
</div>

<p>The namespace is used for the ActiveRecord class, but also for the Query and Peer classes. Just remember that when you use relation names in a query, the namespace should not appear:</p>
<div class="CodeRay">
  <div class="code"><pre><span class="inline-delimiter">&lt;?php</span>
<span class="local-variable">$author</span> = \<span class="constant">Bookstore</span>\<span class="constant">AuthorQuery</span>::create()
  -&gt;useBookQuery()
    -&gt;filterByPrice(<span class="predefined">array</span>(<span class="string"><span class="delimiter">'</span><span class="content">max</span><span class="delimiter">'</span></span> =&gt; <span class="integer">10</span>))
  -&gt;endUse()
  -&gt;findOne();</pre></div>
</div>

<p>You can extend the database namespace in a given table by setting a <code>namespace</code> attribute of the <code>&lt;table&gt;</code> tag:</p>
<div class="CodeRay">
  <div class="code"><pre><span class="preprocessor">&lt;?xml version=&quot;1.0&quot; encoding=&quot;UTF-8&quot; standalone=&quot;no&quot;?&gt;</span>
<span class="tag">&lt;database</span> <span class="attribute-name">name</span>=<span class="string"><span class="delimiter">&quot;</span><span class="content">bookstore</span><span class="delimiter">&quot;</span></span> <span class="attribute-name">namespace</span>=<span class="string"><span class="delimiter">&quot;</span><span class="content">Bookstore</span><span class="delimiter">&quot;</span></span> <span class="attribute-name">defaultIdMethod</span>=<span class="string"><span class="delimiter">&quot;</span><span class="content">native</span><span class="delimiter">&quot;</span></span><span class="tag">&gt;</span>
  <span class="comment">&lt;!-- --&gt;</span>
  <span class="tag">&lt;table</span> <span class="attribute-name">name</span>=<span class="string"><span class="delimiter">&quot;</span><span class="content">publisher</span><span class="delimiter">&quot;</span></span> <span class="attribute-name">namespace</span>=<span class="string"><span class="delimiter">&quot;</span><span class="content">Book</span><span class="delimiter">&quot;</span></span><span class="tag">&gt;</span>
    <span class="comment">&lt;!-- --&gt;</span>
  <span class="tag">&lt;/table&gt;</span>
<span class="tag">&lt;/database&gt;</span></pre></div>
</div>

<p>The corresponding object model objects will use the table namespace as a subnamespace of the database namespace:</p>
<div class="CodeRay">
  <div class="code"><pre><span class="inline-delimiter">&lt;?php</span>
<span class="local-variable">$publisher</span> = <span class="keyword">new</span> \<span class="constant">Bookstore</span>\<span class="constant">Book</span>\<span class="constant">Publisher</span>();
<span class="local-variable">$publisher</span>-&gt;save();</pre></div>
</div>

<p>That’s as simple as this. You can mix classes with various namespaces in the same schema, and these classes can share relations regardless of their namespaces. All the features you love and use in Propel 1.5 - behaviors, many-to-many relationships, joined hydration - work the same for namespaced models.</p>
<p>The new namespace feature is available in the 1.5 branch, and soon in the stable 1.5.2 release. It is <a href="http://www.propelorm.org/wiki/Documentation/1.5/Namespaces">already documented</a>, as usual - and ready for you to use it.</p>
