<?php namespace util\data;

use lang\{ArrayType, Enum, MapType, Reflection, Type, XPClass};
use util\{Bytes, Currency, Date, Money, XPIterator};

/**
 * Takes care of converting objects from and to maps
 *
 * @test  xp://util.data.unittest.MarshallingTest
 * @test  xp://util.data.unittest.PrimitivesTest
 * @test  xp://util.data.unittest.BytesTest
 * @test  xp://util.data.unittest.DatesTest
 * @test  xp://util.data.unittest.EnumsTest
 * @test  xp://util.data.unittest.MoneyTest
 * @test  xp://util.data.unittest.ObjectsTest
 */
class Marshalling {

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
      if ($t->isInstance($value)) {
        return $value;
      } else if ($t->isEnum()) {
        return Enum::valueOf($t, $value);
      } else if ($t->isAssignableFrom(Date::class)) {
        return new Date($value);
      } else if ($t->isAssignableFrom(Bytes::class)) {
        return new Bytes(base64_decode($value));
      } else if ($t->isAssignableFrom(Money::class)) {
        return new Money($value['amount'], Currency::getInstance($value['currency']));
      } else if ($t->isAssignableFrom(XPIterator::class)) {
        return new Iteration($value);
      } else if ($t->isInterface()) {
        return $t->cast($value);
      }

      $reflect= Reflection::of($t);
      if ($i= $reflect->initializer('__unserialize')) {
        return $i->newInstance([$value]);
      } else if (($c= $reflect->constructor()) && 1 === $c->parameters()->size() && $c->accepts([$value])) {
        return $c->newInstance([$value]);
      }

      $r= $reflect->initializer(function() { })->newInstance();
      foreach ($reflect->properties() as $property) {
        $m= $property->modifiers();
        if ($m->isStatic()) continue;

        $n= $property->name();
        if (!isset($value[$n])) continue;

        if (!$m->isPublic() && ($setter= $reflect->method('set'.ucfirst($n)))) {
          $setter->invoke($r, [$this->unmarshal($value[$n], $setter->parameter(0)->constraint()->type())]);
        } else {
          $property->set($r, $this->unmarshal($value[$n], $property->constraint()->type()), $reflect);
        }
      }
      return $r;
    } else if ($t instanceof ArrayType || $t instanceof MapType) {
      $t= $t->componentType();
      $r= [];
      foreach ($value as $k => $v) {
        $r[$k]= $this->unmarshal($v, $t);
      }
      return $r;
    } else if ($t === Type::$ARRAY) {
      $t= Type::$VAR;
      $r= [];
      foreach ($value as $k => $v) {
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
    if ($value instanceof Date) {
      return $value->toString(DATE_ISO8601);
    } else if ($value instanceof Bytes) {
      return base64_encode($value);
    } else if ($value instanceof Money) {
      return ['amount' => $value->amount(), 'currency' => $value->currency()->name()];
    } else if ($value instanceof Enum) {
      return $value->name();
    } else if ($value instanceof \Traversable) {
      return $this->generator($value);
    } else if ($value instanceof XPIterator) {
      return $this->iterator($value);
    } else if (is_object($value)) {
      if (method_exists($value, '__serialize')) return $value->__serialize();
      if (method_exists($value, '__toString')) return $value->__toString();

      $reflect= Reflection::of($value);
      $r= [];
      foreach ($reflect->properties() as $property) {
        $m= $property->modifiers();
        if ($m->isStatic()) continue;

        $n= $property->name();
        if (!$m->isPublic() && ($method= $reflect->method($n) ?? $reflect->method('set'.ucfirst($n)))) {
          $r[$n]= $this->marshal($method->invoke($value, []));
        } else {
          $r[$n]= $this->marshal($property->get($value, $reflect));
        }
      }
      return $r;
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