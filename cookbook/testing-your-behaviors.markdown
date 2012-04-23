---
layout: documentation
title: Testing Your Behaviors
---

# Testing Your Behaviors #

Once you wrote a behavior following [this documentation](writing-behavior.html), you have to
unit test it. This page explains how to do that.


## Writing a Test class ##

The first step to test your behavior is to write a Test class using your favorite testing framework.
This section will use [PHPUnit](www.phpunit.de) but you can easily reproduce the same steps with another
framework.

Assuming you wrote a `MyAwesomeBehavior` behavior which probably does amazing stuffs:

    MyAwesomeBehavior
        |_ src/
        |   \_ MyAwesomeBehavior.php
        |
        |_ tests/
            \_ MyAwesomeBehaviorTest.php


The `MyAwesomeBehaviorTest` class looks like the following one:

{% highlight php %}
<?php

class MyAwesomeBehaviorTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        if (!class_exists('MyObject')) {
            $schema = <<<EOF
<database name="bookstore" defaultIdMethod="native">
    <table name="my_object">
        ...
        <behavior name="my_awesome" />
    </table>
</database>
EOF;
            $builder = new PropelQuickBuilder();
            $config  = $builder->getConfig();
            $config->setBuildProperty('behavior.my_awesome.class', __DIR__.'/../src/MyAwesomeBehavior');
            $builder->setConfig($config);
            $builder->setSchema($schema);
            $con = $builder->build();
        }
    }
}
{% endhighlight %}

We rely on the [PropelQuickBuilder](https://github.com/propelorm/Propel/blob/master/generator/lib/util/PropelQuickBuilder.php)
to generated all classes in memory. It's a convenient way to test some parts of Propel without using fixtures files.

Write a XML schema as usual, and use your new behavior in it. Now, take care of the line below. It's how you will register
your new behavior in the `PropelQuickBuilder`:

{% highlight php %}
<?php

$config->setBuildProperty('behavior.my_awesome.class', __DIR__.'/../src/MyAwesomeBehavior');
{% endhighlight %}


The `PropelQuickBuilder` comes with a **SQLite** database. That means you can execute database queries in your tests.
Did you notice the `$con` variable? It can be useful to add some logic to the database connection in use:

{% highlight php %}
<?php

// Register a 'ACOS' SQL function as SQLite doesn't provide it by default
$con->sqliteCreateFunction('ACOS', 'acos', 1);
{% endhighlight %}

You can now use your in memory classes in your test methods. It will just work.


## Using Composer ##

[Composer](http://getcomposer.org) is designed to manage your PHP dependencies without any effort. It's a pretty
nice way to share your behavior with other people, or just to require it in your project.

A basic configuration looks like:

{% highlight javascript %}
{
    "name": "willdurand/propel-myawesome-behavior",
    "description": "A nice description.",
    "keywords": [ "propel", "behavior" ],
    "license": "MIT",
    "authors": [
        {
            "name": "William DURAND",
            "email": "william.durand1@gmail.com"
        }
    ],
    "require": {
        "propel/propel1": "1.6.*"
    },
    "autoload": {
        "classmap": ["src/"]
    }
}
{% endhighlight %}


>**Note**<br />The convention is to prefix your package name with propel-, and to suffix it with -behavior.


## Configuring PHPUnit ##

If you run the command below, Composer will setup your behavior to run the test suite:

    php composer.phar install --dev

Now, you have to configure your project for PHPUnit. It's really easy. Start by copying the following `phpunit.xml.dist` file:

{% highlight xml %}
<?xml version="1.0" encoding="UTF-8"?>
<phpunit backupGlobals="false"
    backupStaticAttributes="false"
    colors="true"
    convertErrorsToExceptions="true"
    convertNoticesToExceptions="true"
    convertWarningsToExceptions="true"
    processIsolation="false"
    stopOnFailure="false"
    syntaxCheck="false"
    bootstrap="tests/bootstrap.php"
    >
    <testsuites>
        <testsuite name="MyAwesomeBehavior Test Suite">
            <directory>./tests/</directory>
        </testsuite>
    </testsuites>
    <filter>
        <whitelist>
            <directory>./src/</directory>
        </whitelist>
    </filter>
</phpunit>
{% endhighlight %}

Now, create the `tests/bootstrap.php` file:

{% highlight php %}
<?php

require_once __DIR__ . '/../vendor/autoload.php';
set_include_path(__DIR__ . '/../vendor/phing/phing/classes' . PATH_SEPARATOR . get_include_path());

require_once __DIR__ . '/../vendor/propel/propel1/generator/lib/util/PropelQuickBuilder.php';
{% endhighlight %}


That's all! Now, you just have to run the `phpunit` command, and it will launch your test suite.


## Add your behavior to Travis-ci ##

[Travis-ci](http://travis-ci.org/) is a distributed build platform for the open source community.
If you want to add your behavior to Travis-ci, you can use the following `.travis.yml` file:

{% highlight yaml %}
language: php

php:
  - 5.3.2
  - 5.3
  - 5.4

before_script:
    - curl -s http://getcomposer.org/installer | php
    - php composer.phar --dev install

script: phpunit --coverage-text
{% endhighlight %}
