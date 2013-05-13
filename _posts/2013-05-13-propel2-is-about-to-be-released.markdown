---
layout: post
published: true
author: William Durand
title: Propel2 Is About To Be Released
---

One year and a half ago, [Propel2 began](/blog/2011/10/06/propel2-has-begun-.html).
We decided to refactor the whole Propel code base in order to remove BC hacks, and
introduce new features. We, the community and the Propel core developers, wrote
a **roadmap** with all key points:

<!-- more -->

* Removing all `require()` and adding namespaces, that means the directory
structure will be modified to follow the **PSR-0** specification;
* Adding an **autoloader** component, probably the Symfony2 ClassLoader
Component. Note: Composer has been used;
* Fixing CS;
* Fixing naming;
* **Removing Phing** and related stuffs;
* **Removing** late-static-binding **hacks**;
* Adding a new component to handle the **console logic**: Symfony2 Console
Component is suitable for that part
([#100](https://github.com/propelorm/Propel2/pull/100));
* **Introducing a commons logic** (useful for shared information between the
Platform (buildtime) and Adapters (runtime));
* **Refactoring Adapters** to be more generic (Proxy connection or something
else) ([#33](https://github.com/propelorm/Propel2/pull/33),
[#39](https://github.com/propelorm/Propel2/pull/39),
[#47](https://github.com/propelorm/Propel2/pull/47));
* Adding new named exceptions
([#90](https://github.com/propelorm/Propel2/pull/90));
* Adding a new (or real) logging part: probably **Monolog**
([#101](https://github.com/propelorm/Propel2/pull/101));
* **Removing PEER classes**
([#359](https://github.com/propelorm/Propel2/pull/359) and a lot of commits);
* Moved `Base*` classes to a better location, with a better name
(`Om/BaseBook.php` => `Base/Book.php` for instance)
([#175](https://github.com/propelorm/Propel2/pull/175));
* Removing the old validation system, and use a behavior to **integrate the
Symfony2 Validator component**
([#96](https://github.com/propelorm/Propel2/pull/96),
[#156](https://github.com/propelorm/Propel2/pull/156),
[#227](https://github.com/propelorm/Propel2/pull/227)).

That was the plan I announced at [Symfony Live
2012](/blog/2012/07/09/propel2-what-why-when.html). In the meanwhile, we changed
our mind, and decided to [embrace **PHP
5.4**](/blog/2012/08/08/propel2-and-php-5-4-here-we-go-.html).
But we actually did a lot more, closing [more than 300
issues](https://github.com/propelorm/Propel2/issues?milestone=2&page=1&state=closed).

First of all, the **Connection part** has [been
rewritten](https://github.com/propelorm/Propel2/pull/39), introducing a [new **Profiler
logic**](https://github.com/propelorm/Propel2/pull/83), allowing not only
_PDO_ adapters, and adding new abstraction layers such as a
[`DataFetcher`](https://github.com/propelorm/Propel2/blob/master/src/Propel/Runtime/DataFetcher/DataFetcherInterface.php).

Then, we wrote more [unit
tests](https://github.com/propelorm/Propel2/pull/223) to make Propel more
stable. We also adopted the [Symfony2 Filesystem
component](https://github.com/propelorm/Propel2/pull/295), and contributed to
the Symfony2 framework by creating this standalone component. Propel2 relies on
**five Symfony2 components**:
[Yaml](http://symfony.com/doc/current/components/yaml/introduction.html),
[Console](http://symfony.com/doc/current/components/console/introduction.html),
[Finder](http://symfony.com/doc/current/components/finder.html), Validator, and
[Filesystem](http://symfony.com/doc/current/components/filesystem.html).

We introduced some
[**traits**](https://github.com/propelorm/Propel2/commit/0a96ef65e3282e8036f3e896a3da12645eb215bf)
as well as a [**Service
Container**](https://github.com/propelorm/Propel2/commit/87f343190ec3a70174bce5608e9724696e2870b9).
However, that one is not configurable yet, see it as a compiled service
container. It was our first step to decouple the Propel code base.

We also took care of all patches applied to Propel 1.6, and ported them to
Propel2.

Last but not least, Propel2 is **PSR-0**, **PSR-1**, **PSR-2**, and
[**PSR-3**](https://github.com/propelorm/Propel2/commit/24b0e35c2fcf8ce7885e42857577c40e63afafbe) compliant.

We still have a few things to ship before a first **alpha release** like a
[Transaction API](https://github.com/propelorm/Propel2/issues/368), a [new
Pager](https://github.com/propelorm/Propel2/issues/208), and [some other things to clean
up](https://github.com/propelorm/Propel2/issues?milestone=2&state=open). This
first release is scheduled for the **1st of June**. Then, we will ship a
[**beta** version](https://github.com/propelorm/Propel2/issues?milestone=3),
probably in two months. Depending on users feedback, we will be able to release
a first **stable version** in September.

By now, our most important work in progress is [the
documentation](https://github.com/propelorm/Propel2/issues/187) we want to
reorganize. By the way, [Robin Dupret](https://github.com/robin850) is our new
documentation lead for Propel2.

I could not imagine how complicated it was to refactor such a project, we did a
lot but we could be even better, especially now that we have a cleaner code base.
I would like to apologize for the delay, it took more time than I thought. Rome
wasn't built in a day, they said.

My final thoughts go out to [all Propel
contributors](https://github.com/propelorm/Propel2/contributors), you are really
awesome, thank you!
