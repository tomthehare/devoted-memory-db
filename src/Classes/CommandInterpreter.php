<?php

namespace Devoted\MemoryDB\Classes;

use Devoted\MemoryDB\Exceptions\ExitException;

class CommandInterpreter {

  const RESPONSE_INVALID_COMMAND = 'INVALID COMMAND';
  const RESPONSE_TRANSACTION_NOT_FOUND = 'TRANSACTION NOT FOUND';

  private TransactionCoordinator $transactionCoordinator;

  public function __construct(TransactionCoordinator $transactionCoordinator) {
    $this->transactionCoordinator = $transactionCoordinator;
  }

  /**
   * @param string $command
   * @return string|null
   * @throws ExitException
   */
  public function runCommand(string $command): ?string {
    $command = new Command($command);

    if (!$command->isValid()) {
      return self::RESPONSE_INVALID_COMMAND;
    }

    switch ($command->commandVerb()) {
      case Command::COMMAND_SET:
        $this->transactionCoordinator->set($command->input1(), $command->input2());
        break;
      case Command::COMMAND_GET:
        $value = $this->transactionCoordinator->get($command->input1());
        return $value != '' ? $value : 'NULL';
      case Command::COMMAND_DELETE:
        $this->transactionCoordinator->delete($command->input1());
        break;
      case Command::COMMAND_COUNT:
        return (string) $this->transactionCoordinator->count($command->input1());
      case Command::COMMAND_BEGIN:
        $this->transactionCoordinator->beginTransaction();
        break;
      case Command::COMMAND_ROLLBACK:
        return $this->transactionCoordinator->rollbackTransaction() ? null : self::RESPONSE_TRANSACTION_NOT_FOUND;
      case Command::COMMAND_COMMIT:
        $this->transactionCoordinator->commitTransactions();
        break;
      case Command::COMMAND_END;
        throw new ExitException();
      default:
        break;
    }

    return null;
  }
}