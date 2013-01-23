---
layout: documentation
title: Working With Unit Tests in PHPUnit
---

# Working With Unit Tests #

Propel's test environment has a couple of requirements which should be setup before starting the PHPUnit.

## Setup the environment ##

### PHP ###

Install following php modules:

    php5-mysql
    php5-sqlite
    php5-iconv

Increase the `memory_limit` in your php.ini to something very high:

    memory_limit = 512M

Setup a default timezone in your php.ini:

    date.timezone = Europe/Berlin


### MySQL ###

Per default propels test suite uses the username `root` without a password. This is the default setting on most
platforms after installing MySQL.

Create following databases:

    CREATE DATABASE test;
    CREATE SCHEMA bookstore_schemas;
    CREATE SCHEMA contest;
    CREATE SCHEMA second_hand_books;
    CREATE DATABASE reverse_bookstore;

If you want to test with your own credentials, then create a user and authorise him to the created databases.
See `Configure the credentials to be used`.

### Configure the credentials to be used  ###

You must configure both the generator and the runtime connection settings.

{% highlight ini %}
// in test/fixtures/bookstore/build.properties
propel.database = mysql
propel.database.url = mysql:dbname=test
propel.mysqlTableType = InnoDB
propel.disableIdentifierQuoting=true
# For MySQL or Oracle, you also need to specify username & password
propel.database.user = myusername
propel.database.password = p@ssw0rd
{% endhighlight %}

{% highlight xml %}
// in test/fixtures/bookstore/runtime-conf.xml
<datasource id="bookstore">
  <!-- the Propel adapter to use for this connection -->
  <adapter>mysql</adapter>
  <!-- Connection parameters. See PDO documentation for DSN format and available option constants. -->
  <connection>
      <classname>DebugPDO</classname>
      <dsn>mysql:dbname=test</dsn>
      <user>myusername</user>
      <password>p@ssw0rd</password>
      <options>
        <option id="ATTR_PERSISTENT">false</option>
      </options>
      <attributes>
        <!-- For MySQL, you should also turn on prepared statement emulation,
                        as prepared statements support is buggy in mysql driver -->
        <option id="ATTR_EMULATE_PREPARES">true</option>
      </attributes>
      <settings>
        <!--  Set the character set for client connection -->
        <setting id="charset">utf8</setting>
      </settings>
  </connection>
</datasource>
{% endhighlight %}

>**Tip**<br />To run the unit tests for the namespace support in PHP 5.3, you must also configure the `fixtures/namespaced` project.

<br />

>**Tip**<br />To run the unit tests for the database schema support, you must also configure the `fixtures/schemas` project. This projects requires that your database supports schemas, and already contains the following schemas: `bookstore_schemas`, `contest`, and `second_hand_books`. Note that the user defined in `build.properties` and `runtime-conf.xml` must have access to these schemas.


### Get PHPUnit ###

You can get PHPUnit here: https://github.com/sebastianbergmann/phpunit/

The fast way to get it is:

    wget http://pear.phpunit.de/get/phpunit.phar
    chmod +x phpunit.phar

This manual is based on this phpunit.phar.

### Get Phing ###

To get phing, you can download it manually (http://www.phing.info/trac/wiki/Users/Download)
or use composer.

    curl -s https://getcomposer.org/installer | php
or
    wget http://getcomposer.org/composer.phar

and then

    php composer.phar install


## Preparing fixures ##

Every time you start using the test suite with a new propel repository you should fire

    ./test/reset_tests.sh

that prepares all required `fixures` and creates some tables in your databases. So if you re-setup your databases
re-run this script.

If you get something like

    [ bookstore ]
    No VERSION.TXT file found; try setting phing.home environment variable.

then you don't have setup phing correctly.

If you get messages like:

    BUILD FAILED
    Propel/generator/build.xml:95:15: No project directory specified

then this is OK - just ignore them.

## Writing Tests ##

### How the Tests Work ###

Every method in the test classes that begins with ‘test’ is run as a test case by PHPUnit. All tests are run in isolation;
the setUp() method is called at the beginning of ”each” test and the tearDown() method is called at the end.

The BookstoreTestBase class specifies setUp() and tearDown() methods which populate and depopulate, respectively, the database.
This means that every unit test is run with a cleanly populated database. To see the sample data that is populated, take a look at the BookstoreDataPopulator class. You can also add data to this class, if needed by your tests; however, proceed cautiously when changing existing data in there as there may be unit tests that depend on it. More typically, you can simply create the data you need from within your test method. It will be deleted by the tearDown() method, so no need to clean up after yourself.


If you've made a change to a template or to Propel behavior, the right thing to do is write a unit test that ensures that
it works properly -- and continues to work in the future.

### Write one ###

Writing a unit test often means adding a method to one of the existing test classes. For example, let's test a feature in
the Propel templates that supports saving of objects when only default values have been specified. Just add a `testSaveWithDefaultValues()`
method to the `GeneratedObjectTest` class (test/testsuite/generator/builder/om/GeneratedObjectTest.php), as follows:

{% highlight php %}
<?php

class GeneratedObjectTest extends BookstoreTestBase
{

}
[...]

/**
 * Test saving object when only default values are set.
 */
public function testSaveWithDefaultValues() {

  // Relies on a default value of 'Penguin' specified in schema
  // for publisher.name col.

  $pub = new Publisher();
  $pub->setName('Penguin');
    // in the past this wouldn't have marked object as modified
    // since 'Penguin' is the value that's already set for that attrib
  $pub->save();

  // if getId() returns the new ID, then we know save() worked.
  $this->assertNotNull($pub->getId(), "Expect Publisher->save() to work  with only default values.");
}

[...]

?>
{% endhighlight %}


You can also write additional unit test classes to any of the directories in `test/testsuite/` (or add new directories if needed).
PHPUnit command will find these files automatically and run them.


## Start the magic ##

Now you should ready to go with PHPUnit.

To start all tests, run:

    ./phpunit.phar

To start only one test, run:

    ./phpunit.phar test/testsuite/generator/model/TableTest.php


If you get

    OK, but incomplete or skipped tests!

then anything looks quite good. :-)



### Errors ####

If you get something like

    Fatal error: Call to a member function isCommitable() on a non-object in test/tools/helpers/bookstore/BookstoreTestBase.php on line 43

then you have probably not created all necessary databases or you credentials are wrong.

If you get a lot of errors in your first line, then you probably didn't fire the ./test/reset_tests.sh