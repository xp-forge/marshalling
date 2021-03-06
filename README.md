Marshalling
========================================================================

[![Build status on GitHub](https://github.com/xp-forge/marshalling/workflows/Tests/badge.svg)](https://github.com/xp-forge/marshalling/actions)
[![XP Framework Module](https://raw.githubusercontent.com/xp-framework/web/master/static/xp-framework-badge.png)](https://github.com/xp-framework/core)
[![BSD Licence](https://raw.githubusercontent.com/xp-framework/web/master/static/licence-bsd.png)](https://github.com/xp-framework/core/blob/master/LICENCE.md)
[![Requires PHP 7.0+](https://raw.githubusercontent.com/xp-framework/web/master/static/php-7_0plus.svg)](http://php.net/)
[![Supports PHP 8.0+](https://raw.githubusercontent.com/xp-framework/web/master/static/php-8_0plus.svg)](http://php.net/)
[![Latest Stable Version](https://poser.pugx.org/xp-forge/marshalling/version.png)](https://packagist.org/packages/xp-forge/marshalling)

Marshalling converts objects to maps and vice versa.

Example
-------
All primitives, arrays and maps thereof are yielded as-is:

```php
use util\data\Marshalling;

$m= new Marshalling();
$m->marshal('Test');            // "Test"
$m->marshal([1, 2, 3]);         // [1, 2, 3]
$m->marshal(['admin' => true]); // ["admin" => true]
```

Value objects are marshalled using field => getter lookups; supporting both *method named for field* and *get[Field]()* conventions.

```php
use util\data\Marshalling;

class Person {
  private $name, $age;

  public function __construct(string $name, int $age) {
    $this->name= $name;
    $this->age= $age;
  }

  public function name(): string { return $this->name; }
  public function age(): int { return $this->age; }
}

$m= new Marshalling();
$m->marshal(new Person('...', 42)); // ["name" => "...", "age" => 42]
```

When unmarshalling from maps, pass the type as second parameter. Objects are created without invoking the constructor, and by either setting the fields directly or by using the *set[Field]()* convention.

```php
use util\data\Marshalling;

$m= new Marshalling();
$person= $m->unmarshal(['name' => '...', 'age' => 42], Person::class);
```

Types from the "util" package are handled:

```php
use util\data\Marshalling;
use util\{Date, Bytes, Money};

$m= new Marshalling();
$m->marshal(Date::now());                   // "2018-08-29T10:40:49+0200" (ISO 8601)
$m->marshal(new Bytes("\x50\x4b\x03\x04")); // "UEsDBA==" (base64)
$m->marshal(new Money(12.99, $currency));   // ["amount" => 12.99, "currency" => "EUR"]
```

See also:

* https://stackoverflow.com/questions/1443158/binary-data-in-json-string-something-better-than-base64
* https://stackoverflow.com/questions/30249406/what-is-the-standard-for-formatting-currency-values-in-json