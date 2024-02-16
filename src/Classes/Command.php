<?php

namespace Devoted\MemoryDB\Classes;

class Command {

  const COMMAND_SET = 'SET';
  const COMMAND_GET = 'GET';

  const COMMAND_DELETE = 'DELETE';

  const COMMAND_COUNT = 'COUNT';

  const COMMAND_END = 'END';

  const COMMAND_BEGIN = 'BEGIN';

  const COMMAND_ROLLBACK = 'ROLLBACK';

  const COMMAND_COMMIT = 'COMMIT';

  private string $rawCommand;

  private string $commandVerb;

  private string $input1;

  private string $input2;

  public function __construct(string $command) {
    $this->rawCommand = $command;

    $this->parseCommand();
  }

  public function commandVerb(): string {
    return $this->commandVerb;
  }

  public function input1(): string {
    return $this->input1;
  }

  public function getInput2(): string {
    return $this->input2;
  }

  public function isValid(): bool {
    if (empty($this->commandVerb)) {
      return false;
    }

    if (!in_array($this->commandVerb, $this->getAllCommands())) {
      return false;
    }

    if (in_array($this->commandVerb, $this->getCommandsRequiringOneInput()) && empty($this->input1)) {
      return false;
    }

    if (in_array($this->commandVerb, $this->getCommandsRequiringTwoInputs()) && empty($this->input2)) {
      return false;
    }

    return true;
  }

  private function getAllCommands(): array {
    return array_merge(
      $this->getCommandsRequiringNoInput(),
      $this->getCommandsRequiringOneInput(),
      $this->getCommandsRequiringTwoInputs()
    );
  }

  private function getCommandsRequiringNoInput(): array {
    return [
      self::COMMAND_END,
      self::COMMAND_BEGIN,
      self::COMMAND_ROLLBACK,
      self::COMMAND_COMMIT
    ];
  }

  private function getCommandsRequiringOneInput(): array {
    return [
      self::COMMAND_COUNT,
      self::COMMAND_GET,
      self::COMMAND_DELETE
    ];
  }

  private function getCommandsRequiringTwoInputs(): array {
    return [
      self::COMMAND_SET
    ];
  }

  private function parseCommand(): void {
    $parts = explode(' ', $this->rawCommand);

    $this->commandVerb = strtoupper($parts[0] ?? '');
    $this->input1 = $parts[1] ?? '';
    $this->input2 = $parts[2] ?? '';
  }

  public function toJson(): string {
    return json_encode($this->toArray());
  }
  public function toArray(): array
  {
    return [
      'commandVerb' => $this->commandVerb,
      'input1' => $this->input1,
      'input2' => $this->input2
    ];
  }
}