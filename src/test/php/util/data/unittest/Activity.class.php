<?php namespace util\data\unittest;

class Activity {
  private $subscribables;

  public function setSubscribables($subscribables) {
    $this->subscribables= $subscribables;
    return $this;
  }
}
