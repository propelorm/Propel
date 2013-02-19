---
layout: post
title: Propel Gets Class Table Inheritance, With A Twist
published: true
---
<p>Propel 1.6 already supports <a href="http://www.martinfowler.com/eaaCatalog/singleTableInheritance.html">Single Table Inheritance</a> and <a href="http://www.martinfowler.com/eaaCatalog/concreteTableInheritance.html">Concrete Table Inheritance</a>, two powerful ways to map an object inheritance to a relational persistence. However, every once in a while, a Propel user pops in and asks for a Propel implementation of <a href="http://martinfowler.com/eaaCatalog/classTableInheritance.html">Class Table Inheritance</a>. This type of inheritance uses one table per class in the inheritance structure ; each table stores only the columns it doesn't inherits from its parent.<!--more--></p>
<p>For example, a sports news website displays statistics about various sports player. The Class Table Inheritance patterns translates that to a <code>player</code> table storing the identity, and two "children" tables, <code>footballer</code> and <code>basketballer</code>, with distinct statistics columns.</p>
<div class="CodeRay">
  <div class="code"><pre>player
-------
first_name
last_name

footballer
------------
goals_scored
fouls_committed

basketballer
------------
points
field_goals</pre></div>
</div>

<h3>Implementing Class Table Inheritance via Joins</h3>
<p>I have always thought that Class Table Inheritance isn't really inheritance. Actually, it is usually achieved using joins, by defining a foreign key in the children tables to the parent table, as follows:</p>
<div class="CodeRay">
  <div class="code"><pre>player
-------
id
first_name
last_name

footballer
------------
id
goals_scored
fouls_committed
player_id       // foreign key to player.id

basketballer
------------
id
points
field_goals
three_points_field_goals
player_id       // foreign key to player.id</pre></div>
</div>

<p>So to create a basketballer with an identity, relate a <code>Basketballer</code> to a <code>Player</code> the usual Propel way:</p>
<div class="CodeRay">
  <div class="code"><pre>// create a Basketballer
basketballer = new Basketballer();
$basketballer-&gt;setPoints(101);
$basketballer-&gt;setFieldGoals(47);
$basketballer-&gt;setThreePointsFieldGoals(7);
// create a Player
$player = new Player();
$player-&gt;setFirstName('Michael');
$player-&gt;setLastName('Giordano');
// relate the two objects
$basketballer-&gt;setPlayer($player);
// save the two objects
$basketballer-&gt;save();</pre></div>
</div>

<h3>The Delegation Pattern</h3>
<p>But this isn't inheritance. What the user expects, with the inheritance concept in mind, is to deal only with a <code>Basketballer</code> instance to manage both the identity and the stats, as follows:</p>
<div class="CodeRay">
  <div class="code"><pre>$basketballer = new Basketballer();
$basketballer-&gt;setPoints(101);
$basketballer-&gt;setFieldGoals(47);
$basketballer-&gt;setThreePointsFieldGoals(7);
// use inheritance to hide join
$basketballer-&gt;setFirstName('Michael');
$basketballer-&gt;setLastName('Giordano');
// save basketballer and player
$basketballer-&gt;save();</pre></div>
</div>

<p>Even if the two pieces of code would produce the same result (one <code>basketballer</code> record and one <code>player</code> record), the second one is more object-oriented.</p>
<p>But is it possible to achieve that using the PHP inheritance system? Not really, because the user wants the name information to be store in the <code>player</code> table, not in the <code>basketballer</code> table (otherwise Concrete Table Inheritance would be a better fit). As a matter of fact, the <code>Basketballer</code> object needs the <code>Player</code> object to handle the first name and last name for him. In object-oriented design, this is called "delegation". It's a very common design pattern, for example in Objective-C, where it is used extensively.</p>
<p>In PHP, a usual implementation of the delegation pattern is via the <code>__call()</code> magic method. So in order to make the previous code snippet work, all that's needed is the following code:</p>
<div class="CodeRay">
  <div class="code"><pre>class Basketballer extends BaseBasketballer
{
  /**
   * Delegating not found methods to the related Player
   */
  public function __call($method, $params)
  {
    if (is_callable(array('Player', $method))) {
      if (!$delegate = $this-&gt;getPlayer()) {
        $delegate = new Player();
        $this-&gt;setPlayer($delegate);
      }
      return call_user_func_array(array($delegate, $method), $params);
    }
    return parent::__call($method, $params);
  }
}</pre></div>
</div>

<p>And here you go, a <code>Basketballer</code> can reply to the <code>Player</code> method calls, and hide the join used to implement class table inheritance. For the end user, everything happens as if <code>Basketballer</code> actually extended <code>Player</code>, but the <code>Player</code> data is stored in a separate table.</p>
<h3>Introducing the <code>delegate</code> behavior</h3>
<p>Instead of providing yet another extension system in the Propel ActiveRecord classes, I implemented a behavior, called <code>delegate</code>, which allows to delegate method calls to another model. This behavior generates exactly the <code>__call()</code> code shown above, provided you set up your schema in the following way:</p>
<div class="CodeRay">
  <div class="code"><pre>&lt;table name=&quot;player&quot;&gt;
  &lt;column name=&quot;id&quot; type=&quot;INTEGER&quot; primaryKey=&quot;true&quot; autoIncrement=&quot;true&quot;/&gt;
  &lt;column name=&quot;first_name&quot; type=&quot;VARCHAR&quot; size=&quot;100&quot;/&gt;
  &lt;column name=&quot;last_name&quot; type=&quot;VARCHAR&quot; size=&quot;100&quot;/&gt;
&lt;/table&gt;
&lt;table name=&quot;basketballer&quot;&gt;
  &lt;column name=&quot;id&quot; required=&quot;true&quot; primaryKey=&quot;true&quot; autoIncrement=&quot;true&quot; type=&quot;INTEGER&quot; /&gt;
  &lt;column name=&quot;points&quot; type=&quot;INTEGER&quot; /&gt;
  &lt;column name=&quot;field_goals&quot; type=&quot;INTEGER&quot; /&gt;
  &lt;column name=&quot;three_points_field_goals&quot; type=&quot;INTEGER&quot; /&gt;
  &lt;column name=&quot;player_id&quot; type=&quot;INTEGER&quot; /&gt;
  &lt;foreign-key foreignTable=&quot;player&quot;&gt;
    &lt;reference local=&quot;player_id&quot; foreign=&quot;id&quot; /&gt;
  &lt;/foreign-key&gt;
  &lt;behavior name=&quot;delegate&quot;&gt;
    &lt;parameter name=&quot;to&quot; value=&quot;player&quot; /&gt;
  &lt;/behavior&gt;
&lt;/table&gt;</pre></div>
</div>

<p>Rebuild the model, and the <code>Basketballer</code> can now delegate all the method calls it can't manage on its own to his related <code>Player</code>, whether such a player already exists or not.</p>
<p>The <code>delegate</code> behavior, together with complete documentation and unit tests, <a href="https://github.com/propelorm/Propel/pull/46">has landed</a> in the Propel master yesterday, and will be part of the upcoming 1.6.2 release.</p>
<p>You may think: Why should I be enthusiast about a behavior generating six lines of code in a <code>__call()</code> method? First of all, the <code>delegate</code> behavior has more features than than just simulating Class Table Inheritance. Second of all, it allows you to design your object model with delegation in mind, and that opens a lot of new possibilities.</p>
<h3>Multiple Delegation</h3>
<p>In PHP, an object can only inherit from one parent. However, delegation isn't restricted to a single class. So the <code>Basketballer</code> class can delegate to both a <code>Player</code> and an <code>Employee</code> class:</p>
<div class="CodeRay">
  <div class="code"><pre>&lt;table name=&quot;player&quot;&gt;
  &lt;column name=&quot;id&quot; type=&quot;INTEGER&quot; primaryKey=&quot;true&quot; autoIncrement=&quot;true&quot;/&gt;
  &lt;column name=&quot;first_name&quot; type=&quot;VARCHAR&quot; size=&quot;100&quot;/&gt;
  &lt;column name=&quot;last_name&quot; type=&quot;VARCHAR&quot; size=&quot;100&quot;/&gt;
&lt;/table&gt;
&lt;table name=&quot;employee&quot;&gt;
  &lt;column name=&quot;id&quot; type=&quot;INTEGER&quot; primaryKey=&quot;true&quot; autoIncrement=&quot;true&quot;/&gt;
  &lt;column name=&quot;salary&quot; type=&quot;INTEGER&quot;/&gt;
&lt;/table&gt;
&lt;table name=&quot;basketballer&quot;&gt;
  &lt;column name=&quot;id&quot; required=&quot;true&quot; primaryKey=&quot;true&quot; autoIncrement=&quot;true&quot; type=&quot;INTEGER&quot; /&gt;
  &lt;column name=&quot;points&quot; type=&quot;INTEGER&quot; /&gt;
  &lt;column name=&quot;field_goals&quot; type=&quot;INTEGER&quot; /&gt;
  &lt;column name=&quot;three_points_field_goals&quot; type=&quot;INTEGER&quot; /&gt;
  &lt;column name=&quot;player_id&quot; type=&quot;INTEGER&quot; /&gt;
  &lt;foreign-key foreignTable=&quot;player&quot;&gt;
    &lt;reference local=&quot;player_id&quot; foreign=&quot;id&quot; /&gt;
  &lt;/foreign-key&gt;
  &lt;column name=&quot;employee_id&quot; type=&quot;INTEGER&quot; /&gt;
  &lt;foreign-key foreignTable=&quot;employee&quot;&gt;
    &lt;reference local=&quot;employee_id&quot; foreign=&quot;id&quot; /&gt;
  &lt;/foreign-key&gt;
  &lt;behavior name=&quot;delegate&quot;&gt;
    &lt;parameter name=&quot;to&quot; value=&quot;player, employee&quot; /&gt;
  &lt;/behavior&gt;
&lt;/table&gt;</pre></div>
</div>

<p>Using only a <code>Basketballer</code> instance, a developer can now populate three records in three different tables:</p>
<div class="CodeRay">
  <div class="code"><pre>$basketballer = new Basketballer();
$basketballer-&gt;setPoints(101);
$basketballer-&gt;setFieldGoals(47);
$basketballer-&gt;setThreePointsFieldGoals(7);
// delegate to player
$basketballer-&gt;setFirstName('Michael');
$basketballer-&gt;setLastName('Giordano');
// delegate to employee
$basketballer-&gt;setSalary(2000000);
// save basketballer and player and employee
$basketballer-&gt;save();</pre></div>
</div>

<p>The liberty to use multiple inheritance might scare you, for it breaks one of the constraints that prevent many developers from designing horrible conceptual data models. However, it makes it possible to support Class Table Inheritance for several levels. For instance, if you modify the class hierarchy to have a <code>ProBasketballer</code> extend <code>Basketballer</code> extend <code>Player</code>, simple delegation doesn't work there. Even if <code>ProBasketballer</code> delegates to <code>Basketballer</code>, the generated <code>ProBasketballer::__call()</code> code won't be able to manage delegating all the way up to <code>Player</code>. The solution is to use multiple delegation to explicitly delegate to all ancestors, as follows:</p>
<div class="CodeRay">
  <div class="code"><pre>&lt;table name=&quot;player&quot;&gt;
  &lt;column name=&quot;id&quot; type=&quot;INTEGER&quot; primaryKey=&quot;true&quot; autoIncrement=&quot;true&quot;/&gt;
  &lt;column name=&quot;first_name&quot; type=&quot;VARCHAR&quot; size=&quot;100&quot;/&gt;
  &lt;column name=&quot;last_name&quot; type=&quot;VARCHAR&quot; size=&quot;100&quot;/&gt;
&lt;/table&gt;
&lt;table name=&quot;basketballer&quot;&gt;
  &lt;column name=&quot;id&quot; required=&quot;true&quot; primaryKey=&quot;true&quot; autoIncrement=&quot;true&quot; type=&quot;INTEGER&quot; /&gt;
  &lt;column name=&quot;points&quot; type=&quot;INTEGER&quot; /&gt;
  &lt;column name=&quot;field_goals&quot; type=&quot;INTEGER&quot; /&gt;
  &lt;column name=&quot;three_points_field_goals&quot; type=&quot;INTEGER&quot; /&gt;
  &lt;column name=&quot;player_id&quot; type=&quot;INTEGER&quot; /&gt;
  &lt;foreign-key foreignTable=&quot;player&quot;&gt;
    &lt;reference local=&quot;player_id&quot; foreign=&quot;id&quot; /&gt;
  &lt;/foreign-key&gt;
  &lt;behavior name=&quot;delegate&quot;&gt;
    &lt;parameter name=&quot;to&quot; value=&quot;player&quot; /&gt;
  &lt;/behavior&gt;
&lt;/table&gt;
&lt;table name=&quot;pro_basketballer&quot;&gt;
  &lt;column name=&quot;id&quot; required=&quot;true&quot; primaryKey=&quot;true&quot; autoIncrement=&quot;true&quot; type=&quot;INTEGER&quot; /&gt;
  &lt;column name=&quot;salary&quot; type=&quot;INTEGER&quot; /&gt;
  &lt;column name=&quot;basketballer_id&quot; type=&quot;INTEGER&quot; /&gt;
  &lt;foreign-key foreignTable=&quot;basketballer&quot;&gt;
    &lt;reference local=&quot;basketballer_id&quot; foreign=&quot;id&quot; /&gt;
  &lt;/foreign-key&gt;
  &lt;column name=&quot;player_id&quot; type=&quot;INTEGER&quot; /&gt;
  &lt;foreign-key foreignTable=&quot;player&quot;&gt;
    &lt;reference local=&quot;player_id&quot; foreign=&quot;id&quot; /&gt;
  &lt;/foreign-key&gt;
  &lt;behavior name=&quot;delegate&quot;&gt;
    &lt;parameter name=&quot;to&quot; value=&quot;basketballer, player&quot; /&gt;
  &lt;/behavior&gt;
&lt;/table&gt;</pre></div>
</div>

<p>Now a <code>ProBasketballer</code> can have a salary, while a simple <code>Basketballer</code> can't.</p>
<h3>Delegating The Other Way Around</h3>
<p>In all the examples shown previously, the foreign key supporting the delegation relation was located in the table that was actually delegating. This is because the main table (<code>basketballer</code> in the example) must have only one delegate in the other table (<code>player</code> in the example). The model must show a many-to-one relationship, and that places the foreign key in the delegating table.</p>
<div class="CodeRay">
  <div class="code"><pre>player
-------
id
first_name
last_name

basketballer    // delegates to player
------------
id
points
field_goals
three_points_field_goals
player_id       // foreign key to player.id</pre></div>
</div>

<p>But there is another way to have only one related record. Instead of using a many-to-one relationship, one could use a one-to-one relationship. In Propel, this is achieved by setting a foreign key which is also a primary key. So the <code>player_id</code> column can be removed, and the foreign key be placed on the <code>basketballer</code> primary key.</p>
<div class="CodeRay">
  <div class="code"><pre>player
-------
id
first_name
last_name

basketballer   // delegates to player
------------
id             // foreign key to player.id
points
field_goals
three_points_field_goals</pre></div>
</div>

<p>Since this kind of model is also suitable for delegation, the <code>delegate</code> behavior has been designed to supports one-to-one relationships as well.</p>
<p>One-to-one relationships are reversible. That means that the foreign key could be placed in the other table. For the player/basketballer model, that would mean:</p>
<div class="CodeRay">
  <div class="code"><pre>player
-------
id             // foreign key to basketballer.id
first_name
last_name

basketballer   // delegates to player
------------
id
points
field_goals
three_points_field_goals</pre></div>
</div>

<p>This is still supported by the behavior. But such a setup creates one constraint: a player can't have both <code>basketballer</code> and <code>footballer</code> stats anymore. In this case, it's not such a good idea. But think about this other use case:</p>
<div class="CodeRay">
  <div class="code"><pre>user_profile
------------
id             // foreign key to user.id
first_name
last_name
email
telephone

user           // delegates to user_profile
-------
id
login
password</pre></div>
</div>

<p>This schema may sound familiar to users of the <code>sfGuardPlugin</code> for the symfony framework. In this plugin, the <code>User</code> class handles only the basic identification data for a user. All the other information, like email address or full identity, is "delegated" to another class, the <code>UserProfile</code>. It is <em>not</em> a use case for Single Table Inheritance, but it's a great one for delegation.</p>
<p>Using the <code>delegate</code> behavior, Propel can now give access to the profile information directly from the user class:</p>
<div class="CodeRay">
  <div class="code"><pre>&lt;table name=&quot;user&quot;&gt;
  &lt;column name=&quot;id&quot; type=&quot;INTEGER&quot; primaryKey=&quot;true&quot; autoIncrement=&quot;true&quot;/&gt;
  &lt;column name=&quot;login&quot; type=&quot;VARCHAR&quot; size=&quot;100&quot;/&gt;
  &lt;column name=&quot;password&quot; type=&quot;VARCHAR&quot; size=&quot;100&quot;/&gt;
  &lt;behavior name=&quot;delegate&quot;&gt;
    &lt;parameter name=&quot;to&quot; value=&quot;user_profile&quot; /&gt;
  &lt;/behavior&gt;
&lt;/table&gt;
&lt;table name=&quot;user_profile&quot;&gt;
  &lt;column name=&quot;id&quot; type=&quot;INTEGER&quot; primaryKey=&quot;true&quot;/&gt;
  &lt;column name=&quot;first_name&quot; type=&quot;VARCHAR&quot; size=&quot;100&quot;/&gt;
  &lt;column name=&quot;last_name&quot; type=&quot;VARCHAR&quot; size=&quot;100&quot;/&gt;
  &lt;column name=&quot;email&quot; type=&quot;VARCHAR&quot; size=&quot;100&quot;/&gt;
  &lt;column name=&quot;telephone&quot; type=&quot;VARCHAR&quot; size=&quot;100&quot;/&gt;
  &lt;foreign-key foreignTable=&quot;user&quot;&gt;
    &lt;reference local=&quot;id&quot; foreign=&quot;id&quot; /&gt;
  &lt;/foreign-key&gt;
&lt;/table&gt;</pre></div>
</div>

<p>In PHP, the developer can now write:</p>
<div class="CodeRay">
  <div class="code"><pre>$user = new User();
$user-&gt;setLogin('francois');
$user-&gt;setPassword('S€cr3t');
// Fill the profile via delegation
$user-&gt;setEmail('francois@example.com');
$user-&gt;setTelephone('202-555-9355');
// save the user and its profile
$user-&gt;save();</pre></div>
</div>

<p>This is why the concept of delegation is more powerful than Class Table Inheritance. There are a lot of use cases that delegation solves, without even being designed to do so. And this is why the introduction of the <code>delegate</code> behavior in Propel is such a great news.</p>
