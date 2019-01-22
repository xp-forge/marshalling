<?php namespace util\data;

use util\XPIterator;

class Iterator implements XPIterator {
  private $iterable;

  /** @param iterable $arg */
  public function __construct($arg) {
    $this->iterable= $arg instanceof \Traversable ? $arg : new \ArrayIterator($arg);
  }

  /** @return bool */
  public function hasNext() { return $this->iterable->valid(); }

  /** @return var */
  public function next() {
    $value= $this->iterable->current();
    $this->iterable->next();
    return $value;
  }
}