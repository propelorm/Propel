---
layout: documentation
title: Geocodable Behavior
---

# Geocodable Behavior #

The [**Geocodable Behavior**](https://github.com/willdurand/GeocodableBehavior) helps you build geo-aware applications. It automatically geocodes your models when they are saved, giving you the ability to search by location and calculate distances between records.

This behavior uses [Geocoder](https://github.com/willdurand/Geocoder), the Geocoder PHP 5.3 library and requires [Propel](http://github.com/propelorm/Propel) 1.6.4-dev and above.

## Installation ##

Download the behavior: [https://github.com/willdurand/GeocodableBehavior](https://github.com/willdurand/GeocodableBehavior), then cherry-pick the `GeocodableBehavior.php` file in `src/`, and put it somewhere.

Add the following line to your `propel.ini` or `build.properties` configuration file:

{% highlight ini %}
propel.behavior.geocodable.class = path.to.GeocodableBehavior
{% endhighlight %}

## Usage ##

Just add the following XML tag in your `schema.xml` file:

{% highlight xml %}
<behavior name="geocodable" />
{% endhighlight %}

Basically, the behavior will add:

* two new columns to your model (`latitude` and `longitude`);
* four new methods to the _ActiveRecord_ API (`getDistanceTo()`, `isGeocoded()`, `getCoordinates()`, and `setCoordinates()`);
* a new method to the _ActiveQuery_ API (`filterByDistanceFrom()`).


### ActiveRecord API ###

`getDistanceTo()` returns the distance between the current object and a given one.
The method takes two arguments:

* a geocoded object;
* a measure unit (`KILOMETERS_UNIT`, `MILES_UNIT`, or `NAUTICAL_MILES_UNIT` defined in the `Peer` class of the geocoded model).

`isGeocoded()` returns a boolean value whether the object has been geocoded or not.

`getCoordinates()`, `setCoordinates()` allows to quickly set/get latitude and longitude values.


### ActiveQuery API ###

`filterByDistanceFrom()` takes five arguments:

* a latitude value;
* a longitude value;
* a distance value;
* a measure unit (`KILOMETERS_UNIT`, `MILES_UNIT`, or `NAUTICAL_MILES_UNIT` defined in the `Peer` class of the geocoded model);
* a comparison sign (`Criteria::LESS_THAN` is the default value).


It will add a filter by distance on your current query and returns itself for fluid interface.


## Automatic Geocoding ##

At this step, you have to fill in the two columns (`latitude` and `longitude`) yourself.
It's not really useful, right ?

Automatic geocoding to the rescue! There are two automatic ways to get geocoded information:

* using IP addresses;
* using street addresses.

Note: You can use both at the same time.


### IP-Based Geocoding ###

To enable the IP-Based geocoding, add the following configuration in your `schema.xml` file:

{% highlight xml %}
<behavior name="geocodable">
    <parameter name="geocode_ip" value="true" />
    <parameter name="geocoder_api_key" value="<API_KEY>" />
</behavior>
{% endhighlight %}

By default, the default Geocoder `provider` is `YahooProvider` so you'll need to fill in an API key.

If you want to use another provider, you'll need to set a new parameter:

{% highlight xml %}
<parameter name="geocoder_provider" value="\Geocoder\Provider\HostIpProvider" />
{% endhighlight %}

Read the **Geocoder** documentation to know more about providers.

This configuration will add a new column to your model: `ip_address`. You can change the name of this column using the following parameter:

{% highlight xml %}
<parameter name="ip_column" value="ip" />
{% endhighlight %}

The behavior will now use the `ip_address` value to populate the `latitude` and `longitude` columns thanks to **Geocoder**.


### Address-Based Geocoding ###

To enable the Address-Based geocoding, add the following configuration:

{% highlight xml %}
<behavior name="geocodable">
    <parameter name="geocode_address" value="true" />
    <parameter name="geocoder_api_key" value="<API_KEY>" />
</behavior>
{% endhighlight %}

By default, the default Geocoder `provider` is `YahooProvider` so you'll need to fill in an API key but keep in mind it's an optional parameter depending on the provider you choose.

If you want to use another provider, you'll need to set a new parameter:

{% highlight xml %}
<parameter name="geocoder_provider" value="\Geocoder\Provider\GoogleMapsProvider" />
{% endhighlight %}

Read the **Geocoder** documentation to know more about providers.

Basically, the behavior looks for attributes called street, locality, region, postal_code, and country. It tries to make a complete address with them. As usual, you can tweak this parameter to add your own list of attributes that represents a complete street address:

{% highlight xml %}
<parameter name="address_columns" value="street,locality,region,postal_code,country" />
{% endhighlight %}

These parameters will be concatened and separated by a comma to make a street address. This address will be used to get `latitude` and `longitude` values.

Now, each time you save your object, the two columns `latitude` and `longitude` are populated thanks to **Geocoder**.


## HTTP Adapters ##

**Geocoder** provides HTTP adapters which can be configured through the behavior. By default, this behavior uses the `CurlHttpAdapter`.

If you want to use another `adapter`, you'll need to use the following parameter:

{% highlight xml %}
<parameter name="geocoder_adapter" value="\Geocoder\HttpAdapter\BuzzHttpAdapter" />
{% endhighlight %}

Read the **Geocoder** documentation to know more about adapters.


## Parameters ##

{% highlight xml %}
<behavior name="geocodable">
    <parameter name="latitude_column" value="latitude" />
    <parameter name="longitude_column" value="longitude" />

    <!-- IP-Based Geocoding -->
    <parameter name="geocode_ip" value="false" />
    <parameter name="ip_column" value="ip_address" />

    <!-- Address-Based Geocoding -->
    <parameter name="geocode_address" value="false" />
    <parameter name="address_columns" value="street,locality,region,postal_code,country" />

    <!-- Geocoder -->
    <parameter name="geocoder_provider" value="\Geocoder\Provider\YahooProvider" />
    <parameter name="geocoder_adapter" value="\Geocoder\HttpAdapter\CurlHttpAdapter" />
    <parameter name="geocoder_api_key" value="false" />
</behavior>
{% endhighlight %}

This is the default configuration.
