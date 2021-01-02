<?php namespace util\data\unittest;

use unittest\Assert;
use unittest\{Test, TestCase, Values};
use util\data\Marshalling;

class PrimitivesTest {

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

  #[Test, Values('fixtures')]
  public function marshal($value) {
    Assert::equals($value, (new Marshalling())->marshal($value));
  }

  #[Test, Values('fixtures')]
  public function unmarshal($value) {
    Assert::equals($value, (new Marshalling())->unmarshal($value));
  }

  #[Test, Values([['0', 0], ['1', 1], ['-1', -1],])]
  public function unmarshal_to_int_coerces_string($value, $expected) {
    Assert::equals($expected, (new Marshalling())->unmarshal($value, 'int'));
  }
}