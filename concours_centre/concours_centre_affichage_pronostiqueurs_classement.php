<?php
	// Génération dynamique d'une image
	// Quelques paramètres
	$margeLaterale = 20;
	$margeVerticale = 20;
	$epaisseurRepere = 2;
	$rayonDisque = 6;
	
	if($nombrePoints == 0) {
		$nomFichierHTML = '';
		return;
	}
	
	$espaceInterValeur = floor(($zoneDessinLargeur - (2 * $margeLaterale)) / $nombrePoints / 20);
	if($espaceInterValeur == 0)
		$espaceInterValeur = 1;
	$espaceBarre = floor(($zoneDessinLargeur - (2 * $margeLaterale)) / $nombrePoints);
	$largeurBarre = floor(($zoneDessinLargeur - (2 * $margeLaterale)) / $nombrePoints);
	$largeurBarre -= $espaceInterValeur * 2;
	
	header('Content-type: image/png');

	//$image = imagecreate($zoneDessinLargeur, $zoneDessinHauteur);
	$image = imagecreatetruecolor($zoneDessinLargeur, $zoneDessinHauteur);
	imagesavealpha($image, true);
	$transparent = imagecolorallocatealpha($image, 0, 0, 0, 127);
	imagefill($image, 0, 0, $transparent);
	imageantialias($image, true);

	// La première allocation de couleur détermine également la couleur de fond de l'image
	$blanc = imagecolorallocate($image, 255, 255, 255);
	$noir = imagecolorallocate($image, 0, 0, 0);
	$vert = imagecolorallocate($image, 69, 122, 30);
	$orange = imagecolorallocate($image, 230, 127, 42);
	$gris = imagecolorallocate($image, 128, 128, 128);
	
	// La zone de dessin comporte une marge intérieure
	// Marge en hauteur et en largeur (dans cet exemple, deux valeurs de marge différentes)
	
	// Dessin du repère (ligne horizontale uniquement)
	// Compte tenu du fait que les valeurs d'espacement entre les barres et les barres sont systématiquement des valeurs entières, la largeur occupée par le graphique
	// sera différente d'un graphique à l'autre, ce qui implique de calculer la largeur du graphique pour dessiner le repère
	$largeurRepere = $espaceBarre * $nombrePoints;
	imagesetthickness($image, $epaisseurRepere);
	imageline($image, $margeLaterale, $zoneDessinHauteur - $margeVerticale - $epaisseurRepere, $margeLaterale + $largeurRepere, $zoneDessinHauteur - $margeVerticale - $epaisseurRepere, $noir);
	
	imagesetthickness($image, 1);
	
	for($i = 0; $i < $nombrePoints; $i++) {
		// Taille du texte
		$largeurTexteClassement = imagettfbbox(10, 0, '../polices/arial.ttf', $classements[$i]["Valeur"]);
		$largeurTexteJournee = imagettfbbox(10, 0, '../polices/arial.ttf', ($i + 1));
	
		$x1 = $margeLaterale + ($i * $espaceBarre) + $espaceInterValeur;
		$y1 = $margeVerticale + (($classements[$i]["Valeur"] - 1) * (($zoneDessinHauteur - 2 * $margeVerticale) / ($nombrePronostiqueurs - 1)));
		$x2 = $x1 + $largeurBarre;
		$y2 = $zoneDessinHauteur - $margeVerticale - $epaisseurRepere - 1;
		
		$xClassement = $margeLaterale + ($i * $espaceBarre) + ($espaceBarre / 2 - ($largeurTexteClassement[4] - $largeurTexteClassement[0]) / 2);
		$xJournee = $margeLaterale + ($i * $espaceBarre) + ($espaceBarre / 2 - ($largeurTexteJournee[4] - $largeurTexteJournee[0]) / 2);
		
		if($classements[$i]["Valeur"] == $classementMin) {
			// Cas particulier à gérer : si le pronostiqueur est dernier, il faut tout de même afficher une barre d'un trait
			if($classements[$i]["Valeur"] == $nombrePronostiqueurs)
				imagefilledrectangle($image, $x1, $y1 - 2, $x2, $y2, $vert);
			else
				imagefilledrectangle($image, $x1, $y1, $x2, $y2, $vert);
			imagettftext($image, 10, 0, $xClassement, $y1 - 2, $vert, '../polices/arial.ttf', $classements[$i]["Valeur"]);
		}
		else if($classements[$i]["Valeur"] == $classementMax) {
			// Cas particulier à gérer : si le pronostiqueur est dernier, il faut tout de même afficher une barre d'un trait
			if($classements[$i]["Valeur"] == $nombrePronostiqueurs) {
				imagefilledrectangle($image, $x1, $y2 - 1, $x2, $y2, $orange);
				imagettftext($image, 10, 0, $xClassement, $y1 - 8, $orange, '../polices/arial.ttf', $classements[$i]["Valeur"]);
			}
			else {
				imagefilledrectangle($image, $x1, $y1, $x2, $y2, $orange);
				imagettftext($image, 10, 0, $xClassement, $y1 - 2, $orange, '../polices/arial.ttf', $classements[$i]["Valeur"]);
			}
			
		}
		else {
			imagefilledrectangle($image, $x1, $y1, $x2, $y2, $gris);
			imagettftext($image, 10, 0, $xClassement, $y1 - 2, $gris, '../polices/arial.ttf', $classements[$i]["Valeur"]);
		}

		// Numéro de journée
		imagettftext($image, 8, 0, $xJournee, $zoneDessinHauteur - $epaisseurRepere - ($margeVerticale / 2) + 2, $noir, '../polices/arial.ttf', ($i + 1));
	}

	$nomFichier = $dossierImages . $unChampionnat["Championnat"] . '_' . $pronostiqueurConsulte . '_' . date('YmdHis') . '.png';
	$nomFichierHTML = $dossierImagesHTML . $unChampionnat["Championnat"] . '_' . $pronostiqueurConsulte . '_' . date('YmdHis') . '.png';
	
	imagepng($image, $nomFichier);
	imagedestroy($image);	

?>

