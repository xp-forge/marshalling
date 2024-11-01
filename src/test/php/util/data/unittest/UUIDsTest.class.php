<?php namespace util\data\unittest;

use test\{Assert, Test, Values};
use util\UUID;
use util\data\Marshalling;

class UUIDsTest {

  #[Test]
  public function marshal() {
    Assert::equals(
      '{a4f1431f-9039-4b4d-9aec-e174419eb07d}',
      (new Marshalling())->marshal(new UUID('a4f1431f-9039-4b4d-9aec-e174419eb07d'))
    );
  }

  #[Test, Values(['a4f1431f-9039-4b4d-9aec-e174419eb07d', '{a4f1431f-9039-4b4d-9aec-e174419eb07d}'])]
  public function unmarshal_UUID($format) {
    Assert::equals(
      new UUID('a4f1431f-9039-4b4d-9aec-e174419eb07d'),
      (new Marshalling())->unmarshal($format, UUID::class)
    );
  }
}