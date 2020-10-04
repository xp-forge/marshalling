<?php namespace util\data\unittest;

use lang\Type;
use unittest\{Test, TestCase, Values};
use util\data\Marshalling;

class MarshallingTest extends TestCase {

  #[Test]
  public function can_create() {
    new Marshalling();
  }

  #[Test]
  public function marshal() {
    $this->assertEquals(1, (new Marshalling())->marshal(1));
  }

  #[Test, Values(eval: '["var", Type::$VAR]')]
  public function unmarshal($type) {
    $this->assertEquals(1, (new Marshalling())->unmarshal(1, $type));
  }

  #[Test]
  public function unmarshal_without_type() {
    $this->assertEquals(1, (new Marshalling())->unmarshal(1));
  }
}