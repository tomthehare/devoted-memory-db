<?php

namespace Devoted\MemoryDB\Classes;

use Devoted\MemoryDB\Interfaces\DatabaseCommandInterface;

class MemoryDatabase implements DatabaseCommandInterface {

  /**
   * @var array Normal array to do name based lookups with
   */
  private array $nameSet;

  /**
   * @var array Think of this as sort-of a secondary index to support the count function.
   */
  private array $valueSet;

  public function __construct() {
    $this->nameSet = [];
    $this->valueSet = [];
  }

  public function count(string $value): int
  {
    return count(array_keys($this->valueSet[$value] ?? []));
  }

  public function set(string $name, string $value): void
  {
    if (!array_key_exists($value, $this->valueSet)) {
      $this->valueSet[$value] = [];
    }

    $previousValueSet = $this->valueSet[$this->get($name)] ?? [];

    // If this is true, it indicates that the name we're setting a value for was previous set to another value
    // and we need to clean up this index.
    if (count($previousValueSet) == 1) {
      unset($this->valueSet[$this->get($name)]);
    // Alternatively, this value still has other keys attached to it.  Let's unset just this one.
    } elseif (count($previousValueSet) > 1) {
      unset($this->valueSet[$this->get($name)][$name]);
    }

    // Now that our index maintenance is done, we can update the name value
    $this->nameSet[$name] = $value;

    // and the value index - just need a distinct set of keys that are pointing to this value
    $this->valueSet[$value][$name] = true;
  }

  public function get(string $name): ?string
  {
    return $this->nameSet[$name] ?? null;
  }

  public function delete(string $name): void
  {
    if (array_key_exists($name, $this->nameSet)) {
      $value = $this->nameSet[$name];

      unset($this->nameSet[$name]);
      unset($this->valueSet[$value][$name]);

      // If this key was the last remaining thing in the value index, clean up the bucket as well.
      if (count($this->valueSet[$value]) == 0) {
        unset($this->valueSet[$value]);
      }
    }
  }

  public function printState() {
    echo 'NAME SET:' . PHP_EOL;
    echo json_encode($this->nameSet, JSON_PRETTY_PRINT);
    echo PHP_EOL;
    echo 'VALUE SET:' . PHP_EOL;
    echo json_encode($this->valueSet, JSON_PRETTY_PRINT);
    echo PHP_EOL. PHP_EOL;

  }
}