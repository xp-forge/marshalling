<?php namespace util\data;

use lang\ArrayType;
use lang\Enum;
use lang\MapType;
use lang\Type;
use lang\XPClass;
use util\Bytes;
use util\Currency;
use util\Date;
use util\Money;

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
      if ($t->isInterface()) {
        return $t->cast($value);
      } else if ($t->isEnum()) {
        return Enum::valueOf($t, $value);
      } else if ($t->isInstance($value)) {
        return $value;
      } else if ($t->isAssignableFrom(Date::class)) {
        return new Date($value);
      } else if ($t->isAssignableFrom(Bytes::class)) {
        return new Bytes(base64_decode($value));
      } else if ($t->isAssignableFrom(Money::class)) {
        return new Money($value['amount'], Currency::getInstance($value['currency']));
      } else if ($t->hasConstructor() && 1 === $t->getConstructor()->numParameters()) {
        return $t->newInstance($value);
      }

      $n= $t->literal();
      $r= unserialize('O:'.strlen($n).':"'.$n.'":0:{}');
      foreach ($t->getFields() as $field) {
        $m= $field->getModifiers();
        if ($m & MODIFIER_STATIC) continue;

        $n= $field->getName();
        if (!isset($value[$n])) continue;

        if ($m & MODIFIER_PUBLIC) {
          $field->set($r, $this->unmarshal($value[$n], $field->getType()));
        } else if ($t->hasMethod($set= 'set'.ucfirst($n))) {
          $method= $t->getMethod($set);
          $method->invoke($r, [$this->unmarshal($value[$n], $method->getParameter(0)->getType())]);
        } else {
          $field->setAccessible(true)->set($r, $this->unmarshal($value[$n], $field->getType()));
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
      return ['amount' => $value->amount(), 'currency' => $value->currency()->toString()];
    } else if ($value instanceof Enum) {
      return $value->name();
    } else if ($value instanceof \Traversable) {
      return $this->generator($value);
    } else if (is_object($value)) {
      if (method_exists($value, '__toString')) return $value->__toString();

      $r= [];
      $type= typeof($value);
      foreach ($type->getFields() as $field) {
        $m= $field->getModifiers();
        if ($m & MODIFIER_STATIC) continue;

        $n= $field->getName();
        if ($m & MODIFIER_PUBLIC) {
          $v= $field->get($value);
        } else if ($type->hasMethod($n)) {
          $v= $type->getMethod($n)->invoke($value, []);
        } else if ($type->hasMethod($get= 'get'.ucfirst($n))) {
          $v= $type->getMethod($get)->invoke($value, []);
        } else {
          $v= $field->setAccessible(true)->get($value);
        }
        $r[$n]= $this->marshal($v);
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