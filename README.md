# Propel 

 * Propel 1.x is no longer maintained by the upstream project. 
 * You probably shouldn't start a new project using Propel 1.x.
 * This repo has a handful of patches applied to get unit tests to pass (e.g. with PHP 7.2), and sufficient phpdoc fixes to allow psalm (http://github.com/vimeo/psalm) to work on generated code.
 

# Changes in this fork ....

 - Token unit test fixes to stop Travis failing ( https://github.com/DavidGoodwin/Propel/commit/2d238acd530f4fcdb0bce59bb41e1c6596766ff6 )
 - Fix PropelArrayFormatter to work correctly within PHP 7.x ( https://github.com/DavidGoodwin/Propel/commit/dea4da1949534cd4ce6d79f774796dd55b4ff6dc ) 
 - Fix SQL injection in limit/offset ( https://github.com/propelorm/Propel/pull/1054 ) (also included in upstream version 1.7.2)
 - Fix count() for PHP 7.2 ( https://github.com/propelorm/Propel/pull/1050 )
 - Doc block fixes ( https://github.com/propelorm/Propel/pull/1011 )
 - Doc block fixes ( https://github.com/propelorm/Propel/pull/992 )
 - Doc block fixes ( https://github.com/propelorm/Propel/pull/998/ )
 - Further Doc block fixes to allow psalm 3.5.x to pass on projects using Propel 1.7.x.
 - Merge of https://github.com/propelorm/Propel/pull/1086 (various PHP 7.4 fixes)

## To use this fork

```
composer require palepurple/propel1
```


# About 

Propel is an open-source Object-Relational Mapping (ORM) for PHP5.

[![Build Status](https://secure.travis-ci.org/DavidGoodwin/Propel.png?branch=master)](http://travis-ci.org/DavidGoodwin/Propel)

## A quick tour of the features ##

Propel has some nice features you should know about:

 - It's a fast and easy way to manage your database;
 - It provides command line tools for generating code (well documented with an IDE-friendly syntax);
 - It's very flexible: you can simply extend Propel;
 - It uses PDO (PHP Data Objects) so it allows you to use the RDBMS of your choice (MySQL, SQLite, PostgreSQL, Oracle and MSSQL are supported);
 - Propel is an open-source project which is [well documented](http://propelorm.org/Propel/documentation/).

## Installation ##

Read the [Propel documentation](http://propelorm.org/Propel/).


## License ##

Propel is an open-source project released under the MIT license. See the `LICENSE` file for more information.
