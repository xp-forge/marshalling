<?php namespace util\data\unittest;

use lang\Type;
use unittest\TestCase;
use util\data\Marshalling;

class MarshallingTest extends TestCase {

  #[@test]
  public function can_create() {
    new Marshalling();
  }
}