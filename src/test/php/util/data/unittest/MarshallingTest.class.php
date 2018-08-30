<?php namespace util\data\unittest;

use lang\Type;
use lang\Value;
use unittest\TestCase;
use util\data\Marshaller;
use util\data\Marshalling;
use util\data\unittest\fixtures\Person;

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

  #[@test]
  public function unmarshal_without_type() {
    $this->assertEquals(1, (new Marshalling())->unmarshal(1));
  }

  #[@test]
  public function overwriting_marshalling() {
    $m= new Marshalling();
    $m->add(Person::class, newinstance(Marshaller::class, [], [
      'marshal'   => function($value, $marshalling) { return ['id' => $value->id()]; },
      'unmarshal' => function($value, $type, $marshalling) { /* TBI */ }
    ]));

    $this->assertEquals(['id' => 6100], $m->marshal(new Person(6100, 'Test')));
  }

  #[@test]
  public function overwriting_unmarshalling() {
    $m= new Marshalling();
    $m->add(Person::class, newinstance(Marshaller::class, [], [
      'marshal'   => function($value, $marshalling) { /* TBI */ },
      'unmarshal' => function($value, $type, $marshalling) { return new Person($value['id'], 'Test'); }
    ]));

    $this->assertEquals(new Person(6100, 'Test'), $m->unmarshal(['id' => 6100], Person::class));
  }
}