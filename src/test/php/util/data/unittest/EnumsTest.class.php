<?php namespace util\data\unittest;

use unittest\{Test, TestCase, Values};
use util\Currency;
use util\data\Marshalling;

class EnumsTest extends TestCase {

  #[Test]
  public function marshal_enum() {
    $this->assertEquals('EUR', (new Marshalling())->marshal(Currency::$EUR));
  }

  #[Test]
  public function unmarshal_enum_name() {
    $this->assertEquals(Currency::$EUR, (new Marshalling())->unmarshal('EUR', Currency::class));
  }

  #[Test]
  public function unmarshal_enum_instance() {
    $this->assertEquals(Currency::$EUR, (new Marshalling())->unmarshal(Currency::$EUR, Currency::class));
  }
}