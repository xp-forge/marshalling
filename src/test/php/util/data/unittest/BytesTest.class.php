<?php namespace util\data\unittest;

use lang\Type;
use unittest\TestCase;
use util\Bytes;
use util\data\Marshalling;

class BytesTest extends TestCase {

  #[@test]
  public function marshal_bytes_uses_base64() {
    $this->assertEquals(
      'UEsDBA==',
      (new Marshalling())->marshal(new Bytes("\x50\x4b\x03\x04"))
    );
  }

  #[@test]
  public function unmarshal_bytes_from_base64() {
    $this->assertEquals(
      new Bytes("\x50\x4b\x03\x04"),
      (new Marshalling())->unmarshal('UEsDBA==', Type::forName(Bytes::class))
    );
  }
}