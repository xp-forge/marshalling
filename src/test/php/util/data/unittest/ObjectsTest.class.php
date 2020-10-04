<?php namespace util\data\unittest;

use lang\Type;
use unittest\{Test, TestCase, Values};
use util\data\Marshalling;
use util\data\unittest\fixtures\{Activity, People, Person, PersonWithoutConstructor};

class ObjectsTest extends TestCase {

  #[Test]
  public function marshal_person_value_object() {
    $this->assertEquals(
      ['id' => 6100, 'name' => 'Test'],
      (new Marshalling())->marshal(new Person(6100, 'Test'))
    );
  }

  #[Test]
  public function marshal_person_value_object_inside_map() {
    $this->assertEquals(
      ['person' => ['id' => 6100, 'name' => 'Test']],
      (new Marshalling())->marshal(['person' => new Person(6100, 'Test')])
    );
  }

  #[Test]
  public function marshal_person_value_object_inside_array() {
    $this->assertEquals(
      [['id' => 6100, 'name' => 'Test']],
      (new Marshalling())->marshal([new Person(6100, 'Test')])
    );
  }

  #[Test]
  public function marshal_value_object_in_value_object() {
    $this->assertEquals(
      ['list' => [['id' => 6100, 'name' => 'Test']]],
      (new Marshalling())->marshal(new People(new Person(6100, 'Test')))
    );
  }

  #[Test, Values([[['id' => 6100, 'name' => 'Test']], [['id' => '6100', 'name' => 'Test']],])]
  public function unmarshal_person_value($object) {
    $this->assertEquals(
      new Person(6100, 'Test'),
      (new Marshalling())->unmarshal($object, Person::class)
    );
  }

  #[Test]
  public function unmarshal_person_value_object_from_inside_map() {
    $type= Type::forName('[:util.data.unittest.fixtures.Person]');
    $this->assertEquals(
      ['person' => new Person(6100, 'Test')],
      (new Marshalling())->unmarshal(['person' => ['id' => 6100, 'name' => 'Test']], $type)
    );
  }

  #[Test]
  public function unmarshal_person_value_object_from_inside_array() {
    $type= Type::forName('util.data.unittest.fixtures.Person[]');
    $this->assertEquals(
      [new Person(6100, 'Test')],
      (new Marshalling())->unmarshal([['id' => 6100, 'name' => 'Test']], $type)
    );
  }

  #[Test]
  public function unmarshal_object_noconstructor_regression() {
    $this->assertEquals(
      (new PersonWithoutConstructor())->setId(6100)->setName('Test'),
      (new Marshalling())->unmarshal(['id' => 6100, 'name' => 'Test'], PersonWithoutConstructor::class)
    );
  }

  #[Test]
  public function unmarshal_object_less_arguments_regression() {
    $this->assertEquals(
      (new PersonWithoutConstructor())->setId(6100),
      (new Marshalling())->unmarshal(['id' => 6100], PersonWithoutConstructor::class)
    );
  }

  #[Test]
  public function unmarshal_activity() {
    $subscribables= ['one' => 1, 'two' => 2];
    $this->assertEquals(
      (new Activity())->setSubscribables($subscribables),
      (new Marshalling())->unmarshal(['subscribables' => $subscribables], Activity::class)
    );
  }
}