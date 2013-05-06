---
layout: post
title: Don't Copy Code. Oh, and Inheritance and Composition are Bad, Too
published: true
---
<p>I often see, inside the code produced by some of our junior devs, entire blocks of code copied from one class to another. I'm always shocked by this very bad practice. But it's hard to get these developers back into the right path, because there is no perfect alternative.</p>
<h3>Don't Copy Code</h3>
<p>Ctrl+C and Ctrl+V are probably my favorite computer tools, just after email. That's a great invention and an incredible time saver. The problem is that it should never, ever be used by a developer. It's like giving food to a <a href="http://www.youtube.com/watch?v=h24CFZqSEAA">Gremlin</a>: no matter if you can do it without a second thought during daytime, you mustn't do it after midnight. That's the rule.</p>
<p>Why is it bad to copy code? Because each occurrence of the code will need to be tested and maintained separately. It multiplies the costs by the number of times Ctrl+V was hit. Plus it is a source of confusion and of a false security feeling. You find a bug, you fix it once, and you think you're done. Except that piece of code was copied 17 times across the application, but you won't remember it until the bug reappears all of a sudden.</p>
<p>So there should be an alarm bell ringing in your mind every time you see something like this:<!--more--></p>
<div class="CodeRay">
  <div class="code"><pre>class Book
{
  // some methods

  public function persist()
  {
    apc_store(get_class($this) . $this-&gt;id, serialize($this));
  }

  public static function restore($id)
  {
    return unserialize(apc_fetch(get_class($this) . $id));
  }
}

class Author
{
  // some methods

  public function persist()
  {
    apc_store(get_class($this) . $this-&gt;id, serialize($this));
  }

  public static function restore($id)
  {
    return unserialize(apc_fetch(get_class($this) . $id));
  }
}</pre></div>
</div>

<p>Obviously, in this case the developer needed to give the same ability to two classes: the ability to be persisted in the APC cache, and then restored. And since it was done once in the <code>Book</code> class, the easiest way to give the same ability to the <code>Author</code> class was to copy the code. Right? Wrong.</p>
<p><a href="http://en.wikipedia.org/wiki/Don't_repeat_yourself">Don't Repeat Yourself</a>. I won't write it twice.</p>
<p><strong>Tip</strong>: If you want to check if a PHP application contains some duplicated code blocks, I recommend <a href="https://github.com/sebastianbergmann/phpcpd"><code>phpcd</code></a>, a Copy/Paste detector library by Sebastian Bergmann. It's dead simple to install, and very efficient.</p>
<h3>Don't Use Inheritance for Horizontal Code Reuse</h3>
<p>Junior devs quickly understand their mistake once they fix a bug duplicated 17 times. And since they learned Object-Oriented Programming, they often turn up to Inheritance as a great pattern to avoid code duplication. Therefore, the previous piece of code ends up looking like:</p>
<div class="CodeRay">
  <div class="code"><pre>class Persistable
{ 
  public function persist()
  {
    apc_store(get_class($this) . $this-&gt;id, serialize($this));
  }

  public static function restore($id)
  {
    return unserialize(apc_fetch(get_class($this) . $id));
  }
}

class Book extends Persistable
{
  // some methods
}

class Author extends Persistable
{
  // some methods
}</pre></div>
</div>

<p>That's much better: there is no more code duplication. A bug in one of the <code>Persistable</code> methods only needs to be fixed once, and this behavior class can be further reused with only two words (<code>extends Persistable</code>).</p>
<p>Except that's an abuse of the inheritance concept. From the business point of view, a <code>Book</code> <em>is not</em> a <code>Persistable</code>. It's a <code>Publication</code>, or a <code>StoreItem</code>. It <em>has</em> a persistable ability, but that's not the same verb. In fact, the <code>Book</code> class needs to reuse an ability that is not specific to its parent. That's called "horizontal code reuse", as opposed to the "vertical code reuse" of inheritance.</p>
<p>And to better distinguish the two types of reuse, a good rule of thumb is that a class often needs several horizontal reuses, while it can only have at most one vertical reuse. For instance, the <code>Book</code> class needs to be persistable, but it also needs to be sellable (so it must have a price), storable (so it must have a stock quantity), etc. But inheritance only accepts one parent (at least in PHP), so you'll have to choose one.</p>
<p>There is a pattern here: every time you create a class with a name ending with '-able', that's an ability, or "behavior" class, and that's a class that you should not extend. Or you won't be able to extend anything else. And a class isn't mostly distinguished by what it <em>can do</em>, but by what it <em>is</em>.</p>
<h3>Composition to The Rescue</h3>
<p>In PHP, a good workaround for taking the ability of multiple parent objects is <em>composition</em>. Transform a "behavior" class into a "service" class, and inject that class to all the classes that need it. Now that's clean:</p>
<div class="CodeRay">
  <div class="code"><pre>class PersistenceService
{ 
  public function persist($object, $class)
  {
    apc_store($class . $object-&gt;id, serialize($object));
  }

  public static function restore($id, $class)
  {
    return unserialize(apc_fetch($class . $id));
  }
}

class Book
{
  // some methods

  protected $persistence;

  public function setPersistence(PersistenceService $persistence)
  {
    $this-&gt;persistence = $persistence;
  }

  public function persist()
  {
    $this-&gt;persistence-&gt;persist($this, get_class($this));
  }

  public static function restore($id)
  {
    return $this-&gt;persistence::restore($id, get_class($this));
  }
}

class Author
{
  // some methods

  protected $persistence;

  public function setPersistence(PersistenceService $persistence)
  {
    $this-&gt;persistence = $persistence;
  }

  public function persist()
  {
    $this-&gt;persistence-&gt;persist($this, get_class($this));
  }

  public static function restore($id)
  {
    return $this-&gt;persistence::restore($id, get_class($this));
  }
}</pre></div>
</div>

<p>All the persistence logic is now encapsulated into one unique class. It's testable, it's isolated, and it's easy to reuse. Cherry on the cake, the <code>Book</code> and <code>Author</code> classes are Plain Old PHP Objects (or POPO) again, meaning they don't extend anything. Unit test gurus advocate that everything should be POPO, and despise inheritance, so that is probably a good thing.</p>
<p>Notice that the end user isn't allowed to chain method calls and has no knowledge of the actual persistence layer. She manipulates the <code>Book::persist()</code> and the <code>Book::restore()</code> proxy methods, and there is no public way to access the persistence layer. That's following the "Principle of Least Knowledge", also called <a href="http://en.wikipedia.org/wiki/Law_of_Demeter">"Law of Demeter"</a>.</p>
<p>You may complain that the <code>Book</code> and <code>Author</code> classes are now bigger than in the first code snippet, and that they show a slight code duplication. That's a valid complaint, that I will address shortly. But before that, let me make things a little bit more complex.</p>
<h3>Manage Dependencies with a Dependency Injection Container</h3>
<p>So inheritance was replaced by composition, that's fine. If the <code>Book</code> class needs to reuse code from several service classes, then you'll add new service setters, and new proxy methods for the service abilities.</p>
<p>But the above code doesn't work out of the box: the service class must be initialized. Before, you could just write:</p>
<div class="CodeRay">
  <div class="code"><pre>$book = new Book();
$book-&gt;persist();</pre></div>
</div>

<p>Now you must write:</p>
<div class="CodeRay">
  <div class="code"><pre>$book = new Book();
$book-&gt;setPersistence(new PersistenceService());
$book-&gt;persist();</pre></div>
</div>

<p>Besides, you may not want to duplicate the <code>PersistenceService</code> class, because it may be initialized with some configuration settings, or because it's expensive in terms of memory consumption. So you should rather keep a service container somewhere with access to public services:</p>
<div class="CodeRay">
  <div class="code"><pre>// $sc is a service container
$book = new Book();
$book-&gt;setPersistence($sc-&gt;getService('PersistenceService'));
$book-&gt;persist();</pre></div>
</div>

<p>There is a smarter way to do this - and to avoid the pitfall of creating a registry class, often plagued by the <code>static</code> keyword. A <a href="http://en.wikipedia.org/wiki/Dependency_injection">Dependency Injection</a> Container (or DIC) lazy-loads services on demand, manages dependencies, and allows to subclass a service easily. There are a few good implementations of DIC in PHP, I recommend the one from the Symfony Components library, available <a href="https://github.com/symfony/DependencyInjection">in PHP 5.3</a> or in <a href="http://components.symfony-project.org/dependency-injection/">PHP 5.2</a>.</p>
<p>With a DIC, you can manage services into a configuration file. This file is then compiled into a PHP class for a faster execution.</p>
<div class="CodeRay">
  <div class="code"><pre>&lt;?xml version=&quot;1.0&quot; ?&gt;
&lt;container xmlns=&quot;http://symfony-project.org/2.0/container&quot;&gt;
  &lt;services&gt;
    &lt;service id=&quot;persistence&quot; class=&quot;PersistenceService&quot; shared=&quot;false&quot;&gt;
    &lt;/service&gt;
  &lt;/services&gt;
&lt;/container&gt;</pre></div>
</div>

<h3>Code Generation: A Good Alternative</h3>
<p>If you're from a PHP background and not from a Java background, you may be overwhelmed by the complexity of a dependency injection container. I tend to agree - seeing that classes like <code>Compiler\ResolveDefinitionTemplatesPass</code> or <code>ContainerAwareInterface</code> are necessary for simple horizontal code reuse feels like using a sledgehammer to crack a nut.</p>
<p>And yet the classes using the services need a lot of duplicated code written by hand, just for the composition and interfaces to the injected services. In addition, the service class may execute expensive code to adapt the service to the class it's used in. For instance, a better persistence service class should check the class of the object passed to the <code>persist()</code> method instead of requiring it as an argument. That's not an expensive execution, but in the real world this kind of runtime introspection really penalizes performance.</p>
<p>One important thing to notice is that the complexity behind a DIC implies a compilation pass, which generates PHP code for a better runtime performance.</p>
<p>Since code generation is used, why not extend it to directly add the necessary code to the end class? What if you could just generate the following code?</p>
<div class="CodeRay">
  <div class="code"><pre>class Book
{
  // some methods

  public function persist()
  {
    apc_store(get_class($this) . $this-&gt;id, serialize($this));
  }

  public static function restore($id)
  {
    return unserialize(apc_fetch(get_class($this) . $id));
  }
}

class Author
{
  // some methods

  public function persist()
  {
    apc_store(get_class($this) . $this-&gt;id, serialize($this));
  }

  public static function restore($id)
  {
    return unserialize(apc_fetch(get_class($this) . $id));
  }
}</pre></div>
</div>

<p>Yep, that's exactly the same code as in the first snippet. But if it's generated, code duplication isn't a bad thing anymore. There is still a central place where the code lies - in the generator. The public interface is mostly the same as in the DIC version. The service doesn't need to be injected - it's "baked in" by code generation.</p>
<p>And it's blazingly fast. No runtime introspection, no need to instantiate multiple service objects.</p>
<p>And it offers IDE completion for "composed" services.</p>
<p>Propel uses code generation to offer a fast and configurable way to do horizontal code reuse. Just like with a DIC, you need a few lines of configuration to enable a <a href="http://www.propelorm.org/wiki/Documentation/1.5/Behaviors">behavior</a> on a class:</p>
<div class="CodeRay">
  <div class="code"><pre>&lt;table phpName=&quot;Book&quot; name=&quot;book&quot; &gt;
  &lt;behavior name=&quot;persistable&quot; /&gt;
&lt;/table&gt;</pre></div>
</div>

<p>Then, Propel generates all the necessary code in a 'Base' class that you only need to extend. That's right, horizontal reuse through inheritance becomes possible again once you use code generation. And it doesn't prevent <a href="http://www.propelorm.org/wiki/Documentation/1.5/Inheritance">true model inheritance</a> - Propel supports it out of the box.</p>
<p>Since you'll probably be using code generation to manage horizontal code reuse, use it for good. Skip the DIC and generate the code directly in the final classes.</p>
<h3>Mixins: The Only Good Solution</h3>
<p>Dependency injection and code generation are just workarounds for a limit of the PHP language. PHP doesn't offer a native way to handle horizontal code reuse. Other languages, like Ruby, support the concept of "<a href="http://en.wikipedia.org/wiki/Mixin">Mixin</a>", which offers some kind of multiple inheritance. Here is an example taken from the <a href="http://www.ruby-doc.org/docs/ProgrammingRuby/html/tut_modules.html">Ruby documentation</a>:</p>
<div class="CodeRay">
  <div class="code"><pre>module Debug
  def whoAmI?
    &quot;#{self.type.name} (\##{self.id}): #{self.to_s}&quot;
  end
end

class Phonograph
  include Debug
  # ...
end

class EightTrack
  include Debug
  # ...
end</pre></div>
</div>

<p>The <code>Phonograph</code> and <code>EightTrack</code> classes can reuse the code from the <code>Debug</code> behavior without extending it. That completely removes the need for code duplication, and for all the workaround that were exposed in this article.</p>
<p>Fortunately, PHP will soon have some sort of Mixin support - only they're called <a href="http://wiki.php.net/rfc/horizontalreuse#traits_-_reuse_of_behavior_committed_to_trunk">"Traits"</a>. Traits would allow the <code>Book</code> and <code>Author</code> to get the "persistable" behavior in a clean way:</p>
<div class="CodeRay">
  <div class="code"><pre>trait Persistable
{
  public function persist()
  {
    apc_store(get_class($this) . $this-&gt;id, serialize($this));
  }

  public static function restore($id)
  {
    return unserialize(apc_fetch(get_class($this) . $id));
  }
}

class Book
{
  use Persistable;

  // some methods
}

class Author
{
  use Persistable;

  // some methods
}</pre></div>
</div>

<p>Notice the return of the "-able" suffix in this example. Classes are getting access to a behavior, not a service.</p>
<h3>Conclusion</h3>
<p>Traits are already implemented in the development version of PHP (probably called PHP 5.4). Until this version is released (no date for now), you're stuck with just workarounds. Choose the one that better fits your needs, and don't get too attached to it. Dependency Injection Containers and Code Generators will soon become overkill once you can do mixins in PHP.</p>
<p>But one thing will always remain valid: You do no copy code. This is the First Rule of the Developer. And do you know what the Second Rule is?</p>
