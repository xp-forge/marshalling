Marshalling change log
======================

## ?.?.? / ????-??-??

## 2.2.0 / 2024-03-24

* Made compatible with XP 12 - @thekid

## 2.1.0 / 2024-01-09

* Added PHP 8.4 to test matrix - @thekid
* Merged PR #29: Keep empty objects intact - @thekid

## 2.0.0 / 2023-09-24

The second major release changes the way scalars are unmarshalled to
arrays: Previously, they resulted in an empty array and a PHP warning
being raised. Now, they result in an array of themselves.

* Merged PR #4: Unmarshal scalars and null to arrays - @thekid
* Merged PR #5: Migrate to new reflection API - @thekid
* Merged PR #3: Migrate to new testing library - @thekid

## 1.1.1 / 2021-10-21

* Made library compatible with XP 11 - @thekid
* Made compatible with PHP 8.1 - add `ReturnTypeWillChange` attributes to
  iterator, see https://wiki.php.net/rfc/internal_method_return_types
  (@thekid)

## 1.1.0 / 2021-01-02

* Merged PR #1: Add support for `__serialize()` and `__unserialize()`
  (@thekid)

## 1.0.1 / 2021-01-02

* Only construct objects with one-arg constructors if their parameter
  accepts the argument
  (@thekid)

## 1.0.0 / 2019-12-01

The first major release changes no functionality, but drops support for
PHP 5, which has been EOL for almost a year at the time of this release.

* Implemented xp-framework/rfc#334: Drop PHP 5.6. The minimum required
  PHP version is now 7.0.0!
  (@thekid)

## 0.3.2 / 2019-12-01

* Made compatible with XP 10 - @thekid

## 0.3.1 / 2019-02-11

* Fixed shadowing of `util.data.Iterator` class from xp-forge/sequence
  (@thekid)

## 0.3.0 / 2019-01-22

* Added support for `util.XPIterator` instances in marshalling
  (@thekid)

## 0.2.0 / 2018-08-29

* Fixed `unmarshal()` when given value is an instance of an enum
  (@thekid)

## 0.1.0 / 2018-08-29

* Hello World! First release - @thekid