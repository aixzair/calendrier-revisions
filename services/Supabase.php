<?php

namespace Services;

use CurlHandle;
use Exception;

class Supabase {
	private readonly string $ANON;
	private readonly string $API_URL;
	private CurlHandle $curl;

	public function __construct(string $url, string $anon) {
		$this->ANON = $anon;
		$this->API_URL = $url;
	}

	private function init(): void {
		$this->curl = curl_init();

		curl_setopt($this->curl, CURLOPT_HTTPHEADER, [
			'apikey: ' . $this->ANON,
			'Authorization: Bearer ' . $this->ANON,
			'Content-Type: application/json',
			'Prefer: return=minimal'
		]);

		// TODO : à régler
		curl_setopt($this->curl, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, 0);
	}

	private function close(): void {
		curl_close($this->curl);
	}

	/**
	 * @throws Exception
	 */
	public function selectAll(string $table): string {
		$this->init();

		$url = $this->API_URL . $table;
		curl_setopt($this->curl, CURLOPT_URL, $url);
		curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, 'GET');

		// Exécuter la requête
		$reponse = curl_exec($this->curl);

		// Vérifier les erreurs
		if (curl_errno($this->curl)) {
			$erreur = curl_error($this->curl);
			$this->close();
			throw new Exception("Curl error : $erreur.\n");
		}

		$this->close();
		return $reponse;
	}

	public function insert(string $table, string $json) {
		$this->init();

		$url = $this->API_URL . $table;
		curl_setopt($this->curl, CURLOPT_URL, $url);
		curl_setopt($this->curl, CURLOPT_POSTFIELDS, $json);

		curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, 'POST');

		// Exécuter la requête
		$reponse = curl_exec($this->curl);

		// Vérifier les erreurs
		if (curl_errno($this->curl)) {
			$erreur = curl_error($this->curl);
			$this->close();
			throw new Exception("Curl error : $erreur.\n");
		}

		$this->close();
	}
}
