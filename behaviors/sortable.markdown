---
layout: documentation
title: Sortable Behavior
---

# Sortable Behavior #

The `sortable` behavior allows a model to become an ordered list, and provides numerous methods to traverse this list in an efficient way.

## Basic Usage ##

In the `schema.xml`, use the `<behavior>` tag to add the `sortable` behavior to a table:
```xml
<table name="task">
  <column name="id" required="true" primaryKey="true" autoIncrement="true" type="INTEGER" />
  <column name="title" type="VARCHAR" required="true" primaryString="true" />
  <behavior name="sortable" />
</table>
```

Rebuild your model, insert the table creation sql again, and you're ready to go. The model now has the ability to be inserted into an ordered list, as follows:

```php
<?php
$t1 = new Task();
$t1->setTitle('Wash the dishes');
$t1->save();
echo $t1->getRank(); // 1, the first rank to be given (not 0)
$t2 = new Task();
$t2->setTitle('Do the laundry');
$t2->save();
echo $t2->getRank(); // 2
$t3 = new Task();
$t3->setTitle('Rest a little');
$t3->save()
echo $t3->getRank(); // 3
```

As long as you save new objects, Propel gives them the first available rank in the list.

Once you have built an ordered list, you can traverse it using any of the methods added by the `sortable` behavior. For instance:

```php
<?php
$firstTask = TaskQuery::create()->findOneByRank(1); // $t1
$secondTask = $firstTask->getNext();      // $t2
$lastTask = $secondTask->getNext();       // $t3
$secondTask = $lastTask->getPrevious();   // $t2

$allTasks = TaskQuery::create()->findList();
// => collection($t1, $t2, $t3)
$allTasksInReverseOrder = TaskQuery::create()->orderByRank('desc')->find();
// => collection($t3, $t2, $t2)
```

The results returned by these methods are regular Propel model objects, with access to the properties and related models. The `sortable` behavior also adds inspection methods to objects:

```php
<?php
echo $t2->isFirst();      // false
echo $t2->isLast();       // false
echo $t2->getRank();      // 2
```

## Manipulating Objects In A List ##

You can move an object in the list using any of the `moveUp()`, `moveDown()`, `moveToTop()`, `moveToBottom()`, `moveToRank()`, and `swapWith()` methods. These operations are immediate and don't require that you save the model afterwards:

```php
<?php
// The list is 1 - Wash the dishes, 2 - Do the laundry, 3 - Rest a little
$t2->moveToTop();
// The list is now 1 - Do the laundry, 2 - Wash the dishes, 3 - Rest a little
$t2->moveToBottom();
// The list is now 1 - Wash the dishes, 2 - Rest a little, 3 - Do the laundry
$t2->moveUp();
// The list is 1 - Wash the dishes, 2 - Do the laundry, 3 - Rest a little
$t2->swapWith($t1);
// The list is now 1 - Do the laundry, 2 -  Wash the dishes, 3 - Rest a little
$t2->moveToRank(3);
// The list is now 1 - Wash the dishes, 2 - Rest a little, 3 - Do the laundry
$t2->moveToRank(2);
```

By default, new objects are added at the bottom of the list. But you can also insert them at a specific position, using any of the `insertAtTop()`, `insertAtBottom()`, and `insertAtRank()` methods. Note that the `insertAtXXX` methods don't save the object:

```php
<?php
// The list is 1 - Wash the dishes, 2 - Do the laundry, 3 - Rest a little
$t4 = new Task();
$t4->setTitle('Clean windows');
$t4->insertAtRank(2);
$t4->save();
// The list is now  1 - Wash the dishes, 2 - Clean Windows, 3 - Do the laundry, 4 - Rest a little
```

Whenever you `delete()` an object, the ranks are rearranged to fill the gap:

```php
<?php
$t4->delete();
// The list is now 1 - Wash the dishes, 2 - Do the laundry, 3 - Rest a little
```

>**Tip**<br />You can remove an object from the list without necessarily deleting it by calling `removeFromList()`. Don't forget to `save()` it afterwards so that the other objects in the lists are rearranged to fill the gap.

## Multiple Lists ##

When you need to store several lists for a single model - for instance, one task list for each user - use a _scope_ for each list. This requires that you enable scope support in the behavior definition by setting the `use_scope` parameter to `true`:

```xml
<table name="task">
  <column name="id" required="true" primaryKey="true" autoIncrement="true" type="INTEGER" />
  <column name="title" type="VARCHAR" required="true" primaryString="true" />
  <column name="user_id" required="true" type="INTEGER" />
  <foreign-key foreignTable="user" onDelete="cascade">
    <reference local="user_id" foreign="id" />
  </foreign-key>
  <behavior name="sortable">
    <parameter name="use_scope" value="true" />
    <parameter name="scope_column" value="user_id" />
  </behavior>
</table>
```

Now, after rebuilding your model, you can have as many lists as required:

```php
<?php
// test users
$paul = new User();
$john = new User();
// now onto the tasks
$t1 = new Task();
$t1->setTitle('Wash the dishes');
$t1->setUser($paul);
$t1->save();
echo $t1->getRank(); // 1
$t2 = new Task();
$t2->setTitle('Do the laundry');
$t2->setUser($paul);
$t2->save();
echo $t2->getRank(); // 2
$t3 = new Task();
$t3->setTitle('Rest a little');
$t3->setUser($john);
$t3->save()
echo $t3->getRank(); // 1, because John has his own task list
```

The generated methods now accept a `$scope` parameter to restrict the query to a given scope:

```php
<?php
$firstPaulTask = TaskQuery::create()->findOneByRank($rank = 1, $scope = $paul->getId()); // $t1
$lastPaulTask = $firstTask->getNext();      // $t2
$firstJohnTask = TaskPeer::create()->findOneByRank($rank = 1, $scope = $john->getId()); // $t1
```

Models using the sortable behavior with scope benefit from one additional Query method named `inList()`:

```php
<?php
$allPaulsTasks = TaskPeer::create()->inList($scope = $paul->getId())->find();
```

## Multi-Column scopes ##

As of Propel 1.7.0 we added support for Multi-Column scoped Sortable Behavior. This is defined using a comma separated list of column names as `scope_column` parameter.
Note that API methods which are generated by the behavior take parameters in the order which they are defined in `scope_column` parameter.  

```xml
<table name="task">
  <column name="id" required="true" primaryKey="true" autoIncrement="true" type="INTEGER" />
  <column name="title" type="VARCHAR" required="true" primaryString="true" />
  <column name="user_id" required="true" type="INTEGER" />
  <column name="group_id" required="true" type="INTEGER" />
  <foreign-key foreignTable="user" onDelete="cascade">
    <reference local="user_id" foreign="id" />
  </foreign-key>
  <behavior name="sortable">
    <parameter name="use_scope" value="true" />
    <parameter name="scope_column" value="user_id, group_id" />
  </behavior>
</table>
```

As an alternative you may define the same schema using several `scope_column` tags.

```xml
<table name="task">
  <column name="id" required="true" primaryKey="true" autoIncrement="true" type="INTEGER" />
  <column name="title" type="VARCHAR" required="true" primaryString="true" />
  <column name="user_id" required="true" type="INTEGER" />
  <column name="group_id" required="true" type="INTEGER" />
  <foreign-key foreignTable="user" onDelete="cascade">
    <reference local="user_id" foreign="id" />
  </foreign-key>
  <behavior name="sortable">
    <parameter name="use_scope" value="true" />
    <parameter name="scope_column" value="user_id" />
    <parameter name="scope_column" value="group_id" />
  </behavior>
</table>
```

With this schema defined Propel manages one sortable list of tasks per User per Group, so for each User-Group combination:

```php
<?php
// test groups
$adminGroup = new Group();
$userGroup = new Group();
// test users
$paul = new User();
$john = new User();

// now onto the tasks
$t1 = new Task();
$t1->setTitle('Create permissions');
$t1->setUser($paul);
$t1->setGroup($adminGroup);
$t1->save();
echo $t1->getRank(); // 1

$t2 = new Task();
$t2->setTitle('Grant permissions to users');
$t2->setUser($paul);
$t2->setGroup($adminGroup);
$t2->save();
echo $t2->getRank(); // 2

$t3 = new Task();
$t3->setTitle('Install servers');
$t3->setUser($john);
$t3->setGroup($adminGroup);
$t3->save()
echo $t3->getRank(); // 1, because John has his own task list inside the admin-group

$t4 = new Task();
$t4->setTitle('Manage content');
$t4->setUser($john);
$t4->setGroup($userGroup);
$t4->save()
echo $t4->getRank(); // 1, because John has his own task list inside the user-group

```

The generated methods now accept one parameter per scoped column, to restrict the query to a given scope:

```php
<?php
$firstPaulAdminTask = TaskQuery::create()->findOneByRank($rank = 1, $userIdScope = $paul->getId(), $groupIdScope = $adminGroup->getId()); // $t1
$lastPaulTask = $firstTask->getNext();      // $t2
$firstJohnUserTask = TaskPeer::create()->findOneByRank($rank = 1, $userIdScope = $john->getId(), $groupIdScope = $userGroup->getId()); // $t4
```

Models using the sortable behavior with scope benefit from one additional Query method named `inList()`:

```php
<?php
$allJohnsUserTasks = TaskPeer::create()->inList($userIdScope = $john->getId(), $groupIdScope = $userGroup->getId())->find();
```

## Parameters ##

By default, the behavior adds one columns to the model - two if you use the scope feature. If these columns are already described in the schema, the behavior detects it and doesn't add them a second time. The behavior parameters allow you to use custom names for the sortable columns. The following schema illustrates a complete customization of the behavior:

```xml
<table name="task">
  <column name="id" required="true" primaryKey="true" autoIncrement="true" type="INTEGER" />
  <column name="title" type="VARCHAR" required="true" primaryString="true" />
  <column name="my_rank_column" required="true" type="INTEGER" />
  <column name="user_id" required="true" type="INTEGER" />
  <foreign-key foreignTable="user" onDelete="cascade">
    <reference local="user_id" foreign="id" />
  </foreign-key>
  <behavior name="sortable">
    <parameter name="rank_column" value="my_rank_column" />
    <parameter name="use_scope" value="true" />
    <parameter name="scope_column" value="user_id" />
  </behavior>
</table>
```

Whatever name you give to your columns, the `sortable` behavior always adds the following proxy methods, which are mapped to the correct column:

```php
<?php
$task->getRank();         // returns $task->my_rank_column
$task->setRank($rank);
$task->getScopeValue();   // returns $task->user_id
$task->setScopeValue($scope);
```

The same happens for the generated Query object:

```php
<?php
$query = TaskQuery::create()->filterByRank();  // proxies to filterByMyRankColumn()
$query = TaskQuery::create()->orderByRank();   // proxies to orderByMyRankColumn()
$tasks = TaskQuery::create()->findOneByRank(); // proxies to findOneByMyRankColumn()
```

>**Tip**<br />The behavior adds columns but no index. Depending on your table structure, you might want to add a column index by hand to speed up queries on sorted lists.

## Complete API ##

Here is a list of the methods added by the behavior to the model objects:

```php
<?php
// storage columns accessors
int     getRank()
$object setRank(int $rank)
// only for behavior with use_scope
int     getScopeValue()
$object setScopeValue(int $scope)

// inspection methods
bool    isFirst()
bool    isLast()

// list traversal methods
$object getNext()
$object getPrevious()

// methods to insert an object in the list (require calling save() afterwards)
$object insertAtRank($rank)
$object insertAtBottom()
$object insertAtTop()

// methods to move an object in the list (immediate, no need to save() afterwards)
$object moveToRank($rank)
$object moveUp()
$object moveDown()
$object moveToTop()
$object moveToBottom()
$object swapWith($object)

// method to remove an object from the list (requires calling save() afterwards)
$object removeFromList()
```

Here is a list of the methods added by the behavior to the query objects:

```php
<?php
query   filterByRank($order, $scope = null)
query   orderByRank($order, $scope = null)
$object findOneByRank($rank, $scope = null)
coll    findList($scope = null)
int     getMaxRank($scope = null)
bool    reorder($newOrder) // $newOrder is a $id => $rank associative array
// only for behavior with use_scope
array   inList($scope)
```

The behavior also adds a few methods to the Peer classes:

```php
<?php
int     getMaxRank($scope = null)
$object retrieveByRank($rank, $scope = null)
array   doSelectOrderByRank($order, $scope = null)
bool    reorder($newOrder) // $newOrder is a $id => $rank associative array
// only for behavior with use_scope
array   retrieveList($scope)
int     countList($scope)
int     deleteList($scope)
```
