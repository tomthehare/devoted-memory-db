<?php

namespace Devoted\MemoryDB\Tests\Unit\Classes;

use Devoted\MemoryDB\Classes\CommandInterpreter;
use Devoted\MemoryDB\Classes\Command;
use Devoted\MemoryDB\Interfaces\DatabaseInterface;
use Devoted\MemoryDB\Utility\ColorfulConsoleLogger;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Log\NullLogger;

class CommandInterpreterTest extends TestCase {
  use ProphecyTrait;

  protected function getCommandInterpreter($database = null): CommandInterpreter {
    return new CommandInterpreter(
      $database ?? $this->prophesize(DatabaseInterface::class)->reveal(),
//      (new NullLogger())
      ColorfulConsoleLogger::getLogger()
    );
  }

  public function testInvalidCommand(): void {
    $commandInterpreter = $this->getCommandInterpreter();

    self::assertEquals(CommandInterpreter::RESPONSE_INVALID_COMMAND, $commandInterpreter->runCommand('NOT'));
    self::assertEquals(CommandInterpreter::RESPONSE_INVALID_COMMAND, $commandInterpreter->runCommand('NOT VALID'));
    self::assertEquals(CommandInterpreter::RESPONSE_INVALID_COMMAND, $commandInterpreter->runCommand('NOT VALID EITHER'));
  }

  public function testGetCommandCallsGet(): void {
    $mockDb = $this->prophesize(DatabaseInterface::class);

    $name = 'the-name';
    $mockDb->get($name)->willReturn('something')->shouldBeCalledTimes(2);

    $commandLineInterpreter = $this->getCommandInterpreter($mockDb->reveal());
    $commandLineInterpreter->runCommand("GET {$name}");
    $commandLineInterpreter->runCommand("get {$name}");
  }

  public function testSetCommandCallsSet(): void {
    $mockDb = $this->prophesize(DatabaseInterface::class);
    $name = 'the-name';
    $value = 'the-value';
    $mockDb->set($name, $value)->shouldBeCalledTimes(2);

    $commandLineInterpreter = $this->getCommandInterpreter($mockDb->reveal());

    $commandLineInterpreter->runCommand("SET {$name} {$value}");
    $commandLineInterpreter->runCommand("set {$name} {$value}");
  }

  public function testDeleteCommandCallsDelete(): void {
    $mockDb = $this->prophesize(DatabaseInterface::class);

    $name = 'the-name';
    $mockDb->delete($name)->shouldBeCalledTimes(2);

    $commandLineInterpreter = $this->getCommandInterpreter($mockDb->reveal());
    $commandLineInterpreter->runCommand("DELETE {$name}");
    $commandLineInterpreter->runCommand("delete {$name}");
  }

  public function testCountCommandCallsCount(): void {
    $mockDb = $this->prophesize(DatabaseInterface::class);

    $value = 'the-value';
    $mockDb->count($value)->shouldBeCalledTimes(2);

    $commandLineInterpreter = $this->getCommandInterpreter($mockDb->reveal());
    $commandLineInterpreter->runCommand("COUNT {$value}");
    $commandLineInterpreter->runCommand("count {$value}");
  }

  public function testBeginCommandCallsBegin(): void {
    $mockDb = $this->prophesize(DatabaseInterface::class);

    $mockDb->beginTransaction()->shouldBeCalledTimes(2);

    $commandLineInterpreter = $this->getCommandInterpreter($mockDb->reveal());
    $commandLineInterpreter->runCommand("BEGIN");
    $commandLineInterpreter->runCommand("begin");
  }

  public function testRollbackCommandCallsRollback(): void {
    $mockDb = $this->prophesize(DatabaseInterface::class);

    $mockDb->rollbackTransaction()->shouldBeCalledTimes(2);

    $commandLineInterpreter = $this->getCommandInterpreter($mockDb->reveal());
    $commandLineInterpreter->runCommand("ROLLBACK");
    $commandLineInterpreter->runCommand("rollback");
  }

  public function testCommitCommandCallsCommit(): void {
    $mockDb = $this->prophesize(DatabaseInterface::class);

    $mockDb->commitTransactions()->shouldBeCalledTimes(2);

    $commandLineInterpreter = $this->getCommandInterpreter($mockDb->reveal());
    $commandLineInterpreter->runCommand("COMMIT");
    $commandLineInterpreter->runCommand("commit");
  }
}