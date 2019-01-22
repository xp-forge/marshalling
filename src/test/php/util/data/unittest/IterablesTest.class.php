<?php namespace util\data\unittest;

use lang\Type;
use unittest\TestCase;
use util\XPIterator;
use util\data\Marshalling;

class IterablesTest extends TestCase {

  #[@test]
  public function marshal_generator() {
    $generator= function() { yield 1; yield 2; yield 3; };
    $this->assertEquals([1, 2, 3], iterator_to_array((new Marshalling())->marshal($generator())));
  }

  #[@test]
  public function marshal_keyvalue_generator() {
    $generator= function() { yield 'one' => 1; yield 'two' => 2; };
    $this->assertEquals(['one' => 1, 'two' => 2], iterator_to_array((new Marshalling())->marshal($generator())));
  }

  #[@test]
  public function marshal_iterator() {
    $iterator= new \ArrayIterator([1, 2, 3]);
    $this->assertEquals([1, 2, 3], iterator_to_array((new Marshalling())->marshal($iterator)));
  }

  #[@test]
  public function marshal_iterator_aggregate() {
    $iterator= newinstance(\IteratorAggregate::class, [], [
      'getIterator' => function() { yield 1; yield 2; yield 3; }
    ]);
    $this->assertEquals([1, 2, 3], iterator_to_array((new Marshalling())->marshal($iterator)));
  }

  #[@test]
  public function marshal_util_iterator() {
    $iterator= newinstance(XPIterator::class, [], [
      'backing' => [1, 2, 3],
      'hasNext' => function() { return !empty($this->backing); },
      'next'    => function() { return array_shift($this->backing); },
    ]);
    $this->assertEquals([1, 2, 3], iterator_to_array((new Marshalling())->marshal($iterator)));
  }

  #[@test]
  public function unmarshal_iterable() {
    $this->assertEquals(
      [1, 2, 3],
      iterator_to_array((new Marshalling())->unmarshal([1, 2, 3], Type::$ITERABLE))
    );
  }

  #[@test]
  public function unmarshal_keyvalue_iterable() {
    $this->assertEquals(
      ['one' => 1, 'two' => 2],
      iterator_to_array((new Marshalling())->unmarshal(['one' => 1, 'two' => 2], Type::$ITERABLE))
    );
  }

  #[@test]
  public function unmarshal_util_iterator() {
    $it= (new Marshalling())->unmarshal([1, 2, 3], XPIterator::class);
    $result= [];
    while ($it->hasNext()) {
      $result[]= $it->next();
    }
    $this->assertEquals([1, 2, 3], $result);
  }

  #[@test]
  public function unmarshal_generator_to_util_iterator() {
    $f= function() { yield 1; yield 2; yield 3; };
    $it= (new Marshalling())->unmarshal($f(), XPIterator::class);
    $result= [];
    while ($it->hasNext()) {
      $result[]= $it->next();
    }
    $this->assertEquals([1, 2, 3], $result);
  }
}