---
layout: documentation
title: Working With Symfony2 (Introduction)
---
# Working with Symfony2 - Introduction #

If you are interested to work with Propel2 with Symfony2, you should consider using the [PropelBundle](https://github.com/propelorm/PropelBundle).

## Installation ##

The recommended way to install this bundle is to rely on [Composer](http://getcomposer.org):

{% highlight javascript %}
{
    "require": {
        // ...
        "propel/propel-bundle": "1.1.*"
    }
}
{% endhighlight %}

Alternatively, you can use Git, SVN, Git submodules, or the Symfony vendor management (deps file):

Clone this bundle in the `vendor/bundles/Propel` directory:

    > git submodule add https://github.com/propelorm/PropelBundle.git vendor/bundles/Propel/PropelBundle

Checkout Propel and Phing in the `vendor` directory:

    > svn checkout http://svn.github.com/propelorm/Propel.git vendor/propel

    > svn checkout http://svn.phing.info/tags/2.4.6/ vendor/phing

Instead of using svn, you can clone the unofficial Git repositories:

    > git submodule add https://github.com/phingofficial/phing.git vendor/phing

    > git submodule add https://github.com/propelorm/Propel.git vendor/propel

Instead of doing this manually, you can use the Symfony vendor management via the deps file.
If you are using a Symfony2 2.x.x version (actually, a version which is not 2.1 or above),
be sure to deps.lock the PropelBundle to a commit on the 2.0 branch, which does not use the Bridge


The second step is to register this bundle in the `AppKernel` class:

{% highlight php %}
<?php
public function registerBundles()
{
    $bundles = array(
        // ...
        new Propel\PropelBundle\PropelBundle(),
    );

    // ...
}
{% endhighlight %}

Don't forget to register the PropelBundle namespace in `app/autoload.php` if you are not using Composer:

{% highlight php %}
<?php
$loader->registerNamespaces(array(
    // ...
    'Propel' => __DIR__.'/../vendor/bundles',
));
$loader->registerPrefixes(array(
    // ...
    'Phing'  => __DIR__.'/../vendor/phing/classes/phing',
));
{% endhighlight %}

You are almost ready, the next steps are:

* to [configure the bundle](#configuration);
* to [configure Propel](#propel_configuration);
* to [write an XML schema](#xml_schema).

Now, you can build your model classes, and SQL by running the following command:

    > php app/console propel:build [--classes] [--sql] [--insert-sql]

To insert SQL statements, use the `propel:sql:insert` command:

    > php app/console propel:sql:insert [--force]

Note that the `--force` option is needed to actually execute the SQL statements.

Congratulations! You're done; just use the Model classes as any other class in Symfony2:

{% highlight php %}
<?php

class HelloController extends Controller
{
    public function indexAction($name)
    {
        $author = new \Acme\DemoBundle\Model\Author();
        $author->setFirstName($name);
        $author->save();

        return $this->render('AcmeDemoBundle:Hello:index.html.twig', array(
            'name' => $name, 'author' => $author)
        );
    }
}
{% endhighlight %}

## Bundle Inheritance ##

The `PropelBundle` makes use of the bundle inheritance. Currently only schema inheritance is provided.

### Schema Inheritance ###

You can override the defined schema of a bundle from within its child bundle.
To make use of the inheritance you only need to drop a schema file in the `Resources/config` folder of the child bundle.

Each file can be overridden without interfering with other schema files.
If you want to remove parts of a schema, you only need to add an empty schema file.

## Configuration ##

### Symfony configuration ###

In order to use Propel, you have to configure few parameters in your `app/config/config.yml` file.

If you are **not** using Composer, add this configuration:

{% highlight yaml %}
# in app/config/config.yml
propel:
    path:       "%kernel.root_dir%/../vendor/propel"
    phing_path: "%kernel.root_dir%/../vendor/phing"
{% endhighlight %}

Now, you can configure your application.


#### Basic Configuration ####

If you have just one database connection, your configuration will look like as following:

{% highlight yaml %}
# app/config/config*.yml
propel:
    dbal:
        driver:               mysql
        user:                 root
        password:             null
        dsn:                  mysql:host=localhost;dbname=test;charset=UTF8
        options:              {}
        attributes:           {}
{% endhighlight %}

The recommended way to fill in these information is to use parameters:


{% highlight yaml %}
# app/config/config*.yml
# define the parameters in app/config/parameters.yml
propel:
    dbal:
        driver:               %database_driver%
        user:                 %database_user%
        password:             %database_password%
        dsn:                  %database_driver%:host=%database_host%;dbname=%database_name%;charset=UTF8
        options:              {}
        attributes:           {}
{% endhighlight %}


#### Configure Multiple Connection ####

If you have more than one connection, or want to use a named connection, the configuration
will look like:

{% highlight yaml %}
# app/config/config*.yml
propel:
    dbal:
        default_connection:         conn1
        connections:
            conn1:
                driver:             mysql
                user:               root
                password:           null
                dsn:                mysql:host=localhost;dbname=db1
            conn2:
                driver:             mysql
                user:               root
                password:           null
                dsn:                mysql:host=localhost;dbname=db2
{% endhighlight %}


#### Configure Master/Slaves ####

You can also configure Master/Slaves:

{% highlight yaml %}
# app/config/config*.yml
propel:
    dbal:
        default_connection:         default
        connections:
            default:
                driver:             mysql
                user:               root
                password:           null
                dsn:                mysql:host=localhost;dbname=master
                slaves:
                    slave_1:
                        user:       root
                        password:   null
                        dsn:        mysql:host=localhost;dbname=slave_1
{% endhighlight %}


#### Attributes, Options, Settings ####

{% highlight yaml %}
# app/config/config*.yml
propel:
    dbal:
        default_connection:         default
        connections:
            default:
                # ...
                options:
                    ATTR_PERSISTENT: false
                attributes:
                    ATTR_EMULATE_PREPARES: true
                settings:
                    charset:        { value: UTF8 }
                    queries:        { query: 'INSERT INTO BAR ('hey', 'there')' }
{% endhighlight %}

`options`, `attributes` and `settings` are parts of the runtime configuration. See [Runtime Configuration File](http://www.propelorm.org/reference/runtime-configuration.html) documentation for more explanation.


#### Logging ####

You can disable the logging by changing the `logging` parameter value:

{% highlight yaml %}
# in app/config/config.yml
propel:
    logging:    %kernel.debug%
{% endhighlight %}

### Propel Configuration ###

You can add a `app/config/propel.ini` file in your project to specify some
configuration parameters. See the [Build properties Reference](
http://www.propelorm.org/reference/buildtime-configuration.html) to get more
information. However, **the recommended way** to configure Propel is to rely
on **build properties**, see the section below.

By default the PropelBundle is configured with the default parameters:

{% highlight ini %}
# Enable full use of the DateTime class.
# Setting this to true means that getter methods for date/time/timestamp
# columns will return a DateTime object when the default format is empty.
propel.useDateTimeClass = true

# Specify a custom DateTime subclass that you wish to have Propel use
# for temporal values.
propel.dateTimeClass = DateTime

# These are the default formats that will be used when fetching values from
# temporal columns in Propel. You can always specify these when calling the
# methods directly, but for methods like getByName() it is nice to change
# the defaults.
# To have these methods return DateTime objects instead, you should set these
# to empty values
propel.defaultTimeStampFormat =
propel.defaultTimeFormat =
propel.defaultDateFormat =

# A better Pluralizer
propel.builder.pluralizer.class = builder.util.StandardEnglishPluralizer
{% endhighlight %}


#### Build properties ####

You can define _build properties_ by creating a `propel.ini` file in `app/config` like below, but you can also follow
the Symfony2 convention by adding build properties in `app/config/config.yml`:

{% highlight yaml %}
# app/config/config.yml
propel:
    build_properties:
        xxxxx.xxxx.xxxxx:   XXXX
        xxxxx.xxxx.xxxxx:   XXXX
        // ...
{% endhighlight %}


#### Behaviors ####

You can register Propel behaviors using the following syntax:

{% highlight yaml %}
# app/config/config.yml
propel:
    behaviors:
        behavior_name: My\Bundle\Behavior\BehaviorClassName
{% endhighlight %}

If you rely on third party behaviors, most of them are autoloaded so you don't
need to register them. But, for your own behaviors, you can either configure the
autoloader to autoload them, or register them in this section (this is the
recommended way when you namespace your behaviors).

## XML Schema ##

Place the following schema in `src/Acme/DemoBundle/Resources/config/schema.xml`:

{% highlight xml %}
<?xml version="1.0" encoding="UTF-8"?>
<database name="default" namespace="Acme\DemoBundle\Model" defaultIdMethod="native">

    <table name="book">
        <column name="id" type="integer" required="true" primaryKey="true" autoIncrement="true" />
        <column name="title" type="varchar" primaryString="1" size="100" />
        <column name="ISBN" type="varchar" size="20" />
        <column name="author_id" type="integer" />
        <foreign-key foreignTable="author">
            <reference local="author_id" foreign="id" />
        </foreign-key>
    </table>

    <table name="author">
        <column name="id" type="integer" required="true" primaryKey="true" autoIncrement="true" />
        <column name="first_name" type="varchar" size="100" />
        <column name="last_name" type="varchar" size="100" />
    </table>

</database>
{% endhighlight %}

If you are working with an existing database, please [check the related section](#working-with-existing-databases).

## The Commands ##

The PropelBundle provides a lot of commands to manage migrations, database/table manipulations,
and so on.


### Database Manipulations ###

You can create a **database**:

    > php app/console propel:database:create [--connection[=""]]

As usual, `--connection` allows you to specify a connection.


You can drop a **database**:

    > php app/console propel:database:drop [--connection[=""]] [--force]

As usual, `--connection` allows you to specify a connection.

Note that the `--force` option is needed to actually execute the SQL statements.


### Form Types ###

You can generate stub classes based on your `schema.xml` in a given bundle:

    > php app/console propel:form:generate [-f|--force] bundle [models1] ... [modelsN]

It will write Form Type classes in `src/YourVendor/YourBundle/Form/Type`.

You can choose which Form Type to build by specifing Model names:

    > php app/console propel:form:generate @AcmeDemoBundle Book Author


### Graphviz ###

You can generate **Graphviz** file for your project by using the following command line:

    > php app/console propel:graphviz:generate

It will write files in `app/propel/graph/`.


### Migrations ###

Generates SQL diff between the XML schemas and the current database structure:

    > php app/console propel:migration:generate-diff

Executes the migrations:

    > php app/console propel:migration:migrate

Executes the next migration up:

    > php app/console propel:migration:migrate --up

Executes the previous migration down:

    > php app/console propel:migration:migrate --down

Lists the migrations yet to be executed:

    > php app/console propel:migration:status


### Table Manipulations ###

You can drop one or several **tables**:

    > php app/console propel:table:drop [--force] [--connection[="..."]] [table1] ... [tableN]

As usual, `--connection` allows to specify a connection.

The table arguments define which table will be delete, by default all table.

Note that the `--force` option is needed to actually execute the deletion.


### Working with existing databases ###

Run the following command to generate an XML schema from your `default` database:

    > php app/console propel:reverse

You can define which connection to use:

    > php app/console propel:reverse --connection=default

This will create your schema file under `app/propel/generated-schemas`. You need to move/copy it to the corresponding 
bundle config directory. For example: `src/Acme/DemoBundle/Resources/config/`.

## The Fixtures ##

Fixtures are data you usually write to populate your database during the development, or static content
like menus, labels, ... you need by default in your database in production.

### Loading Fixtures ###

The following command is designed to load fixtures:

    > php app/console propel:fixtures:load [-d|--dir[="..."]] [--xml] [--sql] [--yml] [--connection[="..."]] [bundle]

As you can see, there are many options to allow you to easily load fixtures.

As usual, `--connection` allows to specify a connection. The `--dir` option allows to specify a directory
containing the fixtures (default is: `app/propel/fixtures/`).
Note that the `--dir` expects a relative path from the root dir (which is `app/`).

The `--xml` parameter allows you to load only XML fixtures.
The `--sql` parameter allows you to load only SQL fixtures.
The `--yml` parameter allows you to load only YAML fixtures.

You can mix `--xml`, `--yml` and `--sql` parameters to load XML, YAML and SQL fixtures at the same time.
If none of this parameter are set all files YAML, XML and SQL in the directory will be load.

You can pass a bundle name to load fixtures from it. A bundle's name starts with `@` like `@AcmeDemoBundle`.

    > php app/console propel:fixtures:load @AcmeDemoBundle


### XML Fixtures ###

A valid _XML fixtures file_ is:

{% highlight xml %}
<Fixtures>
    <Object Namespace="Awesome">
        <o1 Title="My title" MyFoo="bar" />
    </Object>
    <Related Namespace="Awesome">
        <r1 ObjectId="o1" Description="Hello world !" />
    </Related>
</Fixtures>
{% endhighlight %}


### YAML Fixtures ###

A valid _YAML fixtures file_ is:

{% highlight yaml %}
Awesome\Object:
     o1:
         Title: My title
         MyFoo: bar

Awesome\Related:
     r1:
         ObjectId: o1
         Description: Hello world !

Awesome\Tag:
    t1:
        name: Foo
    t2:
        name: Baz

Awesome\Post:
    p1:
        title: A Post with tags (N-N relation)
        tags: [ t1, t2 ]
{% endhighlight %}


#### Using Faker in YAML Fixtures ####

If you use [Faker](https://github.com/fzaninotto/Faker) with its [Symfony2 integration](https://github.com/willdurand/BazingaFakerBundle),
then the PropelBundle offers a facility to use the Faker generator in your YAML files:

{% highlight yaml %}
Acme\DemoBundle\Model\Book:
    Book1:
        name:        "Awesome Feature"
        description: <?php $faker('text', 500); ?>
{% endhighlight %}

The aim of this feature is to be able to mix both real and fake data in the same file. Fake data is interesting to quickly
add data to your application, but most of the time you need to rely on real data. Integrating Faker in with your YAML files
allows you to write strong fixtures efficiently.


## Dumping data ##

You can dump data from your database into YAML fixtures file by using this command:

    > php app/console propel:fixtures:dump [--connection[="..."]]

Dumped files will be written in the fixtures directory: `app/propel/fixtures/` with the following name:
`fixtures_99999.yml` where `99999` is a timestamp.

Once done, you will be able to load these files by using the `propel:fixtures:load` command.


## ACL Implementation ##


The `PropelBundle` provides a model-based implementation of the Security components' interfaces.
To make us of this `AuditableAclProvider` you only need to change your security configuration.

{% highlight yaml %}
security:
    acl:
        provider: propel.security.acl.provider
{% endhighlight %}

This will switch the provider to be the `AuditableAclProvider` of the `PropelBundle`.

The auditing of this provider is set to a sensible default. It will audit all ACL failures but no success by default.
If you also want to audit successful authorizations, you need to update the auditing of the given ACL accordingly.

After adding the provider, you only need to run the `propel:acl:init` command in order to get the model generated.
If you already got an ACL database, the schema of the `PropelBundle` is compatible with the default schema of Symfony2.

### Separate database connection for ACL ###

In case you want to use a different database for your ACL than your business model, you only need to configure this service.

{% highlight yaml %}
services:
    propel.security.acl.connection:
        class: PropelPDO
        factory_class: Propel
        factory_method: getConnection
        arguments:
            - "acl"
{% endhighlight %}

The `PropelBundle` looks for this service, and if given uses the provided connection for all ACL related operations.
The given argument (`acl` in the example) is the name of the connection to use, as defined in your runtime configuration.

## The PropelParamConverter ##

You can use the PropelParamConverter with the [SensioFrameworkExtraBundle](http://github.com/sensio/SensioFrameworkExtraBundle).
You just need to put the right _Annotation_ on top of your controller:

{% highlight php %}
<?php

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
[...]

/**
 * @ParamConverter("post", class="BlogBundle\Model\Post")
 */
public function myAction(Post $post)
{
}
{% endhighlight %}

Your request needs to have an `id` parameter or any field as parameter (slug, title, ...).

The _Annotation_ is optional if your parameter is typed you could only have this:

{% highlight php %}
<?php

public function myAction(Post $post)
{
}
{% endhighlight %}

**New** with last version of `SensioFrameworkExtraBundle`, 
you can omit the `class` parameter if your controller parameter is typed, 
this is useful when you need to set extra `options`.

{% highlight php %}
<?php
use BlogBundle\Model\Post;

/**
 * @ParamConverter("post")
 */
public function myAction(Post $post)
{
}
{% endhighlight %}


### Exclude some parameters ###

You can exclude some attributes from being used by the converter:

If you have a route like `/my-route/{slug}/{name}/edit/{id}`
you can exclude `name` and `slug` by setting the option "exclude":

{% highlight php %}
<?php

/**
 * @ParamConverter("post", class="BlogBundle\Model\Post", options={"exclude"={"name", "slug"}})
 */
public function myAction(Post $post)
{
}
{% endhighlight %}

#### Custom mapping ####

You can map route parameters directly to model column to be use for filtering.

If you have a route like `/my-route/{postUniqueName}/{AuthorId}`
Mapping option overwrite any other automatic mapping.

{% highlight php %}
<?php

/**
 * @ParamConverter("post", class="BlogBundle\Model\Post", options={"mapping"={"postUniqueName":"name"}})
 * @ParamConverter("author", class="BlogBundle\Model\Author", options={"mapping"={"AuthorId":"id"}})
 */
public function myAction(Post $post, $author)
{
}
{% endhighlight %}

#### Hydrate related object ####

You could hydrate related object with the "with" option:

{% highlight php %}
<?php

/**
 * @ParamConverter("post", class="BlogBundle\Model\Post", options={"with"={"Comments"}})
 */
public function myAction(Post $post)
{
}
{% endhighlight %}

You can set multiple with ```"with"={"Comments", "Author", "RelatedPosts"}```.

The default join is an "inner join" but you can configure it to be a left join, right join or inner join :

{% highlight php %}
<?php

/**
 * @ParamConverter("post", class="BlogBundle\Model\Post", options={"with"={ {"Comments", "left join" } }})
 */
public function myAction(Post $post)
{
}
{% endhighlight %}
Accepted parmeters for join :

* left, LEFT, left join, LEFT JOIN, left_join, LEFT_JOIN
* right, RIGHT, right join, RIGHT JOIN, right_join, RIGHT_JOIN
* inner, INNER, inner join, INNER JOIN, inner_join, INNER_JOIN

## What's Next? ##

Now you are ready to use Propel with Symfony2. If you are interested, you can also checkout these cookbooks:

* [Mastering Symfony2 Forms With Propel](mastering-symfony2-forms-with-propel.html)

* [The Symfony2 Security Component And Propel](the-symfony2-security-component-and-propel.html)

* [Adding A New Behavior In Symfony2](adding-a-new-behavior-in-symfony2.html)
