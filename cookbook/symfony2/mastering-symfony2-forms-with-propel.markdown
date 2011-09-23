---
layout: documentation
title: Mastering Symfony2 Forms With Propel
---

# Mastering Symfony2 Forms With Propel #

In this chapter, you'll learn how to master Symfony2 forms with Propel.
First of all, you have to assume you'll play with `Book` and `Author` objects
into a `LibraryBundle` bundle. The `schema.xml` file is defined as following:

{% highlight xml %}
<?xml version="1.0" encoding="UTF-8"?>
<database name="default" namespace="Acme\LibraryBundle\Model" defaultIdMethod="native">
    <table name="book">
        <column name="id" type="integer" required="true" primaryKey="true" autoIncrement="true" />
        <column name="title" type="varchar" primaryString="1" size="100" />
        <column name="isbn" type="varchar" size="20" />
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

In Symfony2, you deal with `Type` so let's creating a `BookType` to manage
our books. For the moment, just ignore the relation with `Author` objects.

{% highlight php %}
<?php
// src/Acme/LibraryBundle/Form/Type/BookType.php

namespace Acme\LibraryBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class BookType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('name');
        $builder->add('isbn');
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'Acme\LibraryBundle\Model\Book',
        );
    }

    public function getName()
    {
        return 'book';
    }
}
{% endhighlight %}

>**Setting the `data_class`**<br />Every form needs to know the name of the class that holds the underlying data (e.g. `Acme\LibraryBundle\Model\Book`). Usually, this is just guessed based off of the object passed to the second argument to createForm().

Basically, you will use this class in an action of one of your controllers.
Assuming you have a `BookController` controller in your `LibraryBundle`, you will
write the following code to create new books:

{% highlight php %}
<?php
// src/Acme/LibraryBundle/Controller/BookController.php

namespace Acme\LibraryBundle\Controller;

use Acme\LibraryBundle\Model\Book;
use Acme\LibraryBundle\Form\Type\BookType;

class BookController
{
    public function newAction()
    {
        $book = new Book();
        $form = $this->createForm(new BookType(), $book);

        // ...
    }
}
{% endhighlight %}

As such, the topic of persisting the `Book` object to the database is entirely
unrelated to the topic of forms. But, if you've created a `Book` class with Propel,
then persisting it after a form submission can be done when the form is valid:

{% highlight php %}
<?php
// src/Acme/LibraryBundle/Controller/BookController.php

// ...

    public function newAction()
    {
        // ...

        if ($form->isValid()) {
            $book->save();

            return $this->redirect($this->generateUrl('book_success'));
        }
    }
{% endhighlight %}

If, for some reason, you don't have access to your original `$book` object,
you can fetch it from the form:

{% highlight php %}
<?php

$book = $form->getData();
{% endhighlight %}

As you can see, this is really easy to manage basic forms with both Symfony2
and Propel. But, in real life, this kind of forms is not enought and you'll probably
manage objects with relations, this is the next part of this chapter.


## One-To-Many relations ##

A `Book` has an `Author`, this is a **One-To-Many** relation. Let's modifing your
`BookType` to handle this relation:

{% highlight php %}
<?php
// src/Acme/LibraryBundle/Form/Type/BookType.php

namespace Acme\LibraryBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class BookType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('name');
        $builder->add('isbn');
        // Author relation
        $builder->add('author', new AuthorType());
    }

    // ...
}
{% endhighlight %}

You now have to write an `AuthorType` to reflect the new requirements:

{% highlight php %}
<?php
// src/Acme/LibraryBundle/Form/Type/AuthorType.php

namespace Acme\LibraryBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class AuthorType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('first_name');
        $builder->add('last_name');
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'Acme\LibraryBundle\Model\Author',
        );
    }

    public function getName()
    {
        return 'author';
    }
}
{% endhighlight %}

When the user submits the form, the submitted data for the `Author` fields are used to construct an
instance of `Author`, which is then set on the author field of the `Book` instance.
The `Author` instance is accessible naturally via $book->getAuthor().


## Many-To-Many relations ##

Now, imagine you want to add your books to some lists for book clubs. A `BookClubList` can have many
`Book` objects and a `Book` can be in many lists (`BookClubList`). This is a **Many-To-Many** relation.
Add the following defintion to your `schema.xml ` and rebuild your model classes:

{% highlight xml %}
<table name="book_club_list" description="Reading list for a book club.">
    <column name="id" required="true" primaryKey="true" autoIncrement="true" type="INTEGER" description="Unique ID for a school reading list." />
    <column name="group_leader" required="true" type="VARCHAR" size="100" description="The name of the teacher in charge of summer reading." />
    <column name="theme" required="false" type="VARCHAR" size="50" description="The theme, if applicable, for the reading list." />
    <column name="created_at" required="false" type="TIMESTAMP" />
</table>
<table name="book_x_list" phpName="BookListRel" isCrossRef="true"
    description="Cross-reference table for many-to-many relationship between book rows and book_club_list rows.">
    <column name="book_id" primaryKey="true" type="INTEGER" description="Fkey to book.id" />
    <column name="book_club_list_id" primaryKey="true" type="INTEGER" description="Fkey to book_club_list.id" />
    <foreign-key foreignTable="book" onDelete="cascade">
        <reference local="book_id" foreign="id" />
    </foreign-key>
    <foreign-key foreignTable="book_club_list" onDelete="cascade">
        <reference local="book_club_list_id" foreign="id" />
    </foreign-key>
</table>
{% endhighlight %}

You now have `BookClubList` and `BookListRel` objects. Let's creating a `BookClubListType`:

{% highlight php %}
<?php
// src/Acme/LibraryBundle/Form/Type/BookClubListType.php

namespace Acme\LibraryBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class BookClubListType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('group_leader');
        $builder->add('theme');
        // Book collection
        $builder->add('books', 'collection', array(
            'type'          => 'Acme\LibraryBundle\Form\Type\BookType',
            'allow_add'     => true,
            'allow_delete'  => true,
            'by_reference'  => false
        ));
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'Acme\LibraryBundle\Model\BookClubList',
        );
    }

    public function getName()
    {
        return 'book_club_list';
    }
}
{% endhighlight %}

You've added a `CollectionType` for the `Book` list and you've configured it
with your `BookType`. In this example, you allow to add and/or delete books.

>**Warning**<br />The parameter `by_reference` has to be defined and set to `false`. This is required to tell the Forms component to call the setter method (`setBooks()` in this example).

Thanks to the smart collection setter provided by Propel, there is nothing more to configure.
Use the `BookClubListType` as you previously did with the `BookType`. Note the Symfony2 component
doesn't handle the add/remove abilities in the view. You have to write some JavaScript for that.


## Summary ##

The Symfony2 Form Component doesn't have anymore secrets for you and to use it with Propel is really
easy.
