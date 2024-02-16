<?php

namespace Devoted\MemoryDB\Classes;

class Transaction {
  /**
   * @var Command[]
   */
  private array $antiCommands;

  public function __construct() {
    $this->antiCommands = [];
  }

  /**
   * @return Command[]
   */
  public function getAntiCommands(): array {
    return $this->antiCommands;
  }

  public function pushAntiCommand(Command $incomingAntiCommand): void {
    // If any other types of commands sneak in here, no need to actually record them
    if (!$this->isCommandInvertible($incomingAntiCommand)) {
      return;
    }

    // If we are setting something that was already set previously in this command scope, we already know the value
    // it should be if this transaction rolls back.  Ignore this set.
    if ($incomingAntiCommand->isSetCommand() && $this->setCommandExistsForThisKey($incomingAntiCommand)) {
      return;
    }

    // Reverse order for commands that undo the commands that change state.
    array_unshift($this->antiCommands, $incomingAntiCommand);
  }

  private function isCommandInvertible(Command $command): bool {
    return $command->isDeleteCommand() || $command->isSetCommand();
  }

  private function setCommandExistsForThisKey(Command $incomingCommand)
  {
    foreach ($this->antiCommands as $antiCommand) {
      if ($antiCommand->isSetCommand()
        && $incomingCommand->isSetCommand()
        && $antiCommand->input1() == $incomingCommand->input1()
      ) {
        return true;
      }
    }

    return false;
  }

  public function print(): void {
    foreach ($this->getAntiCommands() as $command) {
      echo '  -- ' . $command->toString() . PHP_EOL;
    }
  }
}