<?php

namespace Devoted\MemoryDB\Classes;

use Devoted\MemoryDB\Interfaces\DatabaseInterface;

class Database implements DatabaseInterface {

  public function count(string $value): int
  {
    return -1;
  }

  public function set(string $name, string $value): void
  {
    // TODO: Implement set() method.
  }

  public function get(string $name): string
  {
    return '';
  }

  public function delete(string $name): void
  {
    // TODO: Implement delete() method.
  }

  public function beginTransaction(): void
  {
    // TODO: Implement beginTransaction() method.
  }

  public function rollbackTransaction(): bool
  {
    return false;
  }

  public function commitTransactions(): void
  {
    // TODO: Implement commitTransactions() method.
  }
}