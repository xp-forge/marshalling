<?php namespace util\data\unittest\fixtures;

use util\Money;

class Employee {

  /** @type util.data.unittest.fixtures.Person */
  public $person;

  /** @type util.Money */
  public $salary;

  public function __construct(Person $person, Money $salary) {
    $this->person= $person;
    $this->salary= $salary;
  }
}