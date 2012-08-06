---
layout: documentation
title:  Testing
---

# Testing #

To build better and more reliable applications, you should test your code using
both functional and unit tests. First, read the [Testing chapter](
http://symfony.com/doc/current/book/testing.html) on the Symfony2 documentation.
It explains everything you need to know to write tests for your Symfony2
application. However, it doesn't explain how to configure Propel, or how to use
it in your test classes.

This recipe introduces Propel in the wonderful testing world.
If you are reading it, you are probably interested in **functional tests** as
relying on a database means you write functional tests, not unit tests.

Symfony2 provides a
[WebTestCase](https://github.com/symfony/symfony/blob/master/src/Symfony/Bundle/FrameworkBundle/Test/WebTestCase.php)
which provides great features for your functional test classes. This is the
class you need when you want to do black box testing. Then again, this is
explained in the [Testing chapter - Functional
tests](http://symfony.com/doc/current/book/testing.html#functional-tests).
Moreover, Symfony2 comes with multiple environments, like `dev`, `prod` but
also `test`. The Symfony2 Client, detailled in the section [Working with the
Test Client](http://symfony.com/doc/current/book/testing.html#working-with-the-test-client)
in the Symfony2 documentation, relies on this `test` environment.


## The Test Environment ##

The `config_test.yml` file is where you have to put specific configuration for
testing purpose. For example, you can setup a new database for your tests like
`yourdatabase_test`:

{% highlight yaml %}
# app/config/config_test.yml
propel:
    dbal:
        dsn: %database_driver%:host=%database_host%;dbname=%database_name%_test;charset=UTF8
{% endhighlight %}

You can also configure a `SQLite` connection instead of your production database
vendor (`MySQL`, `PostgreSQL`, etc.). It's a good idea to use a different
database vendor to ensure your application is not tied to a specific database,
however sometimes it's not possible.

As you may know, Propel uses smart code generation depending on the database
vendor, and so on. You need to build both SQL and PHP code before to run your
tests. It's doable by running a single command line, but it's not optimal.


## The Propel WebTestCase ##

A good idea is to extend the `WebTestCase` class in your application, and to add
a few methods to run commands for you:

{% highlight php %}
<?php

namespace Acme\DemoBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as BaseWebTestCase;

class WebTestCase extends BaseWebTestCase
{
    private static $application;

    public static function setUpBeforeClass()
    {
        \Propel::disableInstancePooling();

        self::runCommand('propel:build --insert-sql');
    }

    protected static function getApplication()
    {
        if (null === self::$application) {
            $client = static::createClient();

            self::$application = new \Symfony\Bundle\FrameworkBundle\Console\Application($client->getKernel());
            self::$application->setAutoExit(false);
        }

        return self::$application;
    }

    protected static function runCommand($command)
    {
        $command = sprintf('%s --quiet', $command);

        return self::getApplication()->run(new \Symfony\Component\Console\Input\StringInput($command));
    }
}
{% endhighlight %}

Basically, for each test class, it will build everthing before to execute test
methods. By using this class, you just need to run `phpunit` in your project to
run all tests, including functional tests that rely on a database. In other
words, it's setup your application in `test` environment, just like you would do
in production.

{% highlight php%}
<?php

namespace Acme\DemoBundle\Tests\Controller;

class DefaultControllerTest extends WebTestCase
{
    // Your tests
}
{% endhighlight %}

You can run more commands, like the `propel:fixtures:load` command. It's up to
you. You now have all keys to automatically run functional tests with Propel
inside.

{% highlight php %}
<?php

self::runCommand('propel:fixtures:load @AcmeDemoBundle --yml');
{% endhighlight %}

If you want to write unit tests for your Model classes for some reasons, you can
follow the same principle in your own `TestCase` class.


## The Propel TestCase ##

If you don't use the Symfony2 Client, you don't need to extend the `WebTestCase`
class, just write your own `TestCase` class:

{% highlight php %}
<?php

namespace Acme\DemoBundle\Tests;

require_once __DIR__ . '/../../../../app/AppKernel.php';

class TestCase extends \PHPUnit_Framework_TestCase
{
    private static $application;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        if (null === self::$application) {
            self::runCommand('propel:build --insert-sql');
        }
    }

    protected static function getApplication()
    {
        if (null === self::$application) {
            $kernel = new \AppKernel('test', true);
            $kernel->boot();

            self::$application = new \Symfony\Bundle\FrameworkBundle\Console\Application($kernel);
            self::$application->setAutoExit(false);
        }

        return self::$application;
    }

    protected static function runCommand($command)
    {
        $command = sprintf('%s --quiet', $command);

        return self::getApplication()->run(new \Symfony\Component\Console\Input\StringInput($command));
    }
}
{% endhighlight %}

Having both `WebTestCase` and `TestCase` classes allow you to write Propel aware
tests in your application. You don't need anything else. You can read the [PHPUnit
documentation](http://www.phpunit.de/manual/current/en/) for more information on
assertions.


## Travis-CI ##

Once you have a decent test suite, you may want to use
[Travis-CI](http://travis-ci.org). Here is a standard configuration for Symfony2
projects:

{% highlight yaml %}
language: php

php:
    - 5.3
    - 5.4

before_script:
    - curl -s http://getcomposer.org/installer | php -- --quiet
    - php composer.phar install
    - php app/console propel:database:create --env=test

script: phpunit
{% endhighlight %}
