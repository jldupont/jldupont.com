<?
class A {
 function f() {
   echo "A::f -- this: |".get_class($this)."|\n";
 }
}

class B extends A {
 function f() {
  echo "B::f -- this: |".get_class($this)."|\n";
  A::f();
 }
}

class Z {
 function g() {
  echo "Z::g -- this: |".get_class($this)."|\n";
  B::f();
 }
}

$z = new Z();
$z->g();
