<?php namespace util\data\unittest;

use lang\Type;
use unittest\{Test, Assert, Values};
use util\data\Marshalling;

class MarshallingTest {

  #[Test]
  public function can_create() {
    new Marshalling();
  }

  #[Test]
  public function marshal() {
    Assert::equals(1, (new Marshalling())->marshal(1));
  }

  #[Test, Values(eval: '["var", Type::$VAR]')]
  public function unmarshal($type) {
    Assert::equals(1, (new Marshalling())->unmarshal(1, $type));
  }

  #[Test]
  public function unmarshal_without_type() {
    Assert::equals(1, (new Marshalling())->unmarshal(1));
  }
}