<?php

namespace Controllers;

use DateInterval;
use DateTime;
use DateTimeInterface;

use Models\Revision;
use Respect\Validation\Validator;

require 'vendor/autoload.php';

class RevisionController
extends BaseController {
	private static array $JOURS = [0, 1, 3, 5, 7, 14, 30, 45, 90, 120];

	public function create(): void {
		$erreurs = $_SESSION['erreurs'] ?? [];
		unset($_SESSION['erreurs']);

		echo $this->twig->render('revision/create.html.twig', [
			'erreurs' => $erreurs
		]);
	}

	public function createPost(): void {
		$nom = $_POST['nom'] ?? '';
		$debut = $_POST['debut'] ?? '';
		$erreurs = [];

		// Validation 'nom'
		if (!Validator::notEmpty()->validate($nom)) {
			$erreurs[] = "Le nom est obligatoire.";
		}

		// Validation 'debut'
		if (!Validator::date('Y-m-d')->validate($debut)) {
			$erreurs[] = "La date de début n'est pas valide.";
		} elseif (
			DateTime::createFromFormat('Y-m-d', $debut)
			< DateTime::createFromFormat('Y-m-d', '1970-01-01')
		) {
			$erreurs[] = "L'année doit être plus récente que 1970.";
		}

		// Si erreurs vers la page de création
		if (!empty($erreurs)) {
			$_SESSION['erreurs'] = $erreurs;
			header('Location: /revision/create');
			exit;
		}

		$this->creerProgramme($nom, DateTime::createFromFormat('Y-m-d', $debut));
		header('Location: /revision/programme');
	}

	public function programme(): void {
		echo $this->twig->render('revision/programme.html.twig', [
			'revisions' => Revision::select()
		]);
	}

	/**
	 * @param string $nom
	 * @param string $dateDebut date au format ISO 8601
	 * @return array
	 */
	public function creerProgramme(string $nom, DateTime $dateDebut): void {
		foreach (self::$JOURS as $jour) {
			$date = (new DateTime())->setTimestamp($dateDebut->getTimestamp())
				->add(new DateInterval("P{$jour}D"))
				->getTimestamp();
			$debut = (new DateTime())->setTimestamp($date)->setTime(20, 0)
				->format(DateTimeInterface::ISO8601);
			$fin = (new DateTime())->setTimestamp($date)->setTime(21, 0)
				->format(DateTimeInterface::ISO8601);

			$revision = new Revision();
			$revision->id_groupe = $nom;
			$revision->nom = $nom;
			$revision->j = $jour;
			$revision->debut = $debut;
			$revision->fin = $fin;

			Revision::insert($revision);
		}
	}
}