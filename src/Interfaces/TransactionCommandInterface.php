<?php

namespace Devoted\MemoryDB\Interfaces;

interface TransactionCommandInterface {
  public function beginTransaction(): void;

  public function rollbackTransaction(): bool;

  public function commitTransactions(): void;
}