<?php

namespace Apis;

require 'vendor/autoload.php';

use DateTime;
use Eluceo\iCal\Domain\Entity\Calendar;
use Eluceo\iCal\Domain\Entity\Event;
use Eluceo\iCal\Domain\Entity\TimeZone;
use Eluceo\iCal\Domain\ValueObject\DateTime as IDateTime;
use Eluceo\iCal\Domain\ValueObject\TimeSpan;
use Eluceo\iCal\Presentation\Factory\CalendarFactory;

use Models\Revision;


class RevisionAPI {
	public function calendrier(): void {
		$calendrier = new Calendar();
		$calendrier->addTimeZone(new TimeZone('Europe/Paris'));

		/** @var Revision $revision */
		foreach (Revision::select() as $revision) {
			$event = new Event();
			$event->setSummary("J$revision->j : $revision->nom");
			$event->setOccurrence(new TimeSpan(
				new IDateTime(new DateTime($revision->debut), 0),
				new IDateTime(new DateTime($revision->fin), 0)
			));

			$calendrier->addEvent($event);
		}

		ob_clean();

		header('Content-Type: text/calendar; charset=utf-8');
		header('Content-Disposition: attachment; filename="ical.ics"');

		echo (new CalendarFactory())->createCalendar($calendrier);
	}
}
