<?php

namespace Devoted\MemoryDB\Interfaces;

interface DatabaseInterface {

  public function countValues(string $value): int;

  public function set(string $name, string $value): void;

  public function get(string $name): string;

  public function delete(string $name): void;

  public function beginTransaction(): void;

  public function rollbackTransaction(): bool;

  public function commitTransactions(): void;
}