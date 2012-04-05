---
layout: documentation
title: Working With Symfony2
---

# Working With Symfony2 #

The [PropelBundle](http://www.github.com/propelorm/PropelBundle) eases the integration of Propel in Symfony2.

It currently supports:

* Generation of model classes based on an XML schema (not YAML) placed under `BundleName/Resources/*schema.xml`.
* Insertion of SQL statements.
* Runtime autoloading of Propel and generated classes.
* Propel runtime initialization through the XML configuration.
* Migrations [Propel 1.6](../documentation/10-migrations.html).
* Reverse engineering from [existing database](working-with-existing-databases.html).
* Integration to the Symfony2 Profiler.
* Load SQL and XML fixtures.
* Create/Drop databases.
* Dump data into XML and SQL.

>**Important**<br />The `master` branch follows the Symfony2 `master` branch, and uses the _Propel Bridge_. If you want to use the bundle with a Symfony2 _version 2.x.x_ (actually, a version which is not _2.1_ or above), please use the `2.0` branch.

## Installation

### via Git submodule

Clone this bundle in the `vendor/bundles/Propel` directory:

    git submodule add https://github.com/propelorm/PropelBundle.git vendor/bundles/Propel/PropelBundle

Checkout Propel and Phing in the `vendor` directory:

    svn checkout http://svn.github.com/propelorm/Propel.git vendor/propel

    svn checkout http://svn.phing.info/tags/2.4.6/ vendor/phing

Instead of using svn, you can clone the unofficial Git repositories:

    git submodule add https://github.com/Xosofox/phing vendor/phing

    git submodule add https://github.com/propelorm/Propel.git vendor/propel

### via Symfony2 vendor management

As an alternative, you can also manage your vendors via Symfony2's own bin/vendor command.
Add the following lines to your deps file (located in the root of the Symfony project):

#### For working with Symfony 2.x.x

    [PropelBundle]
        git=https://github.com/propelorm/PropelBundle.git
        target=/bundles/Propel/PropelBundle
        version=origin/2.0
    [phing]
        git=https://github.com/Xosofox/phing
    [propel]
        git=https://github.com/propelorm/Propel.git

#### For working with Symfony master branch

    [PropelBundle]
        git=https://github.com/propelorm/PropelBundle.git
        target=/bundles/Propel/PropelBundle
    [phing]
        git=https://github.com/Xosofox/phing
    [propel]
        git=https://github.com/propelorm/Propel.git
    
Update your vendor directory with

    php bin/vendors install

### via Composer

Add the following lines to your `composer.json` file:

{% highlight js %}
{
  "name": "symfony/framework-standard-edition",
  // ...
  "require": {
    // ...
    "propel/propel-bundle": "dev-master",
  },
	"repositories": {
    "phing": {
      "type": "pear",
      "url": "http://pear.phing.info"
    }
  }
  // ...
}
{% endhighlight %}

Then, run `php composer.phar update`. You can get composer at [http://getcomposer.org/](http://getcomposer.org/).
If you wonder why you need to add the _Phing_ repository, read [this documentation](http://getcomposer.org/doc/faqs/why-can%27t-composer-load-repositories-recursively.md).


## Register your Bundle

Register this bundle in the `AppKernel` class:


{% highlight php %}
<?php

public function registerBundles()
{
    $bundles = array(
        ...

        // PropelBundle
        new Propel\PropelBundle\PropelBundle(),
        // register your bundles
        new Sensio\HelloBundle\HelloBundle(),
    );

    ...
}
{% endhighlight %}

Don't forget to register the PropelBundle namespace in `app/autoload.php`:

{% highlight php %}
<?php

$loader->registerNamespaces(array(
    ...

    'Propel' => __DIR__.'/../vendor/bundles',
));
{% endhighlight %}

To finish, add the following configuration `app/config/propel.ini`:

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
{% endhighlight %}

## Sample Configuration

### Project configuration

{% highlight yaml %}
# in app/config/config.yml
propel:
    path:       "%kernel.root_dir%/../vendor/propel"
    phing_path: "%kernel.root_dir%/../vendor/phing"
#    logging:   %kernel.debug%
#    build_properties:
#        xxxxx.xxxxx: xxxxxx
#        xxxxx.xxxxx: xxxxxx
{% endhighlight %}

{% highlight yaml %}
# in app/config/config_*.yml
propel:
    dbal:
        driver:               mysql
        user:                 root
        password:             null
        dsn:                  mysql:host=localhost;dbname=test;charset=UTF8
        options:              {}
        attributes:           {}
#        default_connection:       default
#        connections:
#           default:
#               driver:             mysql
#               user:               root
#               password:           null
#               dsn:                mysql:host=localhost;dbname=test
#               options:
#                   ATTR_PERSISTENT: false
#               attributes:
#                   ATTR_EMULATE_PREPARES: true
#               settings:
#                   charset:        { value: UTF8 }
#                   queries:        { query: 'INSERT INTO BAR ('hey', 'there')' }
{% endhighlight %}

`options`, `attributes` and `settings` are parts of the runtime configuration. See [Runtime Configuration File](../../reference/runtime-configuration.html) documentation for more explanation.


### Build properties

You can define _build properties_ by creating a `propel.ini` file in `app/config` and put build properties (see [Build properties Reference](../../reference/buildtime-configuration.html)).

{% highlight ini %}
# in app/config/propel.ini
xxxx.xxxx.xxxx = XXXX
{% endhighlight %}

But you can follow the Symfony2 way by adding build properties in `app/config/config.yml`:

{% highlight yaml %}
# in app/config/config.yml
propel:
    build_properties:
        xxxxx.xxxx.xxxxx:   XXXX
        xxxxx.xxxx.xxxxx:   XXXX
        ...
{% endhighlight %}


### Sample Schema

Place the following schema in `src/Sensio/HelloBundle/Resources/config/schema.xml` :

{% highlight xml %}
<?xml version="1.0" encoding="UTF-8"?>
<database name="default" namespace="Sensio\HelloBundle\Model" defaultIdMethod="native">

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


## Commands

### Build Process

Call the application console with the `propel:build` command:

    php app/console propel:build [--classes] [--sql] [--insert-sql]


### Insert SQL

Call the application console with the `propel:insert-sql` command:

    php app/console propel:insert-sql [--force]

Note that the `--force` option is needed to actually execute the SQL statements.


### Use The Model Classes

Use the Model classes as any other class in Symfony2. Just use the correct namespace, and Symfony2 will autoload them:

{% highlight php %}
<?php

class HelloController extends Controller
{
    public function indexAction($name)
    {
        $author = new \Sensio\HelloBundle\Model\Author();
        $author->setFirstName($name);
        $author->save();

        return $this->render('HelloBundle:Hello:index.html.twig', array('name' => $name, 'author' => $author));
    }
}
{% endhighlight %}


### Migrations

Generates SQL diff between the XML schemas and the current database structure:

    php app/console propel:migration:generate-diff

Executes the migrations:

    php app/console propel:migration:migrate

Executes the next migration up:

    php app/console propel:migration:migrate --up

Executes the previous migration down:

    php app/console propel:migration:migrate --down

Lists the migrations yet to be executed:

    php app/console propel:migration:status


### Working with existing databases

Run the following command to generate an XML schema from your `default` database:

    php app/console propel:reverse

You can define which connection to use:

    php app/console propel:reverse --connection=default


### Fixtures

You can load your own fixtures by using the following command:

    php app/console propel:fixtures:load [-d|--dir[="..."]] [--xml] [--sql] [--yml] [--connection[="..."]]

As usual, `--connection` allows to specify a connection.

The `--dir` option allows to specify a directory containing the fixtures (default is: `app/propel/fixtures/`).
Note that the `--dir` expects a relative path from the root dir (which is `app/`).

The `--xml` parameter allows you to load only XML fixtures.
The `--sql` parameter allows you to load only SQL fixtures.
The `--yml` parameter allows you to load only YAML fixtures.

You can mix `--xml`, `--yml` and `--sql` parameters to load XML, YAML and SQL fixtures.
If none of this parameter are set all files YAML, XML and SQL in the directory will be load.

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

A valid _YAML fixtures file_ is:

{% highlight yaml %}
\Awesome\Object:
     o1:
         Title: My title
         MyFoo: bar

 \Awesome\Related:
     r1:
         ObjectId: o1
         Description: Hello world !
{% endhighlight %}

You can dump data into YAML fixtures file by using this command:

    php app/console propel:fixtures:dump [--connection[="..."]]

Dumped files will be written in the fixtures directory: `app/propel/fixtures/` with the following name: `fixtures_99999.yml` where `99999`
is a timestamp.
Once done, you will be able to load this files by using the `propel:fixtures:load` command.

#### Self Referencing Fixtures

In order to make use of several behaviors, such as the `VersionableBehavior` you can self-reference fixtures.

A valid _YAML fixtures file_ is:

{% highlight yaml %}
\Blog\Post:
     Post1_V1:
         title: My title
         content: tba
     Post1_V2:
         id: Post1_V1
         title: My title
         content: This is my first post.
\Blog\Comment:
    Comment_1:
        post_id: Post1_V1
        content: Awesome post!
{% endhighlight %}

This fixtures will first insert a `Post` object with the data of `Post1_V1`.
Afterwards the `Post1_V2` will be read and the reference to the previous post `Post1_V1` on the `PrimaryKey` column (`id`) will result in an `UPDATE` operation.

Assuming you had the `VersionableBehavior` active, this would insert *1* `Post` with *2* `PostVersion` entries.

### Graphviz

You can generate **Graphviz** file for your project by using the following command line:

    php app/console propel:graphviz

It will write files in `app/propel/graph/`.


### Database ###

You can create a **database**:

    > php app/console propel:database:create [--connection[=""]]

As usual, `--connection` allows to specify a connection.


You can drop a **database**:

    > php app/console propel:database:drop [--connection[=""]] [--force]

As usual, `--connection` allows to specify a connection.

Note that the `--force` option is needed to actually execute the SQL statements.

### Table ###

You can drop one or several **table**:

    > php app/console propel:table:drop [--force] [--connection[="..."]] [table1] ... [tableN]

As usual, `--connection` allows to specify a connection.

The table arguments define which table will be delete, by default all table.

Note that the `--force` option is needed to actually execute the deletion.


## PropelParamConverter ##

You can use the Propel ParamConverter with the SensioFrameworkExtraBundle.
You just need to put the right _Annotation_ on top of your controller:

{% highlight php %}
<?php

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

Exclude some parameters:

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


## UniqueObjectValidator ##

In a form, if you want to validate the unicity of a field in a table you have to use the UniqueObjectValidator.
The only way to use it is in a validation.yml file, like this:

{% highlight yaml %}
BundleNamespace\Model\User:
  constraints:
    - Propel\PropelBundle\Validator\Constraints\UniqueObject: username
{% endhighlight %}

For validate the unicity of more than just one fields:

{% highlight yaml %}
BundleNamespace\Model\User:
  constraints:
    - Propel\PropelBundle\Validator\Constraints\UniqueObject: [username, login]
{% endhighlight %}

As many validator of this type as you want can be used.

## Bundle Inheritance ##

The `PropelBundle` makes use of the bundle inheritance.
Currently only schema inheritance is provided.

### Schema Inheritance ###

You can override the defined schema of a bundle from within its child bundle.
To make use of the inheritance you only need to drop a schema file in the `Resources/config` folder of the child bundle.

Each file can be overridden without interfering with other schema files.
If you want to remove parts of a schema, you only need to add an empty schema file.

## ACL implementation ##

The `PropelBundle` provides a model-based implementation of the Security components' interfaces.
To make use of this `AuditableAclProvider` you only need to change your security configuration.

``` yaml
security:
    acl:
        provider: propel.security.acl.provider
```

This will switch the provider to be the `AuditableAclProvider` of the `PropelBundle`.

The auditing of this provider is set to a sensible default. It will audit all ACL failures but no success by default.
If you also want to audit successful authorizations, you need to update the auditing of the given ACL accordingly.

After adding the provider, you only need to run the `propel:init-acl` command in order to get the model generated.
If you already got an ACL database, the schema of the `PropelBundle` is compatible with the default schema of Symfony2.

### Separate database connection for ACL ###

In case you want to use a different database for your ACL than your business model, you only need to configure this service.

``` yaml
services:
    propel.security.acl.connection:
        class: PropelPDO
        factory_class: Propel
        factory_method: getConnection
        arguments:
            - "acl"
```

The `PropelBundle` looks for this service, and if given uses the provided connection for all ACL related operations.
The given argument (`acl` in the example) is the name of the connection to use, as defined in your runtime configuration.
