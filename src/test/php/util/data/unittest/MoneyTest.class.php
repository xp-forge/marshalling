<?php namespace util\data\unittest;

use unittest\TestCase;
use util\Currency;
use util\Money;
use util\data\Marshalling;

class MoneyTest extends TestCase {

  #[@test]
  public function marshal_money_uses_amount_and_currency() {
    $this->assertEquals(
      ['amount' => '3.5', 'currency' => 'EUR'],
      (new Marshalling())->marshal(new Money(3.50, Currency::$EUR))
    );
  }

  #[@test]
  public function unmarshal_money_uses_amount_and_currency() {
    $this->assertEquals(
      new Money(3.50, Currency::$EUR),
      (new Marshalling())->unmarshal(['amount' => '3.5', 'currency' => 'EUR'], Money::class)
    );
  }
}