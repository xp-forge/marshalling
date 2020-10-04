<?php namespace util\data\unittest\fixtures;

class PersonWithoutConstructor {

  /** @var int */
  public $id;

  /** @var string */
  public $name;

  /**
   * Sets ID
   *
   * @param  int $id
   * @return self
   */
  public function setId($id) {
    $this->id= $id;
    return $this;
  }

  /**
   * Sets name
   *
   * @param  string $name
   * @return self
   */
  public function setName($name) {
    $this->name= $name;
    return $this;
  }
}
