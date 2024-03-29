<?php namespace util\data\unittest;

use lang\Type;
use test\{Assert, Test, Values};
use util\XPIterator;
use util\data\Marshalling;

class IterablesTest {

  #[Test]
  public function marshal_generator() {
    $generator= function() { yield 1; yield 2; yield 3; };
    Assert::equals([1, 2, 3], iterator_to_array((new Marshalling())->marshal($generator())));
  }

  #[Test]
  public function marshal_keyvalue_generator() {
    $generator= function() { yield 'one' => 1; yield 'two' => 2; };
    Assert::equals(['one' => 1, 'two' => 2], iterator_to_array((new Marshalling())->marshal($generator())));
  }

  #[Test]
  public function marshal_iterator() {
    $iterator= new \ArrayIterator([1, 2, 3]);
    Assert::equals([1, 2, 3], iterator_to_array((new Marshalling())->marshal($iterator)));
  }

  #[Test]
  public function marshal_iterator_aggregate() {
    $iterator= newinstance(\IteratorAggregate::class, [], [
      'getIterator' => function(): \Traversable { yield 1; yield 2; yield 3; }
    ]);
    Assert::equals([1, 2, 3], iterator_to_array((new Marshalling())->marshal($iterator)));
  }

  #[Test]
  public function marshal_util_iterator() {
    $iterator= newinstance(XPIterator::class, [], [
      'backing' => [1, 2, 3],
      'hasNext' => function() { return !empty($this->backing); },
      'next'    => function() { return array_shift($this->backing); },
    ]);
    Assert::equals([1, 2, 3], iterator_to_array((new Marshalling())->marshal($iterator)));
  }

  #[Test]
  public function unmarshal_iterable() {
    Assert::equals(
      [1, 2, 3],
      iterator_to_array((new Marshalling())->unmarshal([1, 2, 3], Type::$ITERABLE))
    );
  }

  #[Test]
  public function unmarshal_generator_to_iterable() {
    $f= function() { yield 1; yield 2; yield 3; };
    Assert::equals(
      [1, 2, 3],
      iterator_to_array((new Marshalling())->unmarshal($f(), Type::$ITERABLE))
    );
  }

  #[Test]
  public function unmarshal_keyvalue_iterable() {
    Assert::equals(
      ['one' => 1, 'two' => 2],
      iterator_to_array((new Marshalling())->unmarshal(['one' => 1, 'two' => 2], Type::$ITERABLE))
    );
  }

  #[Test]
  public function unmarshal_util_iterator() {
    $it= (new Marshalling())->unmarshal([1, 2, 3], XPIterator::class);
    $result= [];
    while ($it->hasNext()) {
      $result[]= $it->next();
    }
    Assert::equals([1, 2, 3], $result);
  }

  #[Test]
  public function unmarshal_generator_to_util_iterator() {
    $f= function() { yield 1; yield 2; yield 3; };
    $it= (new Marshalling())->unmarshal($f(), XPIterator::class);
    $result= [];
    while ($it->hasNext()) {
      $result[]= $it->next();
    }
    Assert::equals([1, 2, 3], $result);
  }

  #[Test, Values([[[1, 2, 3]], [['color' => 'green']]])]
  public function unmarshal_array_to_array($value) {
    Assert::equals($value, (new Marshalling())->unmarshal($value, Type::$ARRAY));
  }

  #[Test]
  public function unmarshal_iterable_to_array() {
    $f= function() { yield 1; yield 2; yield 3; };
    Assert::equals([1, 2, 3], (new Marshalling())->unmarshal($f(), Type::$ARRAY));
  }

  #[Test, Values(['one', 1, 0, -1.5, true, false])]
  public function unmarshal_scalar_to_array() {
    Assert::equals([], (new Marshalling())->unmarshal(null, Type::$ARRAY));
  }

  #[Test]
  public function unmarshal_null_to_array() {
    Assert::equals([], (new Marshalling())->unmarshal(null, Type::$ARRAY));
  }
}