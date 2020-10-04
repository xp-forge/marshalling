<?php namespace util\data\unittest;

use unittest\{Test, TestCase};
use util\data\Marshalling;
use util\{Currency, Money};

class MoneyTest extends TestCase {

  #[Test]
  public function marshal_money_uses_amount_and_currency() {
    $this->assertEquals(
      ['amount' => '3.5', 'currency' => 'EUR'],
      (new Marshalling())->marshal(new Money(3.50, Currency::$EUR))
    );
  }

  #[Test]
  public function unmarshal_money_uses_amount_and_currency() {
    $this->assertEquals(
      new Money(3.50, Currency::$EUR),
      (new Marshalling())->unmarshal(['amount' => '3.5', 'currency' => 'EUR'], Money::class)
    );
  }
}