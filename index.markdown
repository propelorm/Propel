---
layout: default
title: Propel documentation
---

## Welcome to the Propel Website! ##

### What? ###

Propel is an open-source Object-Relational Mapping (ORM) for PHP5. It allows you to access your database using a set of objects, providing a simple API for storing and retrieving data.

### Why? ###

ves you, the web application developer, the tools to work with databases in the same way you work with other classes and objects in PHP.

* Propel gives your database a well-defined API.
* Propel uses the PHP5 OO standards -- Exceptions, autoloading, Iterators and friends.

Propel makes database coding fun again.

### Show Me! ###

{% highlight php %}
<?php
$book = BookQuery::create()->findPK(123); // retrieve a record from a database
$book->setName('Don\'t be Hax0red!'); // modify. Don't worry about escaping
$book->save(); // persist the modification to the database

$books = BookQuery::create()  // retrieve all books...
  ->filterByPublishYear(2009) // ... published in 2009
  ->orderByTitle()            // ... ordered by title
  ->joinWith('Book.Author')   // ... with their author
  ->find();
foreach($books as $book) {
  echo  $book->getAuthor()->getFullName();
}
{% endhighlight %}

### Get It! ###

### Dive In! ###
