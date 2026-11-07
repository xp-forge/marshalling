<?php namespace util\data\unittest\fixtures;

use util\Money;

class Employee {
  public Person $person;
  public Money $salary;

  public function __construct(Person $person, Money $salary) {
    $this->person= $person;
    $this->salary= $salary;
  }
}