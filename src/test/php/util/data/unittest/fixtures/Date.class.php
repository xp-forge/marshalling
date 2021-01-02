<?php namespace util\data\unittest\fixtures;

class Date {
  private $timestamp;

  /** @param int|string $arg */
  public function __construct($arg) {
    $this->timestamp= is_int($arg) ? $arg : strtotime($arg);
  }

  /** @return int */
  public function timestamp() { return $this->timestamp; }
}