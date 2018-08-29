<?php namespace util\data\unittest;

use unittest\TestCase;
use util\Currency;
use util\data\Marshalling;

class EnumsTest extends TestCase {

  #[@test]
  public function marshal_enum() {
    $this->assertEquals('EUR', (new Marshalling())->marshal(Currency::$EUR));
  }

  #[@test]
  public function unmarshal_enum() {
    $this->assertEquals(Currency::$EUR, (new Marshalling())->unmarshal('EUR', Currency::class));
  }
}