<?php namespace util\data;

use UnitEnum, Traversable;
use lang\{ArrayType, Enum, MapType, Reflection, Type, XPClass};
use util\{Bytes, Currency, Date, Money, XPIterator};

/**
 * Takes care of converting objects from and to maps
 *
 * @test  util.data.unittest.MarshallingTest
 * @test  util.data.unittest.PrimitivesTest
 * @test  util.data.unittest.BytesTest
 * @test  util.data.unittest.DatesTest
 * @test  util.data.unittest.EnumsTest
 * @test  util.data.unittest.MoneyTest
 * @test  util.data.unittest.ObjectsTest
 */
class Marshalling {
  private $mappings= [];

  /** Creates marshalling with default mappings for classes in `util` */
  public function __construct() {
    $this->mappings[Date::class]= [
      function($value) { return $value->toString(DATE_ISO8601); },
      function($value) { return new Date($value); },
    ];
    $this->mappings[Bytes::class]= [
      function($value) { return base64_encode($value); },
      function($value) { return new Bytes(base64_decode($value)); },
    ];
    $this->mappings[Money::class]= [
      function($value) { return ['amount' => $value->amount(), 'currency' => $value->currency()->name()]; },
      function($value) { return new Money($value['amount'], Currency::getInstance($value['currency'])); },
    ];
    $this->mappings[XPIterator::class]= [
      function($value) { return $this->iterator($value); },
      function($value, $t) { return new Iteration($value); },
    ];
  }

  /**
   * Maps user type marshalling
   *
   * @param  string|lang.XPClass $type
   * @param  function(var): var $marshal
   * @return self
   */
  public function mapping($type, $marshal) {
    $this->mappings[$type instanceof XPClass ? $type->literal() : $type][0]= $marshal;
    return $this;
  }

  /**
   * Resolves user type unmarshalling
   *
   * @param  string|lang.XPClass $type
   * @param  function(var, lang.XPClass): var $unmarshal
   * @return self
   */
  public function resolving($type, $unmarshal) {
    $this->mappings[$type instanceof XPClass ? $type->literal() : $type][1]= $unmarshal;
    return $this;
  }

  /**
   * Applies unmarshal() to values inside an iterable
   *
   * @param  iterable $in
   * @param  lang.Type $type
   * @return iterable
   */
  private function iterable($in, $type) {
    foreach ($in as $key => $value) {
      yield $key => $this->unmarshal($value, $type);
    }
  }

  /**
   * Unmarshals a value. Handles util.Date and util.Money instances specially,
   * creates instances if the type has a single-argument constructor; treats
   * other types in a generic way, iterating over their instance fields.
   *
   * @param  var $value
   * @param  ?lang.Type|string $type
   * @return var
   */
  public function unmarshal($value, $type= null) {
    if (null === $type) return $value;

    $t= $type instanceof Type ? $type : Type::forName($type);
    if ($t instanceof XPClass) {
      foreach ($this->mappings as $type => $mapping) {
        if ($t->isAssignableFrom($type)) return $mapping[1]($value, $t);
      }

      if ($t->isInstance($value)) {
        return $value;
      } else if ($t->isEnum()) {
        return Enum::valueOf($t, $value);
      } else if ($t->isInterface()) {
        return $t->cast($value);
      }

      $reflect= Reflection::type($t);
      if (($c= $reflect->constructor()) && $c->parameters()->accept([$value], 1)) {
        return $c->newInstance([$value]);
      }

      $r= $reflect->initializer(null)->newInstance();
      if (method_exists($r, '__unserialize')) {
        $r->__unserialize($value);
        return $r;
      }

      foreach ($reflect->properties() as $name => $p) {
        $modifiers= $p->modifiers();
        if ($modifiers->isStatic() || !isset($value[$name])) {
          continue;
        } else if ($m= $reflect->method('set'.ucfirst($name))) {
          $m->invoke($r, [$this->unmarshal($value[$name], $m->parameter(0)->constraint()->type())]);
        } else {
          $p->set($r, $this->unmarshal($value[$name], $p->constraint()->type()), $reflect);
        }
      }
      return $r;
    } else if ($t instanceof ArrayType || $t instanceof MapType) {
      $t= $t->componentType();
      $r= [];
      foreach ($value instanceof Traversable ? $value : (array)$value as $k => $v) {
        $r[$k]= $this->unmarshal($v, $t);
      }
      return $r;
    } else if ($t === Type::$ARRAY) {
      $t= Type::$VAR;
      $r= [];
      foreach ($value instanceof Traversable ? $value : (array)$value as $k => $v) {
        $r[$k]= $this->unmarshal($v, $t);
      }
      return $r;
    } else if ($t === Type::$ITERABLE) {
      return $this->iterable($value, Type::$VAR);
    } else {
      return $t->cast($value);
    }
  }

  /**
   * Applies marshal() to values inside a generator
   *
   * @param  iterable $in
   * @return iterable
   */
  private function generator($in) {
    foreach ($in as $key => $value) {
      yield $key => $this->marshal($value);
    }
  }

  /**
   * Applies marshal() to values inside an iterator
   *
   * @param  util.XPIterator $in
   * @return iterable
   */
  private function iterator($in) {
    while ($in->hasNext()) {
      yield $this->marshal($in->next());
    }
  }

  /**
   * Marshals a value. Handles util.Date and util.Money instances specially,
   * converts objects with a `__toString` method and handles other objects
   * in a generic way, iterating over their instance fields.
   * 
   * @param  var $value
   * @return var
   */
  public function marshal($value) {
    if (is_object($value)) {
      foreach ($this->mappings as $type => $marshal) {
        if ($value instanceof $type) return $marshal[0]($value);
      }

      if ($value instanceof Enum) {
        return $value->name();
      } else if ($value instanceof UnitEnum) {
        return $value->name;
      } else if ($value instanceof Traversable) {
        return $this->generator($value);
      }

      if (method_exists($value, '__serialize')) return $value->__serialize();
      if (method_exists($value, '__toString')) return $value->__toString();

      $r= [];
      $reflect= Reflection::type($value);
      foreach ($reflect->properties() as $name => $p) {
        $modifiers= $p->modifiers();
        if ($modifiers->isStatic()) {
          continue;
        } else if ($m= $reflect->method($name) ?? $reflect->method('get'.ucfirst($name))) {
          $r[$name]= $this->marshal($m->invoke($value, []));
        } else {
          $r[$name]= $this->marshal($p->get($value, $reflect));
        }
      }
      return empty($r) ? (object)$r : $r;
    } else if (is_array($value)) {
      $r= [];
      foreach ($value as $k => $v) {
        $r[$k]= $this->marshal($v);
      }
      return $r;
    } else {
      return $value;
    }
  }
}