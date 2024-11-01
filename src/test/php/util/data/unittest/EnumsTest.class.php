<?php namespace util\data\unittest;

use test\verify\Runtime;
use test\{Assert, Test, Values};
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

  #[Test, Runtime(php: '>=8.1')]
  public function handles_unit_enum() {
    $suit= eval('enum Suit { case Hearts; case Diamonds; case Clubs; case Spades; } return Suit::Hearts;');
    $marshalling= new Marshalling();

    Assert::equals('Hearts', $marshalling->marshal($suit));
    Assert::equals($suit, $marshalling->unmarshal('Hearts', typeof($suit)));
  }

  #[Test, Runtime(php: '>=8.1')]
  public function handles_backed_enum() {
    $coin= eval('enum Coin : int { case Penny = 1; case Nickel = 5; case Dime = 10; case Quarter = 25; } return Coin::Dime;');
    $marshalling= new Marshalling();

    Assert::equals('Dime', $marshalling->marshal($coin));
    Assert::equals($coin, $marshalling->unmarshal('Dime', typeof($coin)));
  }
}