<?php

namespace Bdd;

use Exception;
use Services\Supabase;

require 'vendor/autoload.php';

class SupabaseAdaptateur
implements IBddService {
	private Supabase $supebase;

	public function __construct() {
		// ENV : SUPABASE_URL
		if (!isset($_ENV['SUPABASE_URL'])) {
			throw new Exception('.env : élément SUPABASE_URL introuvable.');
		}
		$url = $_ENV['SUPABASE_URL'];

		// ENV : SUPABASE_ANON_KEY
		if (!isset($_ENV['SUPABASE_ANON_KEY'])) {
			throw new Exception('.env : élément SUPABASE_ANON_KEY introuvable.');
		}
		$anon = $_ENV['SUPABASE_ANON_KEY'];

		$this->supebase = new Supabase($url, $anon);
	}

	public function select(string $table): array {
		$lignes = json_decode($this->supebase->selectAll($table), true);
		if (json_last_error() !== JSON_ERROR_NONE) {
			// TODO : à modifier
			throw new Exception('Erreur lors du décodage de la réponse JSON');
		}
		return $lignes;
	}

	public function insert(string $table, object $instance, $colId = 'id'): bool {
		$data = (array) $instance;
		unset($data[$colId]);

		try {
			$this->supebase->insert($table, json_encode($data));
		} catch (Exception $exception) {
			// TODO : log
			return false;
		}
		return true;
	}
}