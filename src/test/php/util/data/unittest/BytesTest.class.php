<?php namespace util\data\unittest;

use unittest\{Test, TestCase};
use util\Bytes;
use util\data\Marshalling;

class BytesTest extends TestCase {

  #[Test]
  public function marshal_bytes_uses_base64() {
    $this->assertEquals(
      'UEsDBA==',
      (new Marshalling())->marshal(new Bytes("\x50\x4b\x03\x04"))
    );
  }

  #[Test]
  public function unmarshal_bytes_from_base64() {
    $this->assertEquals(
      new Bytes("\x50\x4b\x03\x04"),
      (new Marshalling())->unmarshal('UEsDBA==', Bytes::class)
    );
  }
}