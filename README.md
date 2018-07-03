Jasny Entity
========

[![Build Status](https://travis-ci.org/jasny/entity.svg?branch=master)](https://travis-ci.org/jasny/entity)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/jasny/entity/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/jasny/entity/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/jasny/entity/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/jasny/entity/?branch=master)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/a1a1745c-1272-46a3-9567-7bbb52acda5a/mini.png)](https://insight.sensiolabs.com/projects/a1a1745c-1272-46a3-9567-7bbb52acda5a)
[![BCH compliance](https://bettercodehub.com/edge/badge/jasny/entity?branch=master)](https://bettercodehub.com/)
[![Packagist Stable Version](https://img.shields.io/packagist/v/jasny/entity.svg)](https://packagist.org/packages/jasny/entity)
[![Packagist License](https://img.shields.io/packagist/l/jasny/entity.svg)](https://packagist.org/packages/jasny/entity)


## Documentation

An entity is a "thing" you want to represent in a database or other data stores. It can be a new article on your blog,
a user in your message board or a permission in your rights management system.

The properties of an entity object is a representation of persisted data.

### Instantiation
Using the `new` keyword is reserved for creating a new entity.

When the data of an entity is fetched, the `__set_state()` method is used to create the entity. This method sets the
properties of the entity object before calling the constructor.

#### Lazy loading

Entities supports [lazy loading](http://en.wikipedia.org/wiki/Lazy_loading) of entities by allowing them to be created
as ghost. A ghost only hold the identifier. When other properties are accessed it can load the rest of the data.

When a scalar value is [casted](#metacast) to an entity, a ghost of that entity is created.


### Set values
The `set()` method is a a helper function for setting all the properties from an array and works like a
[fluent interface](http://en.wikipedia.org/wiki/Fluent_interface).

```php
$foo = new Foo();
$foo->set('answer', 42);
$foo->set(['red' => 10, 'green' => 20, 'blue' => 30]);

$foo
  ->set('destination', 'unknown')
  ->doSomething();
```

#### Dynamic

By default, values that are not defined in the entity class are ignored when setting the properties of the entity.
Override the `isDynamic()` method to return `true` for the entity to allow undefined properties.  

### Get values
Properties SHOULD be declared public and may be accessed directly, especially for reading.

#### toAssoc
To cast an entity to an associated array, use the `toAssoc()` method.

#### jsonSerialize
Entities implement the `JsonSerializable` interface. When calling `json_serialize($entity)`, the `jsonSerialize()`
method is automatically called. If will create a `stdClass` object with casted properties.

#### Identifiable

An entity class is marked as identifiable if it has an identifier property. By default we assume this property is
called `id`. In order to select another property, overwrite the `getIdProperty()` method.

You cat get the identity of the entity with the `getId()` method.  


### Metadata

An entity represents an element in the model. The [metadata](http://en.wikipedia.org/wiki/Metadata) holds 
information about the structure of the entity. Metadata should be considered static as it describes all the
entities of a certain type.

Metadata for a class might contain the table name where data should be stored. Metadata for a property might 
contain the data type, whether or not it is required and the property description.

Jasny DB support defining metadata through annotations by using [Jasny\Meta](http://www.github.com/jasny/meta).

```php
/**
 * Foo entity
 *
 * @entitySet FooSet
 */
class Foo
{
   /**
    * @var string
    * @required
    */
   public $name;
}
```

#### Property annotations

    * @var - (type casting) - Value type or class name
    * @type - (validation) - Value (sub)type
    * @required (validation) - Value should not be blank at validation.
    * @min (validation) - Minimal value
    * @max (validation) - Maximal value
    * @minLength (validation) - Minimal length of a string
    * @maxLength (validation) - Maximal length of a string
    * @options _values_ (validation) - Value should be one the the given options.
    * @pattern _regex_ (validation) - Value should match the regex pattern.
    * @immutable (validation †) - Property can't be changed after it is created.
    * @unique (validation †) - Entity should be unique accross it's dataset.
    * @unique _field_ (validation †) - Entity should be unique for a group. The group is identified by _field_.
    * @censor (filter) - Skip property when outputting the entity.
    * @skip (filter) - Skip property when storing the entity.

_† Requires support from the data gateway._

Additional annotation may be specified for both properties and the class.

#### Caveat
Metadata can be really powerful in generalizing and abstracting code. However you can quickly fall into the trap of
coding through metadata. This tends to lead to code that's hard to read and maintain.

Only use the metadata to abstract widely use functionality and use overloading to implement special cases.


### Triggers

Entities have a method to register handlers for a specific trigger. Triggers may be called from methods of the entity or
from outside services.

To call a trigger, specify the event type and (optionally) a payload. Events are typically named after the method that
triggers them. If there is a before and after event use the syntax `before:event` and `after:event`.

A handler must be a callback with the following signature:

    handler(EntityInterface $entity, mixed $payload): mixed

The handlers are executed in the order they are specified. The return value will be the payload for the subsequent
handler.

Entities have the following internal events

* `before:set` - Payload: input values
* `after:set`
* `before:reload` - Payload: input values
* `after:reload`
* `toAssoc` - Payload: output values
* `jsonSerialize` - Payload: output object
* `expand`
* `destruct`

#### MetaCast

Cast the values based on the [metadata](#metadata) of the entity based on the `@var` tag using the
[Jasny TypeCast](https://github.com/jasny/typecast) library.

This library has a custom type cast handler, that must be set for `EntityInterface` objects.

```php
$metaFactory = new Jasny\Meta\Factory\Annotations();

$handlers = [EntityInterface::class, new Entity\TypeCastHandler()] + TypeCast::getDefaultHandlers();
$typecast = new TypeCast($handlers);

$entity->on("before:set", new MetaCast($metaFactory, $typecast));
```  

#### MetaFilter

Filter values based on any tag of the metadata of the entity. Typically `@censor` is used to omit a property from output
and `@skip` is used to omit a property from being stored.

```php
$metaFactory = new Jasny\Meta\Factory\Annotations();
$entity->on("before:set", new MetaFilter('censor', $metaFactory));
$entity->on("before:save", new MetaFilter('skip', $metaFactory));
```

#### MetaValidation

Validate the entity based on the [metadata](#metadata). Validation may include checking that all required properties
have values, checking the variable type matches and checking if values are uniquely present in the database.

If validation fails 

```php
$metaFactory = new Jasny\Meta\Factory\Annotations();
$entity->on("before:save", new MetaValidation($metaFactory));

try {
    $entity->set($values)->save();
} catch (ValidationException $exception) {
    http_response_code(400); // Bad Request
    json_encode($exception->getErrors());
    exit();
}
```

You can also use the `MetaValidation` object directly. The `validate()` method will return a
[`Jasny\ValidationResult`](https://github.com/jasny/validation-result#readme) rather than throw an exception.

```php
$metaFactory = new Jasny\Meta\Factory\Annotations();
$validation = (new MetaValidation($metaFactory))->validate($entity);

if ($validation->failed()) {
    http_response_code(400); // Bad Request
    json_encode($validation->getErrors());
    exit();
}
```

#### Redact

Omit specified properties. This is useful both for input data as for output.

The Redact handler has 2 methods `without()` and `only()`. Using `without()` will omit the specified properties. Using
`only()` method will omit all properties except the ones specified.

```php
$entity->on("before:set", (new Redact())->without('id', 'credits'));
$entity->on("jsonSerialize", (new Redact())->only('id', 'type', 'description'));
```

_This is an immutable object, both `without()` and `only()` create a new object._
