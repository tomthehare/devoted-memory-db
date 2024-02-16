<?php

namespace Devoted\MemoryDB\Classes;

class Command {

  private string $rawCommand;

  public function __construct(string $command) {
    $this->rawCommand = $command;
  }

  public function isValid(): bool {
    return false;
  }

}