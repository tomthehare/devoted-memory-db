<?php

namespace Devoted\MemoryDB\Classes;

use Devoted\MemoryDB\Interfaces\DatabaseInterface;

class Database implements DatabaseInterface {

  public function countValues(string $value): int
  {
    // TODO: Implement countValues() method.
  }

  public function set(string $name, string $value): void
  {
    // TODO: Implement set() method.
  }

  public function get(string $name): string
  {
    // TODO: Implement get() method.
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
    // TODO: Implement rollbackTransaction() method.
  }

  public function commitTransactions(): void
  {
    // TODO: Implement commitTransactions() method.
  }
}