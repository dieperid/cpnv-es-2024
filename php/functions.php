<?php

// Définition du fuseau horaire
date_default_timezone_set('Europe/Zurich');
// Définition de la locale en français
setlocale(LC_TIME, 'fr_FR.utf8', 'fra');

// Chemin du fichier JSON
$jsonFilePath = 'php/opening_dates.json';

// Vérification de l'existence du fichier
if (!file_exists($jsonFilePath)) {
	echo "Le fichier JSON n'existe pas.";
	exit;
}

// Lecture du contenu JSON depuis le fichier
$jsonData = file_get_contents($jsonFilePath);

// Décodage du JSON
$openingHoursData = json_decode($jsonData, true);

// Vérification si le décodage a réussi
if ($openingHoursData === null && json_last_error() !== JSON_ERROR_NONE) {
	echo "Erreur lors du décodage du JSON.";
	exit;
}

// Fonction pour vérifier si le magasin est ouvert à une certaine date et heure
function isOpenOn($openingHoursData, $date)
{
	// Mise au format "D -> 'Sat'" pour la date reçue
	$dayOfWeek = date('D', strtotime($date));

	// Check si le jour existe dans la liste des horaires d'ouvertures
	if (array_key_exists($dayOfWeek, $openingHoursData)) {
		$openingHours = $openingHoursData[$dayOfWeek];
		if (!empty($openingHours)) {
			foreach ($openingHours as $hours) {
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

// Fonction pour obtenir la prochaine date d'ouverture
function nextOpeningDate($openingHoursData, $date)
{
	$currentTime = strtotime($date);
	$currentDayOfWeek = date('D', $currentTime);
	$daysOfWeek = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];

	// Vérifier si la journée actuelle possède plus d'une plage horaire
	if (isset($openingHoursData[$currentDayOfWeek]) && count($openingHoursData[$currentDayOfWeek]) > 1) {
		$secondOpeningHour = $openingHoursData[$currentDayOfWeek][1];
		$openHour = explode(' - ', $secondOpeningHour)[0];
		$closeHour = explode(' - ', $secondOpeningHour)[1];

		// Concaténer la date avec l'heure
		$openingDateTimeString = date('Y-m-d', $currentTime) . " $openHour";
		$closingDateTimeString = date('Y-m-d', $currentTime) . " $closeHour";

		if ($currentTime >= strtotime($openingDateTimeString)  && $currentTime <= strtotime($closingDateTimeString)) {
			for ($i = 1; $i <= 7; $i++) {
				$nextDayOfWeek = $daysOfWeek[date('N', strtotime("+ $i day", $currentTime)) - 1];

				if (isset($openingHoursData[$nextDayOfWeek]) && !empty($openingHoursData[$nextDayOfWeek])) {
					$openingHours = $openingHoursData[$nextDayOfWeek];
					$firstOpeningHour = reset($openingHours);
					$openHour = explode(' - ', $firstOpeningHour)[0];

					$dateTimeString = date('Y-m-d', strtotime("+ $i day", $currentTime)) . " $openHour";
					return date('Y-m-d H:i:s', strtotime($dateTimeString));
				}
			}
		}
		return date('Y-m-d H:i:s', strtotime($openingDateTimeString));
	}

	for ($i = 1; $i <= 7; $i++) {
		$nextDayOfWeek = $daysOfWeek[date('N', strtotime("+ $i day", $currentTime)) - 1];

		if (isset($openingHoursData[$nextDayOfWeek]) && !empty($openingHoursData[$nextDayOfWeek])) {
			$openingHours = $openingHoursData[$nextDayOfWeek];
			$firstOpeningHour = reset($openingHours);
			$openHour = explode(' - ', $firstOpeningHour)[0];

			$dateTimeString = date('Y-m-d', strtotime("+ $i day", $currentTime)) . " $openHour";
			return date('Y-m-d H:i:s', strtotime($dateTimeString));
		}
	}
	return null;
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
assert(isOpenOn($openingHoursData, $wednesday) === false);
assert(isOpenOn($openingHoursData, $thursday) === false);
assert(isOpenOn($openingHoursData, $sunday) === false);
assert(nextOpeningDate($openingHoursData, $thursdayAfternoon) === $fridayMorning);
assert(nextOpeningDate($openingHoursData, $saturday) === $mondayMorning);
assert(nextOpeningDate($openingHoursData, $thursday) === $thursdayAfternoon);

// echo "Toutes les assertions sont vérifiées.\n";
