---
layout: post
title: The End of Autoloading
published: true
---
<p>Autoloading in PHP is a great time saver. It lets you write concise scripts without the knowledge of the exact directory structure of the libraries you use. But with the arrival of namespaces in PHP 5.3, and the influence of Java over new generation PHP frameworks, autoloading is changing. In the near future, explicit autoloading will be ubiquitous, but with none of the advantages of the old style autoloading.<!--more--></p>
<h3>Before Autoloading, There Were File Paths</h3>
<p>Before autoloading, every class file had to explicitly declare the path to its dependencies. Source code would look like the following, taken from the <a href="http://pear.php.net/">PEAR library</a>:</p>
<div class="CodeRay">
  <div class="code"><pre>&lt;?php
require_once 'PEAR.php';
require_once 'PEAR/DependencyDB.php';

class PEAR_Registry extends PEAR {
  //...
}</pre></div>
</div>

<p>Dependencies appeared clearly at the top of every class. In this code snippet, even if the first use of the <code>PEAR_DependencyDB</code> class is hidden line 328 of the <code>PEAR_Registry</code> class, the dependency is obvious.</p>
<p>In most cases, the path was relative, and the PHP runtime had to rely on the <code>include_path</code> configuration. Performance decreased as the <code>include_path</code> size increased. And the top of many source files was soon littered with <code>require_once</code> calls that harmed readability.</p>
<h3>Then Came SPL Autoloading</h3>
<p><code>require_once</code> was notably slow. On servers without a fast disk or an opcode cache, it was better not to use <code>require_once</code> at all. The PHP SPL library&rsquo;s <a href="http://php.net/manual/en/function.spl-autoload.php"><code>spl_autoload_register()</code></a> function then came to a great use. It made it possible to remove <code>require_once</code> calls from source code completely. It made applications faster.</p>
<p>But the greatest benefit was that you could use a class without actually knowing where its source file was in the directory structure. Here is an extract from the <a href="http://www.symfony-project.org/tutorial/1_0/en/my-first-project">&ldquo;My First Project&rdquo; tutorial</a> for the symfony framework:</p>
<div class="CodeRay">
  <div class="code"><pre>&lt;?php
class postActions extends sfActions
{
  public function executeList()
  {
    $this-&gt;posts = PostPeer::doSelect(new Criteria());
  }
}</pre></div>
</div>

<p>Here, no <code>require_once</code> at all, even if this class depends on the <code>sfActions</code>, <code>PostPeer</code>, and <code>Criteria</code> classes. Developers could dive into the business logic right away, without spending a single second figuring out where the dependencies were. This was <a href="http://en.wikipedia.org/wiki/Rapid_application_development">Rapid Application Development</a> at work.</p>
<h3>Autoloading Implementations</h3>
<p>Implementation of the actual autoloading would vary. Some libraries, like the <a href="http://www.propelorm.org/">Propel</a> runtime, included a list of all the classes that could be required, together with the path to the class source. Here is an extract from the <code>Propel</code> class source code:</p>
<div class="CodeRay">
  <div class="code"><pre>&lt;?php
class Propel
{
  // ..
  protected static $autoloadMap = array(
    'DBAdapter'      =&gt; 'adapter/DBAdapter.php',
    'DBMSSQL'        =&gt; 'adapter/DBMSSQL.php',
    'MssqlPropelPDO' =&gt; 'adapter/MSSQL/MssqlPropelPDO.php',
    'MssqlDebugPDO'  =&gt; 'adapter/MSSQL/MssqlDebugPDO.php',
    // etc.
}</pre></div>
</div>

<p>This technique allowed to hide the actual class path, but forced the library developer to update the autoload map each time a new class was introduced. Another technique, used in the <a href="http://www.symfony-project.org/gentle-introduction/1_4/en/02-Exploring-Symfony-s-Code#chapter_02_sub_class_autoloading">symfony framework</a>, used a one-time file iterator that browsed the project directory structure, indexing all <code>.class.php</code> files. Despite the performance impact on the first request, this technique removed the burden to keep an autoload map up-to-date, and worked for classes outside the framework as well.</p>
<p>Even better, the symfony autoloading technique allowed to override framework classes with custom ones. The file iterator browsed the directory structure in a certain order: user directories first, then project directories, then plugin directories, and framework directories last. So the developer could create a custom <code>PostPeer</code> class that would override the other <code>PostPeer</code> class provided by a plugin.</p>
<p>Autoloading was at his top: fast, powerful, concise.</p>
<h3>Namespaces Autoloading</h3>
<p>The arrival of <a href="http://www.php.net/manual/en/language.namespaces.rationale.php">namespaces</a> in PHP 5.3 forced autoloading techniques to change. An initiative started by some framework authors tried to allow technical interoperability between libraries and the autoloader implementations. Called the <a href="http://groups.google.com/group/php-standards">&ldquo;PHP Standards Working Group&rdquo;</a>, this community agreed that explicit is better than implicit, and that a fully qualified classname would be the relative path to the class source file:</p>
<div class="CodeRay">
  <div class="code"><pre>\Doctrine\Common\IsolatedClassLoader
  =&gt; /path/to/project/lib/vendor/Doctrine/Common/IsolatedClassLoader.php
\Symfony\Core\Request
  =&gt; /path/to/project/lib/vendor/Symfony/Core/Request.php
\Zend\Acl
  =&gt; /path/to/project/lib/vendor/Zend/Acl.php
\Zend\Mail\Message
  =&gt; /path/to/project/lib/vendor/Zend/Mail/Message.php</pre></div>
</div>

<p>Libraries agreeing with the initiative should follow the naming and file structure principles, and provide an autoloading implementation compatible with the example <a href="https://gist.github.com/221634"><code>SplClassLoader</code></a> class. This is the case of most &ldquo;new-generation&rdquo; frameworks in 2011. For instance, here is an extract of the new <a href="http://symfony.com/doc/2.0/quick_tour/the_big_picture.html">&ldquo;My First Project&rdquo; tutorial</a> in Symfony2:</p>
<div class="CodeRay">
  <div class="code"><pre>&lt;?php
namespace Application\HelloBundle\Controller;
use Symfony\Framework\WebBundle\Controller;

class HelloController extends Controller
{
  public function indexAction($name)
  {
    $author = new \Application\HelloBundle\Model\Author();
    $author-&gt;setFirstName($name);
    $author-&gt;save();
    return $this-&gt;render('HelloBundle:Hello:index', array('name' =&gt; $name, 'author' =&gt; $author));
  }
}</pre></div>
</div>

<p>There is still no <code>require_once</code> in this code - autoloading is at work. The PHP autoloading looks for a <code>Symfony\Framework\WebBundle\Controller</code> class in the <code>Symfony/Framework/WebBundle/Controller.php</code> file. The file path is no longer relative to the <code>include_path</code>, since the autoloader must be initialized with the base path to the library directory.</p>
<p>A first advantage is that there is no more &ldquo;first request&rdquo; penalty on performance. Also, dependencies are explicit again. Lastly, if you want to override a class provided by the framework, alias a custom class using a <code>use</code> and you&rsquo;re ready to go:</p>
<div class="CodeRay">
  <div class="code"><pre>&lt;?php
namespace Application\HelloBundle\Controller;
// use a custom Controller class instead of the framework's Controller
use Application\HelloBundle\Tools\Controller;

class HelloController extends Controller
{
  // same code as before
}</pre></div>
</div>

<h3>This Is No Longer Rapid Application Development</h3>
<p>Doesn&rsquo;t the initial <code>use</code> in the previous example remind you of something? Right, it&rsquo;s very similar to the <code>require_once</code> calls of the first example without autoloading:</p>
<div class="CodeRay">
  <div class="code"><pre>&lt;?php
// old style
require_once 'Application/HelloBundle/Tools/Controller.php';
// new style
use 'Application\HelloBundle\Tools\Controller';</pre></div>
</div>

<p>The added verbosity of the namespace autoloading reduces the ease of use introduced by SPL autoloading in the first place.</p>
<p>The problem is not only about having to write more code. Consider the work to do to use a class from a &ldquo;new generation&rdquo; framework:</p>
<ol>
<li>Parse the framework directory structure, looking for the source file of the class to use</li>
<li>Open the source file, and copy the <code>namespace</code> declaration</li>
<li>Paste the namespace declaration inside a <code>use</code> statement in the custom code.</li>
</ol>
<p>This copy/paste task happens a lot when working with Symfony2, for instance. This can be somehow improved when you use an IDE with code completion, but you still have to know the fully qualified name of the classes you need. You must know the framework classes by heart to be able to use them. That&rsquo;s a step backwards in terms of usability when compared to first-generation autoloading, where only knowing the class name was enough.</p>
<h3>There Is No Better Way In PHP</h3>
<p>Wouldn&rsquo;t it be great if you could use new generation framework code without knowing where the required dependencies lay on the filesystem? What if you could write a Symfony2 controller like this:</p>
<div class="CodeRay">
  <div class="code"><pre>&lt;?php
class HelloController extends Controller
{
  public function indexAction($name)
  {
    $author = new Author();
    $author-&gt;setFirstName($name);
    $author-&gt;save();
    return $this-&gt;render('HelloBundle:Hello:index', array('name' =&gt; $name, 'author' =&gt; $author));
  }
}</pre></div>
</div>

<p>A smart autoloader could catch the call for the <code>Controller</code> class, open a default implementation (in <code>Symfony/Framework/WebBundle/Controller.php</code>), and dynamically alias <code>Symfony\Framework\WebBundle\Controller</code> to <code>Controller</code>. Except that in PHP, <code>use</code> creates aliases at compile time, so that doesn&rsquo;t work. There is a possibility to implement such an autoloader using <code>eval()</code>, but that&rsquo;s probably worst than requiring files by hand.</p>
<p>Also, aliasing all classes in a usability layer on top of the framework isn&rsquo;t possible either. It would defeat the lazy loading of core classes, and fail on duplicate class names (e.g. <code>Symfony\Framework\WebBundle\Command</code> and <code>Symfony\Components\Console\Command\Command</code>).</p>
<p>Unless framework authors change their mind on autoloading, the future of PHP will be verbose.</p>
<h3>Solving The Problem</h3>
<p>I personally think that the added verbosity slows down the development a lot. Take <a href="http://johnsonpage.org/more/php-microframeworks">microframeworks</a> for instance: they give you a way to answer an http request in a fast way but with minimum MVC separation. Compare the code for a &ldquo;Hello, world&rdquo; application written using <a href="http://www.slimframework.com/">Slim</a>, a microframework without namespace autoloading, and <a href="http://github.com/fabpot/silex/">Silex</a>, a microframework using namespace autoloading:</p>
<div class="CodeRay">
  <div class="code"><pre>&lt;?php
// Hello world with Slim
require_once 'slim/Slim.php';
Slim::init();
Slim::get('/hello/:name', function($name) {
    Slim::render('hello.php', array('name' =&gt; $name));
});
Slim::run();

// Hello world with Silex
require_once 'silex.phar';
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Templating\Engine;
use Symfony\Component\Templating\Loader\FilesystemLoader;
use Silex\Framework;
$framework = new Framework(array(
  'GET /hello/:name' =&gt; function($name) {
    $loader = new FilesystemLoader('views/%name%.php');
    $view = new Engine($loader);
    return new Response($view-&gt;render(
      'hello', 
      array('name' =&gt; $name)
    ));
  }
));
$framework-&gt;handle()-&gt;send();</pre></div>
</div>

<p>In the second example, autoloading comes in the way, and makes things harder.</p>
<p>Developers of new generation frameworks explain that the added verbosity is the price to pay for a better quality code. I&rsquo;m not sure I&rsquo;m willing to pay this price. I don&rsquo;t like to see PHP as the next Java, where the code is great from a CS graduate point of view, but very expensive to write. It makes me want to switch to other languages, where this namespace autoloading discussion never took place, and where rapid application development is still possible.</p>
<p>Take Ruby for instance. It offers a microframework called <a href="http://www.sinatrarb.com">Sinatra</a>, which makes the &ldquo;Hello, world&rdquo; application really concise:</p>
<div class="CodeRay">
  <div class="code"><pre>require 'sinatra'
require 'erb'
get '/hello/:name' do |name|
    @name = name
    erb :hello
end</pre></div>
</div>

<p>Oh, look, there are <code>require</code> statements in this script. And yet, it&rsquo;s so fast and easy to use.</p>
