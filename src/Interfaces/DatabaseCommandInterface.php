<?php

namespace Devoted\MemoryDB\Interfaces;

interface DatabaseCommandInterface {

  public function count(string $value): int;

  public function set(string $name, string $value): void;

  public function get(string $name): ?string;

  public function delete(string $name): void;
}