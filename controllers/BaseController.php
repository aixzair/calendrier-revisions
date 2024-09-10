<?php

namespace Controllers;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

require 'vendor/autoload.php';

abstract class BaseController {
	protected readonly Environment $twig;

	public function __construct() {
		$loader = new FilesystemLoader(__DIR__ . '/../vues');
		$this->twig = new Environment($loader, [
			'cache' => false,
		]);
	}
}