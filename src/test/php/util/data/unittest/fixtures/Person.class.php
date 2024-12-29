<?php namespace util\data\unittest\fixtures;

class Person {
  private static $ROOT = 0;

  /** @var int */
  private $id= 0;

  /** @var string */
  public $name;

  public function __construct($id, $name) {
    $this->id= $id;
    $this->name= $name;
  }

  public function id() { return $this->id; }
}