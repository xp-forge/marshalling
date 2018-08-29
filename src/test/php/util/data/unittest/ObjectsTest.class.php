<?php namespace util\data\unittest;

use lang\Type;
use unittest\TestCase;
use util\data\Marshalling;

class ObjectsTest extends TestCase {

  #[@test]
  public function marshal_person_value_object() {
    $this->assertEquals(
      ['id' => 6100, 'name' => 'Test'],
      (new Marshalling())->marshal(new Person(6100, 'Test'))
    );
  }

  #[@test]
  public function marshal_person_value_object_inside_map() {
    $this->assertEquals(
      ['person' => ['id' => 6100, 'name' => 'Test']],
      (new Marshalling())->marshal(['person' => new Person(6100, 'Test')])
    );
  }

  #[@test]
  public function marshal_person_value_object_inside_array() {
    $this->assertEquals(
      [['id' => 6100, 'name' => 'Test']],
      (new Marshalling())->marshal([new Person(6100, 'Test')])
    );
  }

  #[@test]
  public function marshal_value_object_in_value_object() {
    $this->assertEquals(
      ['list' => [['id' => 6100, 'name' => 'Test']]],
      (new Marshalling())->marshal(new People(new Person(6100, 'Test')))
    );
  }

  #[@test, @values([
  #  [['id' => 6100, 'name' => 'Test']],
  #  [['id' => '6100', 'name' => 'Test']],
  #])]
  public function unmarshal_person_value($object) {
    $this->assertEquals(
      new Person(6100, 'Test'),
      (new Marshalling())->unmarshal($object, Person::class)
    );
  }

  #[@test]
  public function unmarshal_person_value_object_from_inside_map() {
    $type= Type::forName('[:util.data.unittest.Person]');
    $this->assertEquals(
      ['person' => new Person(6100, 'Test')],
      (new Marshalling())->unmarshal(['person' => ['id' => 6100, 'name' => 'Test']], $type)
    );
  }

  #[@test]
  public function unmarshal_person_value_object_from_inside_array() {
    $type= Type::forName('util.data.unittest.Person[]');
    $this->assertEquals(
      [new Person(6100, 'Test')],
      (new Marshalling())->unmarshal([['id' => 6100, 'name' => 'Test']], $type)
    );
  }

  #[@test]
  public function unmarshal_object_noconstructor_regression() {
    $this->assertEquals(
      (new PersonWithoutConstructor())->setId(6100)->setName('Test'),
      (new Marshalling())->unmarshal(['id' => 6100, 'name' => 'Test'], PersonWithoutConstructor::class)
    );
  }

  #[@test]
  public function unmarshal_object_less_arguments_regression() {
    $this->assertEquals(
      (new PersonWithoutConstructor())->setId(6100),
      (new Marshalling())->unmarshal(['id' => 6100], PersonWithoutConstructor::class)
    );
  }

  #[@test]
  public function unmarshal_activity() {
    $subscribables= ['one' => 1, 'two' => 2];
    $this->assertEquals(
      (new Activity())->setSubscribables($subscribables),
      (new Marshalling())->unmarshal(['subscribables' => $subscribables], Activity::class)
    );
  }
}