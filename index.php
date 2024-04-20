<!DOCTYPE html>
<html lang="en">
<?php
// Inclure les fonctions
require_once 'php/functions.php';
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

	// Check si le magasin est ouvert
	$isOpen = isOpenOn($currentDateTime);
	if ($isOpen) {
		echo "La boutique est actuellement ouverte.";
	} else {
		echo "La boutique est actuellement fermée.";
	}

	// Appel de la méthode nextOpeningDate
	$nextOpening = nextOpeningDate($currentDateTime);
	if ($nextOpening) {
		$nextOpeningDateTime = new DateTime($nextOpening);

		echo '<br> La prochaine ouverture est prévue pour le ' . $nextOpeningDateTime->format('d.m.Y') . ' à ' . $nextOpeningDateTime->format('H\hi');
	} else {
		echo "<br> Aucune date d'ouverture future trouvée.";
	}
	?>
</body>

</html>