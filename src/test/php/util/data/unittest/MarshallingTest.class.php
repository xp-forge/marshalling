<?php namespace util\data\unittest;

use lang\Type;
use test\{Assert, Test, Values};
use util\data\Marshalling;
use util\data\unittest\fixtures\{Date, Person};

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
  public function explicit_null() {
    Assert::equals(null, (new Marshalling())->unmarshal(['id' => null], Person::class)->id());
  }

  #[Test]
  public function nullable() {
    Assert::equals(null, (new Marshalling())->unmarshal(null, '?util.data.unittest.fixtures.Date'));
  }

  #[Test, Values([1609619853, '2021-01-02T21:37:33+01:00'])]
  public function nullable_instance($input) {
    Assert::equals(new Date($input), (new Marshalling())->unmarshal($input, '?util.data.unittest.fixtures.Date'));
  }

  #[Test]
  public function mapping() {
    $marshalling= (new Marshalling())->mapping(
      Person::class,
      function($person) { return ['id' => $person->id()]; }
    );

    Assert::equals(['id' => 6100], $marshalling->marshal(new Person(6100, 'Test')));
  }

  #[Test]
  public function resolving() {
    $marshalling= (new Marshalling())->resolving(
      Person::class,
      function($value) { return new Person($value['id'], 'Test'); }
    );

    Assert::equals(new Person(6100, 'Test'), $marshalling->unmarshal(['id' => 6100], Person::class));
  }
}