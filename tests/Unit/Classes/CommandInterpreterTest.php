<?php

namespace Devoted\MemoryDB\Tests\Unit\Classes;

use Devoted\MemoryDB\Classes\CommandInterpreter;
use Devoted\MemoryDB\Interfaces\DatabaseInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

class CommandInterpreterTest extends TestCase {
  use ProphecyTrait;

  public function testInvalidCommand(): void {

  }

  public function testGetCommandCallsGet(): void {
    $mockDb = $this->prophesize(DatabaseInterface::class);

    $name = 'the-name';

    $mockDb->get($name)->shouldBeCalledTimes(1);

    $commandLineInterpreter = new CommandInterpreter($mockDb->reveal());
    $commandLineInterpreter->runCommand("GET {$name}");
  }

  public function testSetCommandCallsSet(): void {
    self::fail("not yet");
  }

  public function testDeleteCommandCallsDelete(): void {
    self::fail("not yet");
  }

  public function testCountCommandCallsCount(): void {
    self::fail("not yet");
  }

  public function testBeginCommandCallsBegin(): void {
    self::fail("not yet");
  }

  public function testRollbackCommandCallsRollback(): void {
    self::fail("not yet");
  }

  public function testCommitCommandCallsCommit(): void {
    self::fail("not yet");
  }
}