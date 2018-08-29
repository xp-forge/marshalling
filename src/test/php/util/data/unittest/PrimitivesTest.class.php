<?php namespace util\data\unittest;

use unittest\TestCase;
use util\data\Marshalling;

class PrimitivesTest extends TestCase {

  /** @return var[][] */
  private function fixtures() {
    return [
      [0], [-1], [1],
      [0.5], [-1.5],
      [null],
      [true], [false],
      [''], ['Test'],
      [[]], [[1, 2, 3]],
      [['key' => 'value']],
    ];
  }

  #[@test, @values('fixtures')]
  public function marshal($value) {
    $this->assertEquals($value, (new Marshalling())->marshal($value));
  }

  #[@test, @values('fixtures')]
  public function unmarshal($value) {
    $this->assertEquals($value, (new Marshalling())->unmarshal($value));
  }
}