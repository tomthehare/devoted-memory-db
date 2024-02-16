<?php

namespace Devoted\MemoryDB\Tests\Unit;

use Devoted\MemoryDB\Classes\CommandInterpreter;
use Devoted\MemoryDB\Classes\MemoryDatabase;
use Devoted\MemoryDB\Classes\TransactionCoordinator;
use PHPUnit\Framework\TestCase;

class PdfTest extends TestCase {

  public static function dataProviderPdfTests(): array {
    return [
      'Example #1' => [
          [
            ['GET a', 'NULL'],
            ['SET a foo', null],
            ['SET b foo', null],
            ['COUNT foo', '2'],
            ['COUNT bar', '0'],
            ['DELETE a', null],
            ['COUNT foo', '1'],
            ['SET b baz', null],
            ['COUNT foo', '0'],
            ['GET b', 'baz'],
            ['GET B', 'NULL']
          ]
        ],
      'Example #2' => [
        [
          ['SET a foo', null],
          ['SET a foo', null],
          ['COUNT foo', '1'],
          ['GET a', 'foo'],
          ['DELETE a', null],
          ['GET a', 'NULL'],
          ['COUNT foo', '0']
        ]
      ],
      'Example #3' => [
        [
          ['BEGIN', null],
          ['SET a foo', null],
          ['GET a', 'foo'],
          ['BEGIN', null],
          ['SET a bar', null],
          ['GET a', 'bar'],
          ['SET a baz', null],
          ['ROLLBACK', null],
          ['GET a', 'foo'],
          ['ROLLBACK', null],
          ['GET a', 'NULL']
        ]
      ],
      'Example #4' => [
        [
          ['SET a foo', null],
          ['SET b baz', null],
          ['BEGIN', null],
          ['GET a', 'foo'],
          ['SET a bar', null],
          ['COUNT bar', '1'],
          ['BEGIN', null],
          ['COUNT bar', '1'],
          ['DELETE a', null],
          ['GET a', 'NULL'],
          ['COUNT bar', '0'],
          ['ROLLBACK', null],
          ['GET a', 'bar'],
          ['COUNT bar', '1'],
          ['COMMIT', null],
          ['GET a', 'bar'],
          ['GET b', 'baz']
        ]
      ]
    ];
  }

  /**
   * @dataProvider dataProviderPdfTests
   *
   * @param array $commandsWithExpectedOutput
   * @return void
   */
  public function testHarness(array $commandsWithExpectedOutput): void {
    $database = new MemoryDatabase();
    $transactionCoordinator = new TransactionCoordinator($database);
    $commandInterpreter = new CommandInterpreter($transactionCoordinator);

    foreach ($commandsWithExpectedOutput as $details) {
      $command = $details[0];
      $expectedOutput = $details[1];

      $output = $commandInterpreter->runCommand($command);

      self::assertEquals($expectedOutput, $output);
    }
  }
}