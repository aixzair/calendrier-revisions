<?php

namespace Bdd;

interface IBddService {
	public function select(string $table): array;
	public function insert(string $table, object $instance, $colId = 'id'): bool;
}
