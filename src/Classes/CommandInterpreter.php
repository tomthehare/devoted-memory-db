<?php

namespace Devoted\MemoryDB\Classes;

use Devoted\MemoryDB\Interfaces\DatabaseInterface;

class CommandInterpreter {

  private DatabaseInterface $database;

  public function __construct(DatabaseInterface $database) {
    $this->database = $database;
  }

  public function runCommand(string $command): void {
  }

}