<?php namespace util\data\unittest\fixtures;

use lang\Value;
use util\Secret;

class Authorization implements Value {
  private $token, $type;

  public function __construct(Secret $token, string $type= 'Bearer') {
    $this->token= $token;
    $this->type= $type;
  }

  public function __serialize() {
    return ['token' => $this->token->reveal(), 'type' => $this->type];
  }

  public function __unserialize($data) {
    $this->token= new Secret($data['token']);
    $this->type= $data['type'];
  }

  public function hashCode() {
    return crc32($this->type).$this->token->hashCode();
  }

  public function toString() {
    return nameof($this).'('.$this->type.' '.$this->token->toString().')';
  }

  public function compareTo($value) {
    if ($value instanceof self) {
      $r= $this->type <=> $value->type;
      return 0 === $r ? $this->token->reveal() <=> $value->token->reveal() : $r;
    }
    return 1;
  }
}