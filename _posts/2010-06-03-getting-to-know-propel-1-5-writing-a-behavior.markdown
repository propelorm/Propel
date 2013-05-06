---
layout: post
title: ! 'Getting To Know Propel 1.5: Writing A Behavior'
published: true
---
<p>A <a href="http://propel.posterous.com/getting-to-know-propel-15-keeping-an-aggregat">recent post</a> published in this very blog illustrated the power of the hooks offered by the ActiveRecord and Query classes. In this previous post, an aggregate column on a `PollQuestion` class was kept up to date every time a related `PollAnswer` object was saved, edited, or deleted. Now it's time to make this code really reusable across models, and the best way to do so is to move the code from the model to a behavior.</p>
<p><a href="http://www.propelorm.org/wiki/Documentation/1.5/Writing-Behavior" title="How to Write a Behavior">Read the rest of this post &gt;&gt;</a></p>
