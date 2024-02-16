<?php

namespace Devoted\MemoryDB\Classes;

use Devoted\MemoryDB\Classes\Command;
use Devoted\MemoryDB\Interfaces\DatabaseInterface;
use Psr\Log\LoggerInterface;

class CommandInterpreter {

  const RESPONSE_INVALID_COMMAND = 'INVALID COMMAND';
  const RESPONSE_TRANSACTION_NOT_FOUND = 'TRANSACTION NOT FOUND';

  private DatabaseInterface $database;
  private LoggerInterface $logger;

  public function __construct(DatabaseInterface $database, LoggerInterface $logger) {
    $this->database = $database;
    $this->logger = $logger;
  }

  public function runCommand(string $command): string {
    $this->logger->debug('Evaluating command: ' . $command);

    $command = new Command($command);

    if (!$command->isValid()) {
      return self::RESPONSE_INVALID_COMMAND;
    }

    $this->logger->debug($command->toJson());

    switch ($command->commandVerb()) {
      case Command::COMMAND_SET:
        $this->database->set($command->input1(), $command->getInput2());
        return '';
      case Command::COMMAND_GET:
        $value = $this->database->get($command->input1());
        return $value != '' ? $value : 'NULL';
      case Command::COMMAND_DELETE:
        $this->database->delete($command->input1());
        return '';
      case Command::COMMAND_COUNT:
        return (string) $this->database->count($command->input1());
      case Command::COMMAND_BEGIN:
        $this->database->beginTransaction();
        return '';
      case Command::COMMAND_ROLLBACK:
        return $this->database->rollbackTransaction() ? '' : self::RESPONSE_TRANSACTION_NOT_FOUND;
      case Command::COMMAND_COMMIT:
        $this->database->commitTransactions();
        return '';
      default:
        return '';
    }
  }
}