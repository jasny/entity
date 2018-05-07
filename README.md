Jasny DB
========

[![Build Status](https://travis-ci.org/jasny/entity.svg?branch=master)](https://travis-ci.org/jasny/entity)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/jasny/entity/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/jasny/entity/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/jasny/entity/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/jasny/entity/?branch=master)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/a1a1745c-1272-46a3-9567-7bbb52acda5a/mini.png)](https://insight.sensiolabs.com/projects/a1a1745c-1272-46a3-9567-7bbb52acda5a)
[![BCH compliance](https://bettercodehub.com/edge/badge/jasny/entity?branch=master)](https://bettercodehub.com/)
[![Packagist Stable Version](https://img.shields.io/packagist/v/jasny/entity.svg)](https://packagist.org/packages/jasny/entity)
[![Packagist License](https://img.shields.io/packagist/l/jasny/entity.svg)](https://packagist.org/packages/jasny/entity)

## Documentation

Entity
---

An entity is a "thing" you want to represent in a database or other data storages. It can be a new article on your
blog, a user in your message board or a permission in your rights management system.

The properties of an entity object is a representation of the data. Entities usually also carry business logic.

### Set values
The `setValues()` methods is a a helper function for setting all the properties from an array and works like a
[fluent interface](http://en.wikipedia.org/wiki/Fluent_interface).

```php
$foo = new Foo();
$foo->setValues(['red' => 10, 'green' => 20, 'blue' => 30])->doSomething();
```

### Instantiation
Using the `new` keyword is reserved for creating a new entity.

When the data of an entity is fetched, the `__set_state()` method is used to create the entity. This method sets the
properties of the entity object before calling the constructor.

Entity set
---

Whenever an array of entities would be returned, Jasny DB will return an `EntitySet` object instead. An entity set
can be used as array as well as object.

_further documentation required_


Metadata
---

An entity represents an element in the model. The [metadata](http://en.wikipedia.org/wiki/Metadata) holds 
information about the structure of the entity. Metadata should be considered static as it describes all the
entities of a certain type.

Metadata for a class might contain the table name where data should be stored. Metadata for a property might 
contain the data type, whether or not it is required and the property description.

Jasny DB support defining metadata through annotations by using [Jasny\Meta](http://www.github.com/jasny/meta).

```php
/**
 * User entity
 *
 * @entitySet UserSet
 */
class User
{
   /**
    * @var string
    * @required
    */
   public $name;
}
```

### Class annotations

    * @entitySet - Default entity set for this class of Entities

_Additional class annotations may be used by a specific Jasny DB driver._

### Property annotations

    * @var - (type casting) - Value type or class name
    * @type - (validation) - Value (sub)type
    * @required (validation) - Value should not be blank at validation.
    * @min (validation) - Minimal value
    * @max (validation) - Maximal value
    * @minLength (validation) - Minimal length of a string
    * @maxLength (validation) - Maximal length of a string
    * @options _values_ (validation) - Value should be one the the given options.
    * @pattern _regex_ (validation) - Value should match the regex pattern.
    * @immutable (validation) - Property can't be changed after it is created.
    * @unique (validation) - Entity should be unique accross it's dataset.
    * @unique _field_ (validation) - Entity should be unique for a group. The group is identified by _field_.
    * @censor (redact) - Skip property when outputting the entity.

_Additional property annotations may be used by a specific Jasny DB driver._

### Caveat
Metadata can be really powerfull in generalizing and abstracting code. However you can quickly fall into the trap of
coding through metadata. This tends to lead to code that's hard to read and maintain.

Only use the metadata to abstract widely use functionality and use overloading to implement special cases.


Type casting
---

Entities support type casting. This is done based on the metadata. Type casting is implemented by the
[Jasny\Meta](http://www.github.com/jasny/meta) library.

### Internal types
For [php internal types](http://php.net/types) normal [type juggling](http://php.net/type-juggling) is used. Values
aren't blindly casted. For instance casting `"foo"` to an integer would trigger a warning and skip the casting.

### Objects
Casting a value to an `Identifiable` entity that supports [Lazy Loading](#lazy-loading), creates a ghost object.
Entities that implement `ActiveRecord` or have a `DataMapper`, but do not support `LazyLoading` are fetched from the
database.

Casting a value to a non-identifiable entity will call the `Entity::fromData()` method.

Casting to any other type of object will create a new object normally. For instance casting "bar" to `Foo` would result
in `new Foo("bar")`.


Validation
---

Entities implementing the Validatable interface, can do some basic validation prior to saving them. This includes
checking that all required properties have values, checking the variable type matches and checking if values are
uniquely present in the database.

The `validate()` method will return a [`Jasny\ValidationResult`](https://github.com/jasny/validation-result#readme).

```php
$validation = $entity->validate();

if ($validation->failed()) {
    http_response_code(400); // Bad Request
    json_encode($validation->getErrors());
    exit();
}
```

Lazy loading
---

Jasny DB supports [lazy loading](http://en.wikipedia.org/wiki/Lazy_loading) of entities by allowing them to be created
as ghost. A ghost only hold a limited set of the entity's data, usually only the identifier. When other properties are
accessed it will load the rest of the data.

When a value is [casted](#type-casting) to an entity that supports lazy loading, a ghost of that entity is created.


Soft deletion
---

Entities that support soft deletion are deleted in such a way that they can restored.

Deleted entities may restored using `undelete()` or they can be permanently removed using `purge()`.

The `isDeleted()` method check whether this document has been deleted.

