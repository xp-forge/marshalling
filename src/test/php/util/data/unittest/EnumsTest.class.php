<?php namespace util\data\unittest;

use test\Assert;
use test\{Test, TestCase, Values};
use util\Currency;
use util\data\Marshalling;

class EnumsTest {

  #[Test]
  public function marshal_enum() {
    Assert::equals('EUR', (new Marshalling())->marshal(Currency::$EUR));
  }

  #[Test]
  public function unmarshal_enum_name() {
    Assert::equals(Currency::$EUR, (new Marshalling())->unmarshal('EUR', Currency::class));
  }

  #[Test]
  public function unmarshal_enum_instance() {
    Assert::equals(Currency::$EUR, (new Marshalling())->unmarshal(Currency::$EUR, Currency::class));
  }
}