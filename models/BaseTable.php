<?php

namespace Models;

use Bdd\IBddService;
use Bdd\SupabaseAdaptateur;

require 'vendor/autoload.php';
require 'bdd/SupabaseAdaptateur.php';

abstract class BaseTable {
	private static ?IBddService $_bdd = null;

	private static function getBdd(): IBddService {
		if (is_null(self::$_bdd)) {
			self::$_bdd = new SupabaseAdaptateur();
		}
		return self::$_bdd;
	}

	/**
	 * Créé un nouvel objet
	 *
	 * @param $data
	 * @return void
	 */
	public static function create(array $datas): static {
		$instance = new (static::class)();
		foreach ($datas as $key => $value) {
			if (property_exists($instance, $key)) {
				$instance->$key = $value;
			}
		}
		return $instance;
	}

	public static function select(): array {
		$Enfant = static::class;
		$instances = [];
		foreach (self::getBdd()->select(self::getTableNom($Enfant)) as $data) {
			$instances[] = $Enfant::create($data);
		}
		return $instances;
	}

	public static function insert(BaseTable $instance): bool {
		return self::getBdd()->insert(self::getTableNom(static::class), $instance);
	}

	private static function getTableNom(string $class): string {
		$table = strtolower($class);
		$position = strrpos($table, '\\');
		if ($position !== false) {
			$table = substr($table, $position + 1);
		}
		return $table;
	}
}
