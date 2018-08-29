<?php namespace util\data\unittest;

use lang\Type;
use unittest\TestCase;
use util\data\Marshalling;

class MarshallingTest extends TestCase {

  #[@test]
  public function can_create() {
    new Marshalling();
  }

  #[@test]
  public function marshal() {
    $this->assertEquals(1, (new Marshalling())->marshal(1));
  }

  #[@test, @values(['var', Type::$VAR])]
  public function unmarshal($type) {
    $this->assertEquals(1, (new Marshalling())->unmarshal(1, $type));
  }
}