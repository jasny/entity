Jasny Entity
========

[![Build Status](https://travis-ci.org/jasny/entity.svg?branch=master)](https://travis-ci.org/jasny/entity)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/jasny/entity/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/jasny/entity/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/jasny/entity/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/jasny/entity/?branch=master)
[![Packagist Stable Version](https://img.shields.io/packagist/v/jasny/entity.svg)](https://packagist.org/packages/jasny/entity)
[![Packagist License](https://img.shields.io/packagist/l/jasny/entity.svg)](https://packagist.org/packages/jasny/entity)


## Installation

    composer require jasny/entity
    
## Usage

```php
namespace App;

use Jasny\Entity\IdentifiableEntity;
use Jasny\Entity\IdentifiableEntityTraits;

/**
 * A user in our system
 */
class User implements IdentifiableEntity
{
    use IdentifiableEntityTraits;
    
    /** @var string */
    public $id;
    
    /** @var string */
    public $name;
    
    /** @var string */
    public $email;
    
    /** @var string */
    public $password_hash;
}
```

A quick and dirty script to create and output the JSON of a User entity could be

```php
use App\User;

$data = fetch_user_from_db($id);
$user = User::__set_state($data);

header('Content-Type: application\json');
echo json_serialize($user);
```

## Documentation

An entity is a "thing" you want to represent in a database or other data stores. It can be a new article on your blog,
a user in your message board or a permission in your rights management system.

The properties of an entity object is a representation of persisted data.

### Instantiation
Using the `new` keyword is reserved for creating a new entity.

When the data of an entity is fetched, the `__set_state()` method is used to create the entity. This method sets the
properties of the entity object before calling the constructor.

#### Stubs
Entities supports [lazy loading](http://en.wikipedia.org/wiki/Lazy_loading) of entities by allowing them to be created
as stub that only holds the `id` of the entity using the static `fromId()` method.

The `refresh` method can be used to update/expand the stub entities from fetched data.

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
called `id`. In order to select another property, overwrite the static `getIdProperty()` method.

You cat get the identity of the entity with the `getId()` method.  

### Events

Entities have a method to register handlers for a specific trigger. Triggers may be called from methods of the entity or
from outside services.

To call a trigger, specify the event type and (optionally) a payload. Events are typically named after the method that
triggers them. If there is a before and after event use the syntax `before:event` and `after:event`.

A handler must be a callback with the following signature:

    handler(EntityInterface $entity, mixed $payload): mixed

The handlers are executed in the order they are specified. The return value will be the payload for the subsequent
handler.

Entities have the following internal events
