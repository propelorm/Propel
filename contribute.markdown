---
layout: default
title: How To Contribute ?
---

# How To Contribute ? #

You can easily contribute to the Propel project since all projects are hosted by [Github](http://www.github.com).
You just have to _fork_ the Propel project on the [PropelORM organization](http://www.github.com/propelorm) and
to provide Pull Requests or to submit issues. Note, we are using [Git](http://git-scm.com) as main Source Code Management.

The Propel organization maintains three projects:

* [Propel](http://www.github.com/propelorm/Propel) : the main project.
* [PropelBundle](http://www.github.com/propelorm/PropelBundle) : a bundle to integrate Propel with [Symfony2](http://www.symfony.com).
* [sfPropelORMPlugin](http://www.github.com/propelorm/sfPropelORMPlugin) : a plugin to integrate Propel with [symfony 1.x](http://www.symfony-project.org);

## Make a Pull Request ##

The best way to submit a patch is to make a Pull Request on Github. First, you should create a new branch from the `master`.
Assuming you are in your local Propel project:

    git checkout -b master fix-my-patch

Now you can write your patch in this branch. Don't forget to provide unit tests with your fix to prove both the bug and the patch.
It will ease the process to accept or refuse a Pull Request.

When you're done, you have to rebase your branch to provide a clean and safe Pull Request.

    git checkout master

    git pull --ff-only upstream master

    git checkout fix-my-patch

    git rebase master

In this example, the `upstream` remote is the PropelORM organization repository.

Once done, you can submit the Pull Request by pushing your branch to your fork:

    git push origin fix-my-patch

Go to the www.github.com and press the _Pull Request_ button. Add a short description to this Pull Request and submit it.

## Submit an issue ##

The ticketing system is also hosted on Github:

* Propel: [https://github.com/propelorm/Propel/issues](https://github.com/propelorm/Propel/issues)
* PropelBundle: [https://github.com/propelorm/PropelBundle/issues](https://github.com/propelorm/PropelBundle/issues)
* sfPropelORMPlugin: [https://github.com/propelorm/sfPropelORMPlugin/issues](https://github.com/propelorm/sfPropelORMPlugin/issues)
