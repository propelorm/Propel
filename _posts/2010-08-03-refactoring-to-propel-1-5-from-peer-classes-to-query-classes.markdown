---
layout: post
title: ! 'Refactoring to Propel 1.5: From Peer classes to Query classes'
published: true
---
<p>One of the most powerful features of Propel 1.5 lies in the <a href="http://propel.posterous.com/propel-query-by-example">generated Query classes</a>. But to get the most out of them, developers must change their habits and learn to use these new classes instead of the Peer classes.</p>
<p>This tutorial shows how to refactor an existing model code to take advantage of Propel 1.5 features. The code comes from a Forum plugin for symfony, called <a href="http://www.symfony-project.org/plugins/sfSimpleForumPlugin">sfSimpleForumPlugin</a>, initially written for Propel 1.2. You can find the <a href="http://trac.symfony-project.org/browser/plugins/sfSimpleForumPlugin/trunk/">plugin source code</a> in the symfony Subversion repository.</p>
<h3>General Philosophy</h3>
<p>Static methods are bad. They are hard to reuse, hard to test, and they cannot be chained.</p>
<p>On the other hand, Query methods are good. They are testable, chainable, embeddable, offer IDE completion, and they are as fast as static methods. Besides, they allow for a much more expressive syntax. Here is an example:<!--more--></p>
<div class="CodeRay">
  <div class="code"><pre>// Find the cheapest book by Tolsoi

// with Peer constants and static methods
$c = new Criteria();
$c-&gt;addJoin(BookPeer::AUTHOR_ID, AuthorPeer::ID);
$c-&gt;add(AuthorPeer::LAST_NAME, 'Tolstoi')
$c-&gt;addAscendingOrderByColumn(BookPeer::PRICE)
$book = BookPeer::doSelectOne($c);

// with Query classes
$book = BookQuery::create()
  -&gt;useAuthorQuery()
    -&gt;filterByLastName('Tolstoi')
  -&gt;endUse()
  -&gt;orderByPrice()
  -&gt;findOne();</pre></div>
</div>

<p>So the general guideline for converting a Propel &lt; 1.5 application should be to avoid static methods at all costs. That means that every time you fell like writing a static method in a Peer class, you should write a non-static method in the corresponding query class instead.</p>
<h3>Initial Static Model Code</h3>
<p>Let&rsquo;s first look at how the controllers interact with the model in the sfSimpleForumPlugin. Two controllers allow to display the list of latest posts written by a given user: one for the web page, the second for the RSS feed:</p>
<div class="CodeRay">
  <div class="code"><pre>// in modules/sfSimpleForum/lib/BasesfSimpleForumActions.class.php
class BasesfSimpleForumActions extends sfActions
{
  // ...

  public function executeUserLatestTopics()
  {
    $this-&gt;topics_pager = sfSimpleForumTopicPeer::getForUserPager(
      $this-&gt;user-&gt;getId(),
      $this-&gt;getRequestParameter('page', 1),
      sfConfig::get('app_sfSimpleForumPlugin_max_per_page', 10)
    );
    // ...
  }

  public function executeUserLatestTopicsFeed()
  {
    $this-&gt;topics = sfSimpleForumTopicPeer::getForUser(
      $this-&gt;user-&gt;getId(),
      sfConfig::get('app_sfSimpleForumPlugin_feed_max', 10)
    );
    // ...
  }
}</pre></div>
</div>

<p>In this example, two Peer methods are used: <code>sfSimpleForumTopicPeer::getForUserPager()</code>, and <code>sfSimpleForumTopicPeer::getForUser()</code>. Let&rsquo;s check the source code of these methods:</p>
<div class="CodeRay">
  <div class="code"><pre>// in lib/model/plugin/PluginsfSimpleForumTopicPeer.php
class PluginsfSimpleForumTopicPeer extends BasesfSimpleForumTopicPeer
{
  // ...

  public static function getForUserPager($user_id, $page = 1, $max_per_page = 10)
  {
    $c = self::getForUserCriteria($user_id);
    $pager = new sfPropelPager('sfSimpleForumTopic', $max_per_page);
    $pager-&gt;setPage($page);
    $pager-&gt;setCriteria($c);
    $pager-&gt;setPeerMethod('doSelectJoinAll');
    $pager-&gt;init();

    return $pager;
  }

  public static function getForUser($user_id, $max = 10)
  {
    $c = self::getForUserCriteria($user_id);
    $c-&gt;setLimit($max);

    return self::doSelectJoinAll($c);
  }

  protected static function getForUserCriteria($user_id)
  {
    $c = new Criteria();
    $c-&gt;add(self::USER_ID, $user_id);
    $c-&gt;addDescendingOrderByColumn(self::UPDATED_AT);

    return $c;
  } 

}</pre></div>
</div>

<p>In order to avoid repetition of code, the piece of logic that restricts the query to a single user was refactored into a <code>getForUserCriteria()</code> method. Also, both <code>getForUserPager()</code> and <code>getForUser()</code> eventually use <code>sfSimpleForumTopicPeer::doSelectJoinAll()</code> to hydrate topics together with forums.</p>
<h3>Remove Termination Methods</h3>
<p>It appears that <code>getForUserPager()</code> and <code>getForUser()</code>, apart from reusing the common method <code>getForUserCriteria()</code>, only terminate the query with no special added value. They can be seen as <em>termination methods</em> in the Propel Query terminology, since they don&rsquo;t return a <code>Criteria</code> object.</p>
<p>But <code>ModelCriteria</code> already offers most of the termination methods that you need (<code>find()</code>, <code>count()</code>, <code>paginate()</code>, etc.). So the right thing to do here is to keep only the code that adds logic to your model (the <code>getForUserCriteria()</code> method). This code is easy to move to a Propel Query class. And since a Propel Query is a <code>Criteria</code>, no need to create one in the method - just use <code>$this</code> instead.</p>
<div class="CodeRay">
  <div class="code"><pre>// in lib/model/plugin/PluginsfSimpleForumTopicQuery.php
class PluginsfSimpleForumTopicQuery extends BasesfSimpleForumTopicQuery
{
  // ...

  public function getForUserCriteria($user_id)
  {
    $this-&gt;add(self::USER_ID, $user_id);
    $this-&gt;addDescendingOrderByColumn(self::UPDATED_AT);

    return $this;
  } 
}</pre></div>
</div>

<p>The termination can be left to the controllers, which must be refactored a little:</p>
<div class="CodeRay">
  <div class="code"><pre>// in modules/sfSimpleForum/lib/BasesfSimpleForumActions.class.php
class BasesfSimpleForumActions extends sfActions
{
  // ...

  public function executeUserLatestTopics()
  {
    $this-&gt;topics_pager = sfSimpleForumTopicQuery::create()
      -&gt;getForUserCriteria($this-&gt;user-&gt;getId())
      -&gt;paginate($this-&gt;getRequestParameter('page', 1), sfConfig::get('app_sfSimpleForumPlugin_max_per_page', 10)
      );
    // ...
  }

  public function executeUserLatestTopicsFeed()
  {
    $this-&gt;topics = sfSimpleForumTopicQuery::create()
      -&gt;getForUserCriteria($this-&gt;user-&gt;getId())
      -&gt;limit(sfConfig::get('app_sfSimpleForumPlugin_feed_max', 10))
      -&gt;find();
    // ...
  }
}</pre></div>
</div>

<p>It&rsquo;s a good guideline to let the controllers do the termination themselves, and keep in the model classes only <em>filter</em> methods, which return the current query object. It will make your model code much more reusable.</p>
<p>There is one thing missing from this refactoring: the joined hydration that used to be offered by <code>doSelectJoinAll()</code>. Let&rsquo;s add it back to the model code using the new syntax offered by Propel Queries - the <code>joinWith()</code> method:</p>
<div class="CodeRay">
  <div class="code"><pre>// in lib/model/plugin/PluginsfSimpleForumTopicQuery.php
class PluginsfSimpleForumTopicQuery extends BasesfSimpleForumTopicQuery
{
  // ...

  public function getForUserCriteria($user_id)
  {
    $this-&gt;add(self::USER_ID, $user_id);
    $this-&gt;addDescendingOrderByColumn(self::UPDATED_AT);
    $this-&gt;joinWith('sfSimpleForumForum');

    return $this;
  } 
}</pre></div>
</div>

<h3>Use Generated Filter Methods</h3>
<p>From a Propel Query point of view, the <code>getForUserCriteria()</code> method <em>filters</em> and <em>orders</em> the query. The Propel Query API has a faster way of doing so, using the generated filter methods:</p>
<div class="CodeRay">
  <div class="code"><pre>// in lib/model/plugin/PluginsfSimpleForumTopicQuery.php
class PluginsfSimpleForumTopicQuery extends BasesfSimpleForumTopicQuery
{
  // ...

  public function getForUserCriteria($user_id)
  {
    return this
      -&gt;filterByUserId($user_id)
      -&gt;orderByUpdatedAt('desc')
      -&gt;joinWith('sfSimpleForumForum');
  } 
}</pre></div>
</div>

<p><code>filterByUserId()</code> replaces the call to <code>Criteria::add()</code>, and <code>orderByUpdatedAt()</code> replaces the longish <code>addDescendingOrderByColumn()</code>. All the Propel Query methods that are not termination methods return the current Query object, so the fluid interface was used to avoid the repetition of <code>$this</code> on each line.</p>
<h3>Use Objects Whenever Possible</h3>
<p>The controller calls the <code>filterByUserId()</code> method, but it has access to the whole User object. Why not keep objects for this filter? The generated Query class offer an object filter for each foreign key, including a more convenient <code>filterByUser($user)</code> method:</p>
<div class="CodeRay">
  <div class="code"><pre>// in modules/sfSimpleForum/lib/BasesfSimpleForumActions.class.php
class BasesfSimpleForumActions extends sfActions
{
  // ...

  public function executeUserLatestTopics()
  {
    $this-&gt;topics_pager = sfSimpleForumTopicQuery::create()
      -&gt;getForUserCriteria($this-&gt;user)
      -&gt;paginate($this-&gt;getRequestParameter('page', 1), sfConfig::get('app_sfSimpleForumPlugin_max_per_page', 10)
      );
    // ...
  }

  public function executeUserLatestTopicsFeed()
  {
    $this-&gt;topics = sfSimpleForumTopicQuery::create()
      -&gt;getForUserCriteria($this-&gt;user)
      -&gt;limit(sfConfig::get('app_sfSimpleForumPlugin_feed_max', 10))
      -&gt;find();
    // ...
  }
}</pre></div>
</div>

<p>The model code should be modified accordingly:</p>
<div class="CodeRay">
  <div class="code"><pre>&lt;?php
// in lib/model/plugin/PluginsfSimpleForumTopicQuery.php
class PluginsfSimpleForumTopicQuery extends BasesfSimpleForumTopicQuery
{
  // ...

  public function getForUserCriteria($user)
  {
    return this
      -&gt;filterByUser($user)
      -&gt;orderByUpdatedAt('desc')
      -&gt;joinWith('sfSimpleForumForum');
  } 
}</pre></div>
</div>

<p>Keep objects as long as you can in your model queries - the code will be clearer, and you will be able to achieve more elaborate queries. Propel encourages the use of objects over columns and foreign keys.</p>
<p>The new API has already allowed to dramatically reduce the Model and the Controller code, but it&rsquo;s only the beginning.</p>
<h3>Use Meaningful Names</h3>
<p>The middle piece of the method, which orders results by update date, could be refactored to be more expressive. Actually, tt returns the latest updated books first, so let&rsquo;s write it this way:</p>
<div class="CodeRay">
  <div class="code"><pre>// in lib/model/plugin/PluginsfSimpleForumTopicQuery.php
class PluginsfSimpleForumTopicQuery extends BasesfSimpleForumTopicQuery
{
  // ...

  public function getForUserCriteria($user)
  {
    return this
      -&gt;filterByUser($user)
      -&gt;lastUpdatedFirst()
      -&gt;joinWith('sfSimpleForumForum');
  }

  public function lastUpdatedFirst()
  {
    return this-&gt;orderByUpdatedAt('desc');
  } 
}</pre></div>
</div>

<p>This new method can then be reused in other queries easily.</p>
<p>Now it&rsquo;s time to wonder about the main method name. <code>getForUserCriteria()</code> was good for a Peer static method, but now that it&rsquo;s in a query class, it should be named differently. Something that like <code>latestForUser()</code> should fit:</p>
<div class="CodeRay">
  <div class="code"><pre>// in lib/model/plugin/PluginsfSimpleForumTopicQuery.php
class PluginsfSimpleForumTopicQuery extends BasesfSimpleForumTopicQuery
{
  // ...

  public function latestForUser($user)
  {
    return this
      -&gt;filterByUser($user)
      -&gt;lastUpdatedFirst()
      -&gt;joinWith('sfSimpleForumForum');
  }

  public function lastUpdatedFirst()
  {
    return this-&gt;orderByUpdatedAt('desc');
  } 
}</pre></div>
</div>

<p>Now the model code is expressive and reusable, and the controller code is very simple and readable:</p>
<div class="CodeRay">
  <div class="code"><pre>// in modules/sfSimpleForum/lib/BasesfSimpleForumActions.class.php
class BasesfSimpleForumActions extends sfActions
{
  // ...

  public function executeUserLatestTopics()
  {
    $this-&gt;topics_pager = sfSimpleForumTopicQuery::create()
      -&gt;latestForUser($this-&gt;user)
      -&gt;paginate($this-&gt;getRequestParameter('page', 1), sfConfig::get('app_sfSimpleForumPlugin_max_per_page', 10)
      );
    // ...
  }

  public function executeUserLatestTopicsFeed()
  {
    $this-&gt;topics = sfSimpleForumTopicQuery::create()
      -&gt;latestForUser($this-&gt;user)
      -&gt;limit(sfConfig::get('app_sfSimpleForumPlugin_feed_max', 10))
      -&gt;find();
    // ...
  }
}</pre></div>
</div>

<p>You should keep the amount of model code inside controller to a minimum. A good rule of thumb is to allow one creation method (<code>create()</code>), one termination method (<code>paginate()</code>, <code>find()</code>), and one logic method (like <code>latestForUser()</code>).</p>
<h3>Remove Things That Propel Can Do On Its Own</h3>
<p>The model code of the sfSimpleForum plugin contains several more examples of <code>getXXXCriteria()</code> methods in Peer classes that can benefit from a similar refactoring. It also contains a lot of custom code that can easily be replaced by native Propel Query features. For instance:</p>
<div class="CodeRay">
  <div class="code"><pre>// in lib/model/plugin/PluginsfSimpleForumForumPeer.php
class PluginsfSimpleForumForumPeer extends BasesfSimpleForumForumPeer
{
  public static function retrieveByStrippedName($stripped_name)
  {
    $c = new Criteria();
    $c-&gt;add(self::STRIPPED_NAME, $stripped_name);

    return self::doSelectOne($c);
  }

  public static function getAllAsArray()
  {
    $forums = self::doSelect(new Criteria());
    $res = array();

    foreach ($forums as $forum)
    {
      $res[$forum-&gt;getStrippedName()] = $forum-&gt;getName();
    }

    return $res;
  }
}</pre></div>
</div>

<p>The first method, <code>retrieveByStrippedName()</code> has a Model Query counterpart, out of the box:</p>
<div class="CodeRay">
  <div class="code"><pre>// retrieve one forum by stripped name
$forum = sfSimpleForumForumQuery::create()
  -&gt;findOneByStrippedName($stripped_name);</pre></div>
</div>

<p>The second method, <code>getAllAsArray()</code>, is of no use since Propel naturally returns collections, which are one line away from arrays:</p>
<div class="CodeRay">
  <div class="code"><pre>// get all forum names as an array indexed by stripped name
$forums = sfSimpleForumForumQuery::create()
  -&gt;find()
  -&gt;toKeyValue('StrippedName', 'Name');</pre></div>
</div>

<p>Additionally, all the methods implementing a custom join hydration (like <code>PluginsfSimpleForumForumPeer ::doSelectJoinCategoryLeftJoinPost()</code>, or <code>PluginsfSimpleForumPostPeer::doSelectJoinTopicAndForum()</code>) become useless since you can choose the joined objects directly in the query using <code>joinWith()</code>.</p>
<p>All in all, more than 75% of the Peer code of the current sfSimpleForum plugin doesn&rsquo;t need to be ported to a Query object - new Propel 1.5 features do the job out of the box.</p>
<h3>Conclusion</h3>
<p>Moving existing model code written for Propel 1.4 to Propel 1.5 Query classes is fast, easy, and it will make your application better. Reusability comes by moving code from the controller to the model. Expressivity comes by using objects as arguments, and meaningful method names. And ease of maintenance comes by keeping the number of Model methods low.</p>
<p>Applications written for Propel 1.3 or 1.4 work out of the box with Propel 1.5, which is a backwards compatible release. Since it&rsquo;s so easy to replace old code by new code optimized for Propel 1.5, don&rsquo;t wait, and upgrade now.</p>
