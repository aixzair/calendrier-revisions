<?php

namespace Models;

require 'vendor/autoload.php';
require 'BaseTable.php';

use DateTime;

class Revision extends BaseTable {
	public ?int $id;
	public string $id_groupe;
	public string $nom;
	public int $j;

	/** @var string $debut Date au format ISO 8601 */
	public string $debut;

	/** @var string $fin Data aut format ISO 8601 */
	public string $fin;
}
