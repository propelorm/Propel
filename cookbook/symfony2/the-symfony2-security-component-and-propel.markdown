---
layout: default
title: The Symfony2 Security Component And Propel
---

# The Symfony2 Security Component And Propel #

If you've started to play with the awesome Symfony2 Security Component, you'll know that you can configure a **provider**
to retrieve your users. Symfony2 has two providers: `in_memory` and `entity`. Unfortunately, no other providers exist.

But you can add your own custom provider and the `PropelBundle` provides a dedicated class to ease that: `ModelUserProvider`.

Basically, you'll have to create a service:

{% highlight xml %}
<!-- src/Acme/SecuredBundle/Resources/config/services.xml -->
<?xml version="1.0" ?>
<container xmlns="http://symfony.com/schema/dic/services"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xsi:schemaLocation="http://symfony.com/schema/dic/services http://symfony.com/schema/dic/services/services-1.0.xsd">

    <services>
        <service id="acme.secured.security.provider" class="Acme\SecuredBundle\Security\User\CustomUserProvider" />
    </services>

</container>
{% endhighlight %}

And the corresponding class:

{% highlight php %}
<?php
// src/Acme/SecuredBundle/Security/User/CustomUserProvider.php

namespace Acme\SecuredBundle\Security\User;

use Propel\PropelBundle\Security\User\ModelUserProvider;

class CustomUserProvider extends ModelUserProvider
{
    public function __construct()
    {
        parent::__construct('Acme\SecuredBundle\Model\User', 'username');
    }
}
{% endhighlight %}

The `ModelUserProvider` takes two arguments:

* A _class name_ which is the Propel class that owns the logic of your users;
* A _property name_ which is the property to retrieve your users (default is: `username`).

Once done, you'll have to register your new custom provider in the `security.yml` file:

{% highlight yaml %}
# src/Acme/SecuredBundle/Resources/config/security.yml
security:
    # ...
    providers:
        custom_provider:
            id: acme.secured.security.provider
{% endhighlight %}

You now have a working security provider with Propel.
