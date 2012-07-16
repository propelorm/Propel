---
layout: default
title: Adding A New Behavior In Symfony2
---

# Adding A New Behavior In Symfony2 #

Propel provides a lot of [behaviors](../../documentation/07-behaviors.html) but there are also [third-party behaviors](../user-contributed-behaviors.html) provided by the community.

In order to get these behaviors working in a Symfony2 application with Propel, you need to register them, here is
how you should do.

The first step is to get the code. If the behavior is available on a Git repository, like GitHub for instance,
then you'll be able to add it to your `deps` file.
Assuming you want to use the [GeocodableBehavior](https://github.com/willdurand/GeocodableBehavior), you'll write:

{% highlight bash %}
[GeocodableBehavior]
    git=http://github.com/willdurand/GeocodableBehavior.git
    target=/willdurand/propel-geocodable-behavior
{% endhighlight %}

If you are using Git submodules, then run:

{% highlight bash %}
git submodule add http://github.com/willdurand/GeocodableBehavior.git vendor/willdurand/propel-geocodable-behavior
{% endhighlight %}

If you are using [Composer](http://getcomposer.org), then just require the behavior in your `composer.json`:

{% highlight javascript %}
{
    "require": {
        // ...
        "willdurand/propel-geocodable-behavior": "dev-master"
    }
}
{% endhighlight %}

And run, `php composer.php update`.

>**Tip**<br />If there is no available Git repository for a behavior, just copy it to `vendor/propel-thebehavior-behavior`. It's up to you to version it or not.

You're done!
