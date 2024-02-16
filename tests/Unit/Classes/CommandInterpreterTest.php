<?php

namespace Devoted\MemoryDB\Tests\Unit\Classes;

use Devoted\MemoryDB\Classes\CommandInterpreter;
use Devoted\MemoryDB\Classes\TransactionCoordinator;
use Devoted\MemoryDB\Interfaces\DatabaseCommandInterface;
use Devoted\MemoryDB\Utility\ColorfulConsoleLogger;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Log\NullLogger;

class CommandInterpreterTest extends TestCase {
  use ProphecyTrait;

  protected function getCommandInterpreter($database = null): CommandInterpreter {
    $transactionCoordinator = new TransactionCoordinator($database ?? $this->prophesize(DatabaseCommandInterface::class)->reveal());

    return new CommandInterpreter(
      $transactionCoordinator,
      (new NullLogger())
    );
  }

  public function testInvalidCommand(): void {
    $commandInterpreter = $this->getCommandInterpreter();

    self::assertEquals(CommandInterpreter::RESPONSE_INVALID_COMMAND, $commandInterpreter->runCommand('NOT'));
    self::assertEquals(CommandInterpreter::RESPONSE_INVALID_COMMAND, $commandInterpreter->runCommand('NOT VALID'));
    self::assertEquals(CommandInterpreter::RESPONSE_INVALID_COMMAND, $commandInterpreter->runCommand('NOT VALID EITHER'));
  }

  public function testGetCommandCallsGet(): void {
    $mockDb = $this->prophesize(DatabaseCommandInterface::class);

    $name = 'the-name';
    $mockDb->get($name)->willReturn('something')->shouldBeCalledTimes(2);

    $commandLineInterpreter = $this->getCommandInterpreter($mockDb->reveal());
    $commandLineInterpreter->runCommand("GET {$name}");
    $commandLineInterpreter->runCommand("get {$name}");
  }

  public function testSetCommandCallsSet(): void {
    $mockDb = $this->prophesize(DatabaseCommandInterface::class);
    $name = 'the-name';
    $value = 'the-value';
    $mockDb->set($name, $value)->shouldBeCalledTimes(2);

    $commandLineInterpreter = $this->getCommandInterpreter($mockDb->reveal());

    $commandLineInterpreter->runCommand("SET {$name} {$value}");
    $commandLineInterpreter->runCommand("set {$name} {$value}");
  }

  public function testDeleteCommandCallsDelete(): void {
    $mockDb = $this->prophesize(DatabaseCommandInterface::class);

    $name = 'the-name';
    // There is a GET call prior to the DELETE happening in order to coordinate transactions and to check if the delete actually needs to occur.
    $mockDb->get($name)->shouldBeCalledTimes(2);;
    $mockDb->delete($name)->shouldBeCalledTimes(2);

    $commandLineInterpreter = $this->getCommandInterpreter($mockDb->reveal());
    $commandLineInterpreter->runCommand("DELETE {$name}");
    $commandLineInterpreter->runCommand("delete {$name}");
  }

  public function testCountCommandCallsCount(): void {
    $mockDb = $this->prophesize(DatabaseCommandInterface::class);

    $value = 'the-value';
    $mockDb->count($value)->shouldBeCalledTimes(2);

    $commandLineInterpreter = $this->getCommandInterpreter($mockDb->reveal());
    $commandLineInterpreter->runCommand("COUNT {$value}");
    $commandLineInterpreter->runCommand("count {$value}");
  }

  public function testBeginCommandCallsBegin(): void {
    $mockTC = $this->prophesize(TransactionCoordinator::class);

    $mockTC->beginTransaction()->shouldBeCalled();

    $commandLineInterpreter = new CommandInterpreter($mockTC->reveal(), (new NullLogger()));
    $commandLineInterpreter->runCommand("BEGIN");
    $commandLineInterpreter->runCommand("begin");
  }

  public function testRollbackCommandCallsRollback(): void {
    $mockTC = $this->prophesize(TransactionCoordinator::class);

    $mockTC->rollbackTransaction()->shouldBeCalled();

    $commandLineInterpreter = new CommandInterpreter($mockTC->reveal(), (new NullLogger()));
    $commandLineInterpreter->runCommand("ROLLBACK");
    $commandLineInterpreter->runCommand("rollback");
  }

  public function testCommitCommandCallsCommit(): void {
    $mockTC = $this->prophesize(TransactionCoordinator::class);

    $mockTC->commitTransactions()->shouldBeCalled();

    $commandLineInterpreter = new CommandInterpreter($mockTC->reveal(), (new NullLogger()));
    $commandLineInterpreter->runCommand("COMMIT");
    $commandLineInterpreter->runCommand("commit");
  }
}