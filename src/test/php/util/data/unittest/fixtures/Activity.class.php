<?php namespace util\data\unittest\fixtures;

class Activity {
  private $subscribables;

  public function setSubscribables($subscribables) {
    $this->subscribables= $subscribables;
    return $this;
  }
}
