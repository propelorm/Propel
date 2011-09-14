---
layout: default
title: Download Propel
---

# Download Propel #

For a full installation tutorial, check the [Installation documentation](documentation/01-installation). The following options allow you to download the Propel code and documentation.

## Git ##

Clone it:

{% highlight bash %}
> git clone git://github.com/propelorm/Propel.git
{% endhighlight %}

Or add it as a submodule:

{% highlight bash %}
> git submodule add git://github.com/propelorm/Propel.git /path/to/propel
{% endhighlight %}

## Subversion Checkout / Externals ##

{% highlight bash %}
> svn co http://svn.github.com/propelorm/Propel.git
{% endhighlight %}

>**Warning**<br />SVN is no more the default Source Code Management since 2011.

## PEAR Installer ##

Propel is available through its own PEAR channel [pear.propelorm.org](pear.propelorm.org), in two separate packages for generator and runtime:

{% highlight bash %}
> pear channel-discover pear.propelorm.org
> sudo pear install -a propel/propel_generator
> sudo pear install -a propel/propel_runtime
{% endhighlight %}

Propel depends on the Phing library, and the dependency should be properly handled by PEAR thanks to the -a option above. Alternatively, you can install Phing separately:

{% highlight bash %}
> pear channel-discover pear.phing.info
> sudo pear install phing/phing
{% endhighlight %}

>**Tip**<br />If you would like to use a beta or RC version of Propel, you may need to change your preferred_state PEAR environment variable.

## Full Propel Package ##

Please download one of the packages below if you would like to install the traditional Propel package, which includes both runtime and generator components.

* [Last version of Propel as ZIP file](https://github.com/propelorm/Propel/zipball/master)
* [Last version of Propel as TAR.GZ file](https://github.com/propelorm/Propel/tarball/master)

Other releases are available for download at [files.propelorm.org](http://files.propelorm.org).
