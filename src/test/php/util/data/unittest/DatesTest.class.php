<?php namespace util\data\unittest;

use unittest\Assert;
use unittest\{Test, TestCase, Values};
use util\Date;
use util\data\Marshalling;

class DatesTest {

  #[Test]
  public function marshal_date_uses_is8601() {
    Assert::equals(
      '2018-02-07T09:47:00+0100',
      (new Marshalling())->marshal(new Date('2018-02-07 09:47:00+0100'))
    );
  }

  #[Test, Values(['2018-02-07T09:47:00+0100', '2018-02-07 09:47:00+0100', '07.02.2018 09:47:00+0100', '07.02.2018 09:47:00 Europe/Berlin',])]
  public function unmarshal_date_accepts($format) {
    Assert::equals(
      new Date('2018-02-07 09:47:00+0100'),
      (new Marshalling())->unmarshal($format, Date::class)
    );
  }
}