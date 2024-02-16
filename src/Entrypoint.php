<?php

namespace Devoted\MemoryDB;

use Devoted\MemoryDB\Classes\CommandInterpreter;
use Devoted\MemoryDB\Classes\MemoryDatabase;

class Entrypoint {

  private CommandInterpreter $commandInterpreter;

  public function __construct(CommandInterpreter $commandInterpreter)
  {
    $this->commandInterpreter = $commandInterpreter;
  }

  public function executeControlLoop()
  {
    while ($line = fgets(STDIN)) {
      // Remove newline character from the end of the line
      $line = rtrim($line, "\n");

      // Process the input line
      $output = $this->commandInterpreter->runCommand($line);

      if ($output !== '') {
        echo $output . PHP_EOL;
      }
    }
  }
}

require '/devoted/memory-db/vendor/autoload.php';

// Set to true to get some more verbose logging.
$debug = true;

$commandInterpreter = new CommandInterpreter(
  (new MemoryDatabase())
);

(new Entrypoint($commandInterpreter))->executeControlLoop();

