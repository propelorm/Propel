---
layout: post
title: Behavior driven development
published: true
---
<p>You may know that Propel allows to define behaviors. Up to recently, I used to think behaviors just added cool features to my models. It took me time to realize that in fact, behaviors allow my models to have a uniform API. In this article, I'll expose how to define <em>application scoped behaviors</em>, but also how you should extend existing behaviors.</p>
<h3>Extending existing behaviors</h3>
<p><a href="http://www.propelorm.org/behaviors/versionable.html" title="The versionable behavior documentation">Versionable </a>is definitely a cool feature. It's easy to use, quite transparent, and it does its job : keep track of the entity modifications. But how can you keep track of who made the changes? Should you set the <code>created_by</code> fields in every place your application saves an object? And what about the behavior configuration? Should you repeat it in every table?</p>
<p>The answer is obviously no, remember the <a href="http://en.wikipedia.org/wiki/Don't_repeat_yourself">DRY principle</a>. So, how can you centralise this code and configuration in a better place than in <code>MyApplicationVersionableBehavior</code>?</p>
<p>Extend the original behavior, override the&nbsp;<code>parameters</code> property, define a <code>preSave()</code> method retrieving the current user, and from now on just focus on your business logic!</p>
<h3>Creating application behaviors</h3>
<p>If you use an ORM and a framework, it usualy means that your application requires a specific business logic. Most of the time you may feel like repeating yourself in different models, for pieces of logic concerning resource access policy, object image...</p>
<p>These are perfect candidates to become behaviors, as the API will be unified for every objects. The good thing is that logic is coded once, tested once, and available everywhere...</p>
<p>Oh, and if your behavior can be shared, just do it, it might avoid people from reinventing the wheel. Who knows, if there are enough shared behaviors, maybe Propel2 will provide a way to simply manage external behaviors!</p>
