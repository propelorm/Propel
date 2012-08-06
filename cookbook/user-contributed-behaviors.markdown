---
layout: documentation
title: User-Contributed Behaviors
---

# User-Contributed Behaviors #

Here is a list of Propel behaviors contributed by users. Feel free to use them or to add your own behaviors to this list.


## Agnostic Behaviors ##

* [EqualNestBehavior](http://github.com/CraftyShadow/EqualNestBehavior) Supports relations where a class references to itself and the columns within the reference class are equal. Suitable for "Friends"-like relationships.

* [ArrayAccessBehavior](http://github.com/nnarhinen/propel-arrayaccess) Adds ArrayAccess implementations to the BaseObjects.

* [GeocodableBehavior](./geocodable-behavior.html) Helps you build geo-aware applications.

* [ExtraPropertiesBehavior](https://github.com/Carpe-Hora/ExtraPropertiesBehavior) Extend your models with key/value attributes.

* [AuditableBehavior](https://github.com/Carpe-Hora/AuditableBehavior) Audit your objects activity.

* [TypehintableBehavior](https://github.com/willdurand/TypehintableBehavior) Insane Propel behavior that helps you to be compliant with third-party interfaces by adding type hint to generated methods.

* [PropelListenerBehavior](https://github.com/gossi/propel-listener-behavior) Add listeners to propel generated objects.

* [EventDispatcherBehavior](https://github.com/willdurand/EventDispatcherBehavior) Integrates the Symfony2 EventDispatcher component in your Model classes.

* [StateMachineBehavior](https://github.com/willdurand/StateMachineBehavior) Adds a finite state machine to your model.

* [PublishableBehavior](https://github.com/willdurand/PublishableBehavior) Designed to quickly add publish/unpublish features to your model.

* [MultipleAggregateColumnBehavior](https://github.com/natecj/PropelMultipleAggregateColumnBehavior) A replacement to aggregate_column behavior that allows multiple aggregate columns on a single table.


## symfony 1.x Behaviors ##

* [sfNestedCommentPlugin](https://github.com/nibsirahsieu/sfNestedCommentPlugin) A behavior for propel 1.5 and symfony to enabled the model(s) to be commentable.

* [sfPropelActAsBlameableBehaviorPlugin](https://github.com/ArnaudD/sfPropelActAsBlameableBehaviorPlugin) Symfony plugin for Propel 1.5 to autofill created_by, updated_by and deleted_by columns?-.

* [sfPropelLuceneableBehaviorPlugin](https://github.com/nibsirahsieu/sfPropelLuceneableBehaviorPlugin) A behavior for propel 1.6 and symfony 1.4 to enabled the model(s) to be searchable.

* [sfPropelObjectPathBehaviorPlugin](http://www.symfony-project.org/plugins/sfPropelObjectPathBehaviorPlugin) Simplifies joining and handling relations deeper than one level. (Not Symfony specific)

* [sfPropelORMTaggableBehaviorPlugin](https://bitbucket.org/matteosister/sfpropelormtaggablebehaviorplugin) A behavior and a widget for propel 1.6 and symfony 1.4.x to tag your objects.

* [sfPropelORMRatableBehaviorPlugin](https://bitbucket.org/matteosister/sfpropelormratablebehaviorplugin)  A behavior for propel 1.6 and symfony 1.4.x to rate your objects.

* [sfContextBehavior](https://github.com/Carpe-Hora/sfContextBehavior) A behavior for propel 1.6 and symfony 1.x rendering object context aware.

* [ncPropelChangeLogBehaviorPlugin](https://github.com/CraftyShadow/ncPropelChangeLogBehaviorPlugin) Provides a Behavior for Propel objects that allows you to track any changes made to them. 


## Symfony2 2.x Behaviors ##

* [PropelEventBundle](https://bitbucket.org/glorpen/glorpenpropeleventbundle) Add a way of using Symfony2 DIC in Propel model classes through events.

* [TaggableBehaviorBundle](https://bitbucket.org/glorpen/taggablebehaviorbundle) A behavior and a widget for propel 1.6 and Symfony2.0 to tag your objects.

* [TaggableBehaviorBundle](https://github.com/vbardales/PropelTaggableBehaviorBundle.git) A behavior for propel 1.6 and Symfony2.1 to tag your objects, supporting i18n.