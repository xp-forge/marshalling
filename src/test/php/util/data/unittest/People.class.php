<?php namespace util\data\unittest;

class People {

  /** @var util.data.unittest.Person[] */
  private $list;

  /** @param util.data.unittest.Person... $list */
  public function __construct(... $list) {
    $this->list= $list;
  }

  /** @return util.data.unittest.Person */
  public function all() { return $this->list; }
}