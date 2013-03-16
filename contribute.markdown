---
layout: default
title: How To Contribute ?
---

# How To Contribute ? #

You can easily contribute to the Propel project since all projects are hosted by [GitHub](https://github.com).
You just have to _fork_ the Propel project on the [PropelORM organization](https://github.com/propelorm) and
to provide Pull Requests or to submit issues. Note, we are using [Git](http://git-scm.com) as main Source Code Management.

The Propel organization maintains four projects:

* [Propel](http://github.com/propelorm/Propel) : the main project.
* [PropelBundle](http://github.com/propelorm/PropelBundle) : a bundle to integrate Propel with [Symfony2](http://www.symfony.com).
* [sfPropelORMPlugin](http://github.com/propelorm/sfPropelORMPlugin) : a plugin to integrate Propel with [symfony 1.x](http://www.symfony-project.org);
* [propelorm.github.com](https://github.com/propelorm/propelorm.github.com) : the Propel documentation (aka this website).

## Submit an issue ##

The ticketing system is also hosted on GitHub:

* Propel: [https://github.com/propelorm/Propel/issues](https://github.com/propelorm/Propel/issues)
* PropelBundle: [https://github.com/propelorm/PropelBundle/issues](https://github.com/propelorm/PropelBundle/issues)
* sfPropelORMPlugin: [https://github.com/propelorm/sfPropelORMPlugin/issues](https://github.com/propelorm/sfPropelORMPlugin/issues)

## Make a Pull Request ##

The best way to submit a patch is to make a Pull Request on GitHub. First, you should create a new branch from the `master`.
Assuming you are in your local Propel project:

```bash
$ git checkout -b fix-my-patch master
```

Now you can write your patch in this branch. Don't forget to provide unit tests with your fix to prove both the bug and the patch.
It will ease the process to accept or refuse a Pull Request.

When you're done, you have to rebase your branch to provide a clean and safe Pull Request.

```bash
$ git checkout master
$ git pull --ff-only upstream master
$ git checkout fix-my-patch
$ git rebase master
```

In this example, the `upstream` remote is the PropelORM organization repository.

Once done, you can submit the Pull Request by pushing your branch to your fork:

```bash
$ git push origin fix-my-patch
```bash

Go to www.github.com and press the _Pull Request_ button. Add a short description to this Pull Request and submit it.

## Running Unit Tests ##

See [Working with unit tests](../cookbook/working-with-unit-tests.html) How to setup propel's required environment and use PHPUnit.


## Improve the documentation ##

The Propel documentation is written in [Markdown](http://daringfireball.net/projects/markdown/) syntax and runs through [GitHub Pages](http://pages.github.com/). Everybody can contribute to the documentation by forking the [propelorm.github.com](https://github.com/propelorm/propelorm.github.com) project and to submit Pull Requests.
