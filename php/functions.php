<?php

// Définition du fuseau horaire
date_default_timezone_set('Europe/Zurich');
// Définition de la locale en français
setlocale(LC_TIME, 'fr_FR.utf8', 'fra');

/**
 * Fonction pour vérifier si le magasin est ouvert à une certaine date et heure
 * @param mixed $date - Date à vérifier
 * @return boolean - Retourne true si le magasin est ouvert
 */
function isOpenOn($date)
{
	$openingHours = json_decode(file_get_contents('php/opening_dates.json'), true);

	// Mise au format "D -> 'Sat'" pour la date reçue
	$dayOfWeek = date('D', strtotime($date));

	// Check si le jour existe dans la liste des horaires d'ouvertures
	if (array_key_exists($dayOfWeek, $openingHours)) {
		$openingHoursOfDay = $openingHours[$dayOfWeek];
		if (!empty($openingHoursOfDay)) {
			foreach ($openingHoursOfDay as $hours) {
				list($open, $close) = explode(' - ', $hours);
				$currentTime = date('H:i', strtotime($date));
				if ($currentTime >= $open && $currentTime < $close) {
					return true;
				}
			}
		}
	}
	return false;
}

/**
 * Fonction pour récupérer la prochaine date d'ouverture
 * @param string $date - Date du jour
 * @return string - Date de la prochaine ouverture
 */
function nextOpeningDate($date)
{
	$openingHours = json_decode(file_get_contents('php/opening_dates.json'), true);

	$currentDay = strtotime($date);

	do {
		$nextDate = strtotime("+0 day", $currentDay);
		$currentWeekDay = date('D', $currentDay);

		// Check si le jour existe et si le jour possède des heures d'ouverture
		if (array_key_exists($currentWeekDay, $openingHours) && count($openingHours[$currentWeekDay]) > 0) {
			// Check si le jour possède plusieurs heures d'ouvertures
			if (count($openingHours[$currentWeekDay]) > 1) {
				list($openHour, $closeHour) = explode(' - ', $openingHours[$currentWeekDay][1]);

				// Check si l'heure reçue est la deuxième heure d'ouverture du jour
				if (date('H:i', $currentDay) >= $openHour && date('H:i', $currentDay) <= $closeHour) {
					$nextDate = strtotime("+1 day", $currentDay);

					// Check si le jour suivant est un dimanche
					if (date('D', $nextDate) === 'Sun') {
						// Ajoute +2 jour pour retourner le lundi
						$nextDate = strtotime("+2 day", $currentDay);
					}

					$nextWeekDay = date('D', $nextDate);
					list($openHour, $closeHour) = explode(' - ', $openingHours[$nextWeekDay][0]);
				}
			} else {
				$nextDate = strtotime("+1 day", $currentDay);

				// Check si le jour suivant est un dimanche
				if (date('D', $nextDate) === 'Sun') {
					// Ajoute +2 jour pour retourner le lundi
					$nextDate = strtotime("+2 day", $currentDay);
				}

				$nextWeekDay = date('D', $nextDate);
				list($openHour, $closeHour) = explode(' - ', $openingHours[$nextWeekDay][0]);
			}
		}
		return date('Y-m-d H:i', strtotime(date('Y-m-d', $nextDate) . ' ' . $openHour));
	} while (true);
}

// Dates pour validation
$wednesday = '2024-02-21 07:45:00';
$thursday = '2024-02-22 12:22:11';
$saturday = '2024-02-24 09:15:00';
$sunday = '2024-02-25 09:15:00';
$fridayMorning = '2024-02-23 08:00:00';
$mondayMorning = '2024-02-26 08:00:00';
$thursdayAfternoon = '2024-02-22 14:00:00';

// Vérifications
assert(isOpenOn($wednesday) === false);
assert(isOpenOn($thursday) === false);
assert(isOpenOn($sunday) === false);
assert(nextOpeningDate($thursdayAfternoon) === '2024-02-23 08:00');
assert(nextOpeningDate($saturday) === '2024-02-26 08:00');
assert(nextOpeningDate($thursday) === '2024-02-22 14:00');
