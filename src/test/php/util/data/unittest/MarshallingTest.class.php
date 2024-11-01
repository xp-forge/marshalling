<?php namespace util\data\unittest;

use lang\Type;
use test\{Assert, Test, Values};
use util\data\Marshalling;
use util\data\unittest\fixtures\Person;

class MarshallingTest {

  #[Test]
  public function can_create() {
    new Marshalling();
  }

  #[Test]
  public function marshal() {
    Assert::equals(1, (new Marshalling())->marshal(1));
  }

  #[Test]
  public function objects() {
    Assert::equals((object)[], (new Marshalling())->marshal((object)[]));
  }

  #[Test, Values(eval: '["var", Type::$VAR]')]
  public function unmarshal($type) {
    Assert::equals(1, (new Marshalling())->unmarshal(1, $type));
  }

  #[Test]
  public function unmarshal_without_type() {
    Assert::equals(1, (new Marshalling())->unmarshal(1));
  }

  #[Test]
  public function mapping() {
    $marshalling= (new Marshalling())->mapping(
      Person::class,
      fn($person) => ['id' => $person->id()]
    );

    Assert::equals(['id' => 6100], $marshalling->marshal(new Person(6100, 'Test')));
  }

  #[Test]
  public function resolving() {
    $marshalling= (new Marshalling())->resolving(
      Person::class,
      fn($value) => new Person($value['id'], 'Test')
    );

    Assert::equals(new Person(6100, 'Test'), $marshalling->unmarshal(['id' => 6100], Person::class));
  }
}