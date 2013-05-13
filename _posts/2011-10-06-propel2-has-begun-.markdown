---
layout: post
title: Propel2 has begun!
published: true
---

Hi there,

Yesterday we held an IRC meeting to talk about the future of Propel. We focused on the next major evolution, named <strong>Propel2</strong>&nbsp;to emphasize on the internal refactoring, and because we want to be free to break backward compatibility to get things right.

Hey! Propel2 ? What's new about it ? Will I have to learn it all again ?&nbsp;Don't worry, current Query and Active record API are safe, this is the reason Propel is so great so be sure it will be for long.

And, contrary to what was announced here, Propel2 won't be built on top of Doctrine2. I'll let you read the IRC transcript below to know why.

<!--more-->

Actually, Propel2 will be based on the 1.6.3 version. To make it up to date with today's standards, the buildtime and runtime classes will use namespaces. That will open the door for a new autoloader (probably the Symfony2 ClassLoader Component). That means Propel2 will run on PHP version &gt; 5.3. We'll add new exceptions to be more explicit (there are only two named exceptions for now). We'll remove the current Phing dependency for command line tasks and replace with another, more modern Command line component (probably the Symfony2 Console component). We'll get rid of the Peer classes; to unclutter your model directories. We also discussed about the logging part and Monolog will probably be used. To ease the communication with more RDBMS we'll introduce adapters instead of extending PDO for connections. And the last part was about builders: today we use PHP scripts to build PHP classes, and it's a pain ; we proposed to use Twig instead and it was accepted by the community - like all other points.

That means the upcoming Propel 1.6.3 version is the last version on the 1.x branch. We'll just maintain it by merging bugfixes. Propel 1.6 is full-featured, stable, and used since a long time by a lot of people. We recently improved its speed and provided an API to deal with collections.

<script src="https://gist.github.com/1266792.js?file=Propel%20IRC%20Meeting"></script>

The development of Propel2 will be held on <a href="https://github.com/propelorm/Propel2">GitHub</a>,
you'll find the roadmap <a href="https://github.com/propelorm/Propel2/issues/1">here</a>
and any help is welcome. If you want to contribute on this huge refactoring, just come
and give a hand :)
