<?php

namespace Devoted\MemoryDB\Classes;

use Devoted\MemoryDB\Interfaces\DatabaseCommandInterface;
use Devoted\MemoryDB\Interfaces\TransactionCommandInterface;

class TransactionCoordinator implements TransactionCommandInterface, DatabaseCommandInterface {

  /**
   * @var Transaction[]
   */
  private array $transactionScopes;
  private ?Transaction $activeTransaction;
  private DatabaseCommandInterface $database;

  public function __construct(DatabaseCommandInterface $database) {
    $this->database = $database;
    $this->transactionScopes = [];
    $this->activeTransaction = null;
  }

  private function getActiveTransaction(): ?Transaction {
    return $this->activeTransaction;
  }

  public function beginTransaction(): void
  {
    $newTransaction = new Transaction();
    $this->transactionScopes[] = $newTransaction;
    $this->activeTransaction = $newTransaction;
  }

  public function rollbackTransaction(): bool
  {
    if (count($this->transactionScopes) == 0) {
      return false;
    }

    $transaction = array_pop($this->transactionScopes);

    // "Undo" the commands that were done as part of the last transaction scope.
    // When creating them, their opposite was created on a reversed ledger, so no need to think too much here.
    foreach ($transaction->getAntiCommands() as $antiCommand) {
      if ($antiCommand->isDeleteCommand()) {
        $this->database->delete($antiCommand->input1());
      }

      if ($antiCommand->isSetCommand()) {
        $this->database->set($antiCommand->input1(), $antiCommand->input2());
      }
    }

    if (count($this->transactionScopes) > 0) {
      $this->activeTransaction = end($this->transactionScopes);
    } else {
      $this->activeTransaction = null;
    }

    return true;
  }

  public function commitTransactions(): void
  {
    // Committing a set of transactions just means not undoing any of them in this implementation.
    $this->transactionScopes = [];
    $this->activeTransaction = null;
  }

  public function count(string $value): int
  {
    return $this->database->count($value);
  }

  public function set(string $name, string $value): void
  {
    if (!is_null($this->getActiveTransaction())) {
      $currentlySetValue = $this->database->get($name);
      // If the value was set previously before this set, we need to roll back to that value in the case of a rollback.
      if (!is_null($currentlySetValue)) {
        $this->getActiveTransaction()->pushAntiCommand((new Command("SET {$name} {$currentlySetValue}")));
      // Else, it was not set previously, so we can just delete it.
      } else {
        $this->getActiveTransaction()->pushAntiCommand((new Command("DELETE {$name}")));
      }
    }
    $this->database->set($name, $value);
  }

  public function get(string $name): ?string
  {
    return $this->database->get($name);
  }

  public function delete(string $name): void
  {
    $previousValue = $this->database->get($name);

    // If it was null, this means it wasn't set, so there's nothing to add back with a rollback nor delete.
    if (!is_null($previousValue)) {
      if (!is_null($this->getActiveTransaction())) {
        $this->getActiveTransaction()->pushAntiCommand((new Command("SET {$name} {$previousValue}")));
      }

      $this->database->delete($name);
    }
  }

  public function print(): void {
    echo 'Active transaction? ' . (!is_null($this->activeTransaction) ? spl_object_hash($this->activeTransaction) : ' none') . PHP_EOL;
    foreach ($this->transactionScopes as $index => $tx) {
      echo 'TX ' . $index . ' (' . spl_object_hash($tx) . ')' . PHP_EOL;
      $tx->print();
      echo PHP_EOL;
    }
  }
}