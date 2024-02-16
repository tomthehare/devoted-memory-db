<?php

namespace Devoted\MemoryDB\Tests\Unit\Classes;

use Devoted\MemoryDB\Classes\MemoryDatabase;
use PHPUnit\Framework\TestCase;

class MemoryDatabaseTest extends TestCase {

  public function testEmptyGet(): void {
    $db = new MemoryDatabase();

    self::assertNull($db->get('something'));
  }

  public function testSetEmptyValue(): void {
    $db = new MemoryDatabase();

    $db->set('', 'something');

    self::assertEquals('something', $db->get(''));
  }

  public function testSetFullValue(): void {
    $db = new MemoryDatabase();

    $name = 'thisIsABiggerKey';
    $value = 'something';
    $db->set($name, $value);

    self::assertEquals($value, $db->get($name));
  }

  public function testResetValue(): void {
    $db = new MemoryDatabase();

    $name = 'thisIsABiggerKey';
    $value1 = 'something';
    $value2 = 'somethingElseCompletely';
    $db->set($name, $value1);
    $db->set($name, $value2);

    self::assertEquals($value2, $db->get($name));
  }

  public function testCountOneThing(): void {
    $db = new MemoryDatabase();

    $name = 'thisIsABiggerKey';
    $value = 'something';
    $db->set($name, $value);

    self::assertEquals(1, $db->count($value));
  }

  public function testCountMoreThings(): void {
    $db = new MemoryDatabase();

    $name = '$name';
    $name2 = '$name2';
    $name3 = '$name3';

    $value = 'something';
    $db->set($name, $value);
    $db->set($name2, $value);
    $db->set($name3, $value);

    self::assertEquals(3, $db->count($value));
  }

  public function testMultipleSetsOnSameValueMaintainsProperCount(): void {
    $db = new MemoryDatabase();

    $name = '$name';
    $name2 = '$name2';
    $name3 = '$name3';

    $value = 'something';
    $db->set($name, $value);
    $db->set($name3, $value);
    $db->set($name2, $value);
    $db->set($name2, $value);
    $db->set($name3, $value);
    $db->set($name3, $value);
    $db->set($name2, $value);

    self::assertEquals(3, $db->count($value));
  }

  public function testMultipleSetsOnSameValueAndThenDelete(): void {
    $db = new MemoryDatabase();

    $name = '$name';
    $name2 = '$name2';
    $name3 = '$name3';

    $value = 'something';
    $db->set($name, $value);
    $db->set($name3, $value);
    $db->set($name2, $value);

    self::assertEquals(3, $db->count($value));

    $db->delete($name2);

    self::assertEquals(2, $db->count($value));
  }
}