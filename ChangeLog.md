Marshalling change log
======================

## ?.?.? / ????-??-??

## 1.1.0 / 2021-01-02

* Merged PR #1: Add support for `__serialize()` and `__unserialize()`
  (@thekid)

## 1.0.1 / 2021-01-02

* Only construct objects with one-arg constructors if their parameter
  accepts the argument
  (@thekid)

## 1.0.0 / 2019-12-01

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