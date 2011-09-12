---
layout: documentation
title: Init A Symfony Project With Propel As Default ORM - The Git Way
---

# Init A Symfony Project With Propel As Default ORM - The Git Way #

Since this summer `Propel` ORM has had a new `Symfony` integration plugin `sfPropelORMPlugin` replacing the old one `sfPropel15Plugin`.
 
The old `sfPropel15Plugin` caused [some misunderstood at each new Propel's version](http://propel.posterous.com/sfpropel16plugin-is-already-there-didnt-you-k).
   
Now `sfPropelORMPlugin` will always integrate the last `Propel`'s version to `Symfony 1.4`.
 
Let me show you how to start a new `Symfony 1.4` project with all necessary libraries as `git submodule`.
  
## Install `Symfony1.4` as `git submodule`, init a new project, init `sfPropelORMPlugin` as `git submodule` ##
 
{% highlight bash %}
mkdir propel
cd propel
git init
git submodule add git://github.com/vjousse/symfony-1.4.git lib/vendor
php lib/vendor/data/bin/symfony generate:project propel
git submodule add git://github.com/propelorm/sfPropelORMPlugin plugins/sfPropelORMPlugin
cd plugins/sfPropelORMPlugin
git submodule update --init
{% endhighlight %}

the `.gitignore` file will be something like

{% highlight bash %}
config/databases.yml
cache/*
log/*
data/sql/*
lib/filter/base/*
lib/form/base/*
lib/model/map/*
lib/model/om/*
{% endhighlight %}

enable `sfPropelORMPlugin` in `config/ProjectConfiguration.class.php`

{% highlight php %}
class ProjectConfiguration extends sfProjectConfiguration
{
  public function setup()
  {
     $this->enablePlugins('sfPropelORMPlugin');
  }
}
{% endhighlight %}

publish assets

{% highlight bash %}
php symfony plugin:publish-assets
{% endhighlight %}

copy `propel.ini` model in your project

{% highlight bash %}
cp plugins/sfPropelORMPlugin/config/skeleton/config/propel.ini config/propel.ini
{% endhighlight %}

verify behaviors lines look like: 

{% highlight ini %}

// config/propel.ini

propel.behavior.symfony.class                  = plugins.sfPropelORMPlugin.lib.behavior.SfPropelBehaviorSymfony
propel.behavior.symfony_i18n.class             = plugins.sfPropelORMPlugin.lib.behavior.SfPropelBehaviorI18n
propel.behavior.symfony_i18n_translation.class = plugins.sfPropelORMPlugin.lib.behavior.SfPropelBehaviorI18nTranslation
propel.behavior.symfony_behaviors.class        = plugins.sfPropelORMPlugin.lib.behavior.SfPropelBehaviorSymfonyBehaviors
propel.behavior.symfony_timestampable.class    = plugins.sfPropelORMPlugin.lib.behavior.SfPropelBehaviorTimestampable
{% endhighlight %}
                 
adapt your `databases.yml` or copy the model in your project

{% highlight bash %}
cp plugins/sfPropelORMPlugin/config/skeleton/config/databases.yml config/databases.yml
{% endhighlight %}

it has to look like this

{% highlight yml %}
# You can find more information about this file on the symfony website:
# http://www.symfony-project.org/reference/1_4/en/07-Databases

dev:
  propel:
    param:
      classname:  DebugPDO
      debug:
        realmemoryusage: true
        details:
          time:       { enabled: true }
          slow:       { enabled: true, threshold: 0.1 }
          mem:        { enabled: true }
          mempeak:    { enabled: true }
          memdelta:   { enabled: true }

test:
  propel:
    param:
      classname:  DebugPDO

all:
  propel:
    class:        sfPropelDatabase
    param:
      classname:  PropelPDO
      dsn:        mysql:dbname=test;host=localhost
      username:   root
      password:   
      encoding:   utf8
      persistent: true
      pooling:    true

{% endhighlight %}

you're now ready for writing a `schema.xml` and building your project  