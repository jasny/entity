Jasny Entity
========

[![Build Status](https://travis-ci.org/jasny/entity.svg?branch=master)](https://travis-ci.org/jasny/entity)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/jasny/entity/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/jasny/entity/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/jasny/entity/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/jasny/entity/?branch=master)
[![Packagist Stable Version](https://img.shields.io/packagist/v/jasny/entity.svg)](https://packagist.org/packages/jasny/entity)
[![Packagist License](https://img.shields.io/packagist/l/jasny/entity.svg)](https://packagist.org/packages/jasny/entity)

An entity is a "thing" you want to represent in a database or other data stores. It can be a new article on your blog,
a user in your message board or a permission in your rights management system.

The properties of an entity object is a representation of persisted data.

## Installation

    composer require jasny/entity
    
## Usage

```php
namespace App;

use Jasny\Entity\IdentifiableEntityInterface;
use Jasny\Entity\IdentifiableEntityTraits;

/**
 * A user in our system
 */
class User extends AbstractIdentifiableEntity
{
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

A quick and dirty script to create and output the JSON of a `User` entity could be

```php
use App\User;
use Jasny\Entity\Event;

$data = $db->prepare("SELECT * FROM user WHERE id = ?")->execute($id)->fetch(PDO::FETCH_ASSOC);
$user = User::fromData($data);

$user->addEventListener(function(Event\ToJson $event): void {
    $data = $event->getPayload();
    unset($data['password_hash']);

    $event->setPaypload($data);
});

header('Content-Type: application\json');
echo json_serialize($user);
```

> _In this example you could just as well json serialize the data directly. The layer helps in adding abstraction to
applications that are beyond simple scripts._

## Documentation

### Basic entity

The `Entity` interface defines the methods all entities need to implement. To implement an entity you may extend the
`AbstractBasicEntity` base class. Alternatively you can use the [traits](#traits) of this library.

It's recommended to define properties as public, however only use them to get values and not to set. For setting values
use the `set()` method. This isn't enforced at runtime, but may be checked by a static code analyser like PHPStan.

```php
class Color extends AbstractBasicEntity
{
    /** @var int */
    public $red;
    
    /** @var int */
    public $green;
    
    /** @var int */
    public $blue;
}
```

### Identifiable entity

If an entity has a unique identifier, the class should implement `IdentifiableEntity`.

```php
class User extends AbstractIdentifiableEntity
{
    /** @var int */
    public $id;
    
    /** @var string */
    public $name;
    
    /** @var string */
    public $email;
    
    /** @var string */
    public $password_hash;
}
```

It's assumed you're using property `id` as a surrogate key. If you're using a differently property, make sure to
overwrite the static `getIdProperty()` method.  

```php
class Invoice extends AbstractIdentifiableEntity
{
    /** @var string */
    public $number;
    
    // ...
    
    protected static function getIdProperty(): string
    {
        return 'number';
    }
}
```

### Dynamic entity

By default entities should only have the properties that are specified by the class. The `set()` method will ignore
all values that don't correspond with any property. If for some reason additional properties are added, the `toAssoc()`
and `jsonSerialize()` methods, also ignore properties that aren't defined in the class.

In some cases an entity might be dynamic; it can have properties that are added at runtime. Some data stores like
`MongoDB` have dynamic schemas, which don't have to be defined at forehand, to support this.

To indicate that an entity may have dynamic properties it should implement the `DynamicEntity` interface. 

```php
class User extends AbstractIdentifiableEntity implements DynamicEntity
{
    // ...
}
```

### New entity

Using the `new` keyword is reserved for creating a new entity.

```php
$user = new User(); // This represents a new user in the system
```

If you set the identified (`id` property) of a new entity, it will either overwrite it or throw an duplicate id error,
depending on the data storage implementation. However it will (or rather should) not update the existing record.

The `isNew()` method will tell if it's a new user or if it's loaded from data.

### Existing entity

When the data of an entity is fetched, the static `fromData()` method is used to create the entity.

```php
$data = $db->prepare("SELECT * FROM user WHERE id = ?")->execute($id)->fetch(PDO::FETCH_ASSOC);
$user = User::fromData($data);
```

The `fromData()` method sets the properties of the entity object, _before_ calling the constructor.

##### var_export
The `__set_state()` method is set as alias of `fromData()`, allowing entities to be serialized via
[`var_export`](https://php.net/var_export) and stored as PHP script. Other libraries like
[Jasny Typecast](https://github.com/jasny/typecast), rely on the `__set_state()` method as well.

### Set values

The `set()` method is a a helper function for setting all the properties from an array.

```php
$foo = new Foo();
$foo->set('answer', 42);
$foo->set(['red' => 10, 'green' => 20, 'blue' => 30]);
```

It can be use as [fluent interface](http://en.wikipedia.org/wiki/Fluent_interface).

```php
$adventure = (new Adventure)
  ->set('destination', 'unknown')
  ->set('duration', '1 year')
  ->go();
```

The `set()` method triggers 2 events; [`BeforeSet`](#entity-events) and [`AfterSet`](#entity-events).  

### Same entities

Check if two entities are the same using the `is()` method which returns a boolean. The method always returns `true` in
case the objects are the same object (similar to `===`).

For identifiable objects, the method will also return `true` is the entity class and the `id` value are the same. The
value of other properties are disregarded.

### Cast to associative array

Cast an entity to an associative array with the `toAssoc()` method. By default this method will return the values of all
public properties.

```php
$data = $user->toAssoc();
```

The [`ToAssoc`](#entity-events) event is available to modify the result of this method. The library comes with the
`ToAssocRecursive` event listener, which will also turn child entities into associative arrays.

```php
$user->addEventListener(function(Event\ToAssoc $event): void {
    $assoc = $event->getPayload();
    
    if (isset($assoc['password'])) {
        $assoc['password_hashed'] = password_hash($assoc['password'], PASSWORD_DEFAULT);
        unset($assoc['password_hashed']);
    }
    
    $event->setPayload($assoc);
});
```

> _The `toAssoc()` method can be used in farious places. It's not recommended to create event listeners to handle a
specific use case. Instead create a new type of event for that specific use._

### Cast to JSON

Entities must implement `JsonSerializable`, meaning they can be casted to JSON via
[`json_encode()`](https://php.net/json_encode). By default, the result is an object with all the public properties of
the entity.

The `jsonSerialize` method can be overwritten in the entity class. Alternatively the [`ToJson`](#entity-events) event
can be used to modify the result before it's serialized to a json string.  

```php
$user->addEventListener(function(Event\ToJson $event): void {
    $assoc = $event->getPayload();
    unset($assoc['password_hashed']);
    
    $event->setPayload($assoc);
});
```

The library contains the `JsonCast` event listener that will convert `DateTime` objects to a date string and will
convert any (child) object that implements `JsonSerializable`.

### Persisting entities

This library **does not** have any methods for saving entities into persistent storage (like a database).

It recommended to implement data gateway services for this (and not adopt Active Record pattern).

```php
class UserGateway
{
    /** @var \PDO */
    protected $db;

    public function __construct(\PDO $db)
    {
        $this->db = $db;
    }

    public function load(string $id): User
    {
        $data = $db->prepare("SELECT * FROM user WHERE id = ?")->execute($id)->fetch(PDO::FETCH_ASSOC);
        
        if ($data === null) {
            throw new RuntimeException("User `$id` not found");
        }
        
        return User::fromData($data);
    }
    
    public function save(User $user): void
    {
        $data = $user->toAssoc();
        
        $columns = join(', ', array_keys($data));             // "id, name, email, password_hash"
        $placeholders = ':' . join(', :', array_keys($data)); // ":id, :name, :email, :password_hash"
        
        $db->prepare("REPLACE INTO users ($columns) VALUES ($placeholders)")->execute();
        
        $user->markAsPersisted();
    }
}
```

> _The example always does a `REPLACE` query, but you could do an `UPDATE` query if `isNew()` returns `false` instead._

After an entity is saved, the gateway should call the `markAsPersisted` method, which will trigger an event and mark the
entity as no longer being new (for `isNew()`).

If you're using a auto-generated identifier, you should retrieve it from the db layer and directly set the `id` property
prior to calling `marktAsPersisted()`.

### Events

Entities may support events through a [PSR-14 compatible](https://www.php-fig.org/psr/psr-14/) event dispatcher. This
allows additional abstraction for different services and is important when 
 
Before you can add event listener, you need to register an event dispatcher. The entity doesn't create one itself.

```php
use Jasny\Entity\Event;
use Jasny\Entity\EventListener\JsonCast;
use Jasny\EventDispatcher\EventDispatcher;

$listener = (new ListenerProvider)
    ->withListener(function(Event\Serialize $event): void {
        $assoc = $event->getPayload();
        
        if (isset($assoc['password'])) {
            $assoc['password_hashed'] = password_hash($assoc['password'], PASSWORD_DEFAULT);
            unset($assoc['password_hashed']);
        }
        
        $event->setPayload($assoc);
    })
    ->withListener(new JsonCast());
    
$dispatcher = new EventDispatcher($listener);

$user = new User;
$user->setEventDispatcher($dispatcher);
```

Typically the event dispatcher is added to an entity by the gateway. This means that the gateway should also be used
when creating a new entity.

To add an event listener to an existing entity use the `addEventListener()` method of the entity.

```php
$user->addEventListener(function(Event\ToJson $event): void {
    $assoc = $event->getPayload();
    unset($assoc['password_hashed']);
    
    $event->setPayload($assoc);
});
```

> _Note that since adding event listeners isn't defined by the PSR-14 standard, the `addEventListener()` method only
works with [Jasny Event Dispatcher](https://github.com/jasny/event-dispatcher)._

The `dispatchEvent()` method takes an event and dispatches it to the listeners. It will return the passed event object,
which may be modified by the event listeners.

```php
$event = $user->dispatchEvent(new CustomEvent($user, $someData));
```

#### Event objects

An event can be any object. The event lister are filtered on the object class.

Event classes of this library take the `$entity` and `$payload` as constructor arguments. The `getEntity()` method will
return the emitting entity. You can get the payload using `getPayload()` and update it with `setPayload()`. The modified
event is passed to subsequent listeners and used by the method triggering the event.

#### Entity events

The library has the following events

* **BeforeSet** - Called by `set()`. Modifying the payload will effect the values that are set. This method
  can be used for casting the values to the correct type or filtering out properties that are not allowed to be changed
  manually.
* **AfterSet** - Called by `set()`, after updating the entity object. Modifying the payload has no effect.
* **Persisted** - Called by `markAsPersisted()`, which in turn should be called whenever the entity is saved to
  persistent storage like a DB.
* **ToAssoc** - Called by `toAssoc()`. Modifying the payload will affect the return value of this method.
* **ToJson** - Called by `jsonSerialize()`. Modifying the payload will affect the return value of this method.

#### Event listeners

* **ToAssocRecursive** - Recursively loop through all properties, also turning sub-entities into associative arrays. 
* **JsonCast** - Recursively loop through all properties, casting `DateTime` objects to date/time strings and
  getting the json data for `JsonSerializable` objects.
