<?php namespace util\data\unittest;

use test\{Assert, Test};
use util\data\Marshalling;
use util\{Currency, Money};

class MoneyTest {

  #[Test]
  public function marshal_money_uses_amount_and_currency() {
    Assert::equals(
      ['amount' => '3.5', 'currency' => 'EUR'],
      (new Marshalling())->marshal(new Money(3.50, Currency::$EUR))
    );
  }

  #[Test]
  public function unmarshal_money_uses_amount_and_currency() {
    Assert::equals(
      new Money(3.50, Currency::$EUR),
      (new Marshalling())->unmarshal(['amount' => '3.5', 'currency' => 'EUR'], Money::class)
    );
  }
}