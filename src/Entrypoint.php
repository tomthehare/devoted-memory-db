<?php

namespace Devoted\MemoryDB;

use Devoted\MemoryDB\Classes\CommandInterpreter;
use Devoted\MemoryDB\Classes\MemoryDatabase;
use Devoted\MemoryDB\Classes\TransactionCoordinator;
use Devoted\MemoryDB\Exceptions\ExitException;

class Entrypoint {

  private CommandInterpreter $commandInterpreter;

  public function __construct(CommandInterpreter $commandInterpreter)
  {
    $this->commandInterpreter = $commandInterpreter;
  }

  public function executeControlLoop(): void
  {
    echo ">>";
    try {
      while ($line = fgets(STDIN)) {
        // Remove newline character from the end of the line

        $line = rtrim($line, "\n");

        // Process the input line
        $output = $this->commandInterpreter->runCommand($line);

        if (!is_null($output) && $output !== '') {
          echo $output . PHP_EOL;
        }

        echo ">>";
      }
    } catch (ExitException) {
      // This is okay!  Gracefully exit.
    } catch (\Exception $exception) {
      echo "Exception thown: " . $exception->getMessage();
    }
  }
}

require '/devoted/memory-db/vendor/autoload.php';

$database = new MemoryDatabase();
$transactionCoordinator = new TransactionCoordinator($database);
$commandInterpreter = new CommandInterpreter($transactionCoordinator);

(new Entrypoint($commandInterpreter))->executeControlLoop();

