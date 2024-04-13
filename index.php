<!DOCTYPE html>
<html lang="en">
<?php
require_once 'php/functions.php';

// Lecture du contenu JSON depuis le fichier
$jsonPath = 'php/opening_dates.json';
$jsonData = file_get_contents($jsonFilePath);
$openingHoursData = json_decode($jsonData, true);
?>

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Magasin CPNV</title>
</head>

<body>
	<h1>Magasin CPNV</h1>
	<?php
	// Date du jour + formattage
	$currentDateTime = new DateTime();
	$currentDateTime = date('Y-m-d H:i:s');

	$isOpen = isOpenOn($openingHoursData, $currentDateTime);

	echo "La boutique est actuellement ";
	echo $isOpen ? "ouverte." : "fermée. <br>";

	// Appel de la méthode nextOpeningDate
	$nextOpening = nextOpeningDate($openingHoursData, $currentDateTime);
	if ($nextOpening) {
		// Convertir la chaîne de date en objet DateTime
		$nextOpeningDateTime = new DateTime($nextOpening);

		// Formater la date et l'heure dans le format spécifié
		$formattedDate = $nextOpeningDateTime->format('d.m.Y');
		$formattedTime = $nextOpeningDateTime->format('H\hi');

		echo "La prochaine ouverture est prévue pour le $formattedDate à $formattedTime";
	} else {
		echo "Aucune date d'ouverture future trouvée.";
	}
	?>
</body>

</html>