<?php
	include_once('commun_administrateur.php');

	// Mise en place des paramètres de l'interface
	$largeur = 1360;
	$largeurCarreau = 20;
	$hauteurCarreau = 20;

	// Numéro de journée
	$ordreSQL =		'	SELECT      MAX(Journee) AS Journee' .
					'	FROM        journees' .
					'	JOIN        matches' .
					'	            ON      journees.Journee = matches.Journees_Journee' .
					'	WHERE       journees.Championnats_Championnat = 1' .
					'	            AND     matches.Matches_ScoreEquipeDomicile IS NOT NULL' .
					'	            AND     matches.Matches_ScoreEquipeVisiteur IS NOT NULL';
	$req = $bdd->query($ordreSQL);
	$journees = $req->fetchAll();
	$journee = $journees[0]["Journee"];

	// Lecture des pronostiqueurs ayant un classement en Ligue 1 compris entre la cinquième et la quatorzième place
	$ordreSQL =		'	SELECT		pronostiqueurs.Pronostiqueur, pronostiqueurs.Pronostiqueurs_NomUtilisateur' .
					'	FROM		pronostiqueurs' .
					'	JOIN		classements' .
					'				ON		pronostiqueurs.Pronostiqueur = classements.Pronostiqueurs_Pronostiqueur' .
					'	JOIN		(' .
					'					SELECT		Journees_Journee, MAX(Classements_DateReference) AS Classements_DateReference' .
					'					FROM		classements' .
					'					WHERE		Journees_Journee = ' . $journee .
					'				) classements_max' .
					'				ON		classements.Journees_Journee = classements_max.Journees_Journee' .
					'						AND		classements.Classements_DateReference = classements_max.Classements_DateReference' .
					'	WHERE		classements.Classements_ClassementGeneralMatch >= 5' .
					'				AND		classements.Classements_ClassementGeneralMatch <= 14';
	$req = $bdd->query($ordreSQL);
	$pronostiqueurs5A14 = $req->fetchAll();

	// Confrontations
	$ordreSQL =		'	SELECT		confrontations.Confrontation' .
					'				,confrontations.Confrontations_NomCourt' .
					'				,confrontations.Pronostiqueurs_PronostiqueurA, confrontations.Pronostiqueurs_PronostiqueurB' .
					'				,confrontations.Confrontations_NumeroCaseA, confrontations.Confrontations_NumeroCaseB' .
					'				,IFNULL(pronostiqueursA.Pronostiqueurs_NomUtilisateur, \'-\') AS PronostiqueursA_NomUtilisateur, IFNULL(pronostiqueursA.Pronostiqueurs_Photo, \'_inconnu.png\') AS PronostiqueursA_Photo' .
					'				,IFNULL(pronostiqueursB.Pronostiqueurs_NomUtilisateur, \'-\') AS PronostiqueursB_NomUtilisateur, IFNULL(pronostiqueursB.Pronostiqueurs_Photo, \'_inconnu.png\') AS PronostiqueursB_Photo' .
					'				,IFNULL(Confrontations_ScorePronostiqueurA, \'-\') AS Confrontations_ScorePronostiqueurA' .
					'				,IFNULL(Confrontations_ScorePronostiqueurB, \'-\') AS Confrontations_ScorePronostiqueurB' .
					'				,confrontations.Pronostiqueurs_Vainqueur' .
					'				,confrontations.Confrontations_DecalageY' .
					'				,confrontations.Confrontations_DecalageX' .
					'				,confrontations.Confrontations_ConfrontationReelle' .
					'	FROM		journees' .
					'	JOIN		confrontations' .
					'				ON		journees.Journee = confrontations.Journees_Journee' .
					'	LEFT JOIN	pronostiqueurs pronostiqueursA' .
					'				ON		confrontations.Pronostiqueurs_PronostiqueurA = pronostiqueursA.Pronostiqueur' .
					'	LEFT JOIN	pronostiqueurs pronostiqueursB' .
					'				ON		confrontations.Pronostiqueurs_PronostiqueurB = pronostiqueursB.Pronostiqueur' .
					'	WHERE		journees.Championnats_Championnat = 5' .
					'	ORDER BY	confrontations.Confrontations_DecalageY, confrontations.Confrontations_DecalageX';
					
	$req = $bdd->query($ordreSQL);
	$confrontations = $req->fetchAll();
	
	// Branches reliant les confrontations
	$ordreSQL =		'	SELECT		Confrontations_Confrontation' .
					'				,ConfrontationsBranches_DecalageX, ConfrontationsBranches_DecalageY' .
					'				,ConfrontationsBranches_Largeur, ConfrontationsBranches_Hauteur' .
					'				,ConfrontationsBranches_ClasseBordure' .
					'	FROM		confrontations_branches' .
					'	ORDER BY	ConfrontationsBranches_DecalageY, ConfrontationsBranches_DecalageX';
	$req = $bdd->query($ordreSQL);
	$confrontationsBranches = $req->fetchAll();

	$ordreSQL =		'	SELECT		IFNULL(CDF_AdresseVideo, \'\') AS  CDF_AdresseVideo' .
					'	FROM		cdf_adresse_video' .
					'	LIMIT		1';
	$req = $bdd->query($ordreSQL);
	$adresse = $req->fetchAll();
	$adresseVideo = $adresse[0]["CDF_AdresseVideo"];

	// Copie des quatre premiers de L1 et des exemptés
	echo '<div style="text-align: center;">';
		echo '<label>Vidéo en direct :&nbsp;&nbsp;</label><input type="text" value="' . $adresseVideo . '" onchange="gererCDF_modifierAdresseVideo(this);" />';
		echo '<br />';
		echo '<label class="bouton" onclick="gererCDF_reinitialiserConfrontations();">Tout réinitialiser</label>';
		echo '<br />';
		echo '<label class="bouton" onclick="gererCDF_placerPronostiqueurs1A4();">Placer les quatre premiers de Ligue 1</label>';
	echo '</div>';
	echo '<br /><br /><br />';
	
	// Affichage des branches reliant les confrontations
	foreach($confrontationsBranches as $uneBranche) {
		$hauteurBordure = 1;
		switch($uneBranche["ConfrontationsBranches_ClasseBordure"]) {
			case 1: $classe = 'confrontation--branche-gauche-bas'; break;
			case 2: $classe = 'confrontation--branche-gauche-haut'; break;
			case 3: $classe = 'confrontation--branche-droite-bas'; break;
			case 4: $classe = 'confrontation--branche-droite-haut'; break;
			case 5: $classe = 'confrontation--branche-demi--gauche'; $hauteurBordure = 2; break;
			case 6: $classe = 'confrontation--branche-demi--droite'; $hauteurBordure = 2; break;
		}
		
		$x = $uneBranche["ConfrontationsBranches_DecalageX"] * $largeurCarreau;
		$y = $uneBranche["ConfrontationsBranches_DecalageY"] * $hauteurCarreau;
		
		// Attention, ne pas oublier de soustraire la taille de la bordure
		$largeur = $uneBranche["ConfrontationsBranches_Largeur"] * $largeurCarreau - 2;
		$hauteur = $uneBranche["ConfrontationsBranches_Hauteur"] * $hauteurCarreau - $hauteurBordure;
		echo '<span class="confrontation--branche ' . $classe . '" style="left: ' . $x . 'px; top: ' . $y . 'px; width: ' . $largeur . 'px; height: ' . $hauteur . 'px"></span>';
	}
	
	// Affichage des confrontations
	foreach($confrontations as $uneConfrontation) {
		$x = $uneConfrontation["Confrontations_DecalageX"] * $largeurCarreau;
		$y = $uneConfrontation["Confrontations_DecalageY"] * $hauteurCarreau;

		// Différenciation visuelle (et fonctionnelle) entre les confrontations réelles et celles qui n'ont pas lieu (car qualification d'office pour le tour suivant)
		$confrontation = $uneConfrontation["Confrontation"];
		switch($confrontation) {
			case $confrontation <= 32:		$classeEnveloppeTour = 'confrontation--enveloppe32'; break;
			case $confrontation <= 48:		$classeEnveloppeTour = 'confrontation--enveloppe16'; break;
			case $confrontation <= 56:		$classeEnveloppeTour = 'confrontation--enveloppe8'; break;
			case $confrontation <= 60:		$classeEnveloppeTour = 'confrontation--enveloppe4'; break;
			case $confrontation <= 62:		$classeEnveloppeTour = 'confrontation--enveloppe2'; break;
			case $confrontation = 63:		$classeEnveloppeTour = 'confrontation--enveloppe1'; break;
			default: $classeEnveloppeTour = '';
		}

		$classeEnveloppe = 'confrontation--enveloppe ' . $classeEnveloppeTour;
		
		if($uneConfrontation["Confrontations_ConfrontationReelle"] == 1) {
			echo '<div class="' . $classeEnveloppe . '" style="left: ' . ($x - 1) . 'px; top: ' . ($y - 1) . 'px;">';
				echo '<div class="confrontation confrontation-reelle" style="left: ' . $x . 'px; top: ' . $y . 'px; height: ' . $hauteurCarreau . 'px" onclick="gerer_cdf_selectionnerPronostiqueur(' . $uneConfrontation["Confrontations_NumeroCaseA"] . ');">';
					echo '<img class="confrontation--image" src="images/pronostiqueurs/' . $uneConfrontation["PronostiqueursA_Photo"] . '" alt="" />';
					if($uneConfrontation["Pronostiqueurs_PronostiqueurA"] != null) {
						if($uneConfrontation["Pronostiqueurs_Vainqueur"] == $uneConfrontation["Pronostiqueurs_PronostiqueurA"] && $uneConfrontation["Pronostiqueurs_Vainqueur"] != null)
							echo '<label class="confrontation--vainqueur confrontation--nom">' . $uneConfrontation["PronostiqueursA_NomUtilisateur"] . '</label>';
						else
							echo '<label class="confrontation--nom">' . $uneConfrontation["PronostiqueursA_NomUtilisateur"] . '</label>';
					}
					else
						echo '<label class="confrontation--nom">' . $uneConfrontation["Confrontations_NumeroCaseA"] . '</label>';

					echo '<label class="confrontation--score">' . $uneConfrontation["Confrontations_ScorePronostiqueurA"] . '</label>';
				echo '</div>';
				
				echo '<div class="confrontation-reelle" style="left: ' . $x . 'px; top: ' . ($y + $hauteurCarreau) . 'px; height: ' . $hauteurCarreau . 'px" onclick="gerer_cdf_selectionnerPronostiqueur(' . $uneConfrontation["Confrontations_NumeroCaseB"] . ');">';
					echo '<img class="confrontation--image" src="images/pronostiqueurs/' . $uneConfrontation["PronostiqueursB_Photo"] . '" alt="" />';
					if($uneConfrontation["Pronostiqueurs_PronostiqueurB"] != null) {
						if($uneConfrontation["Pronostiqueurs_Vainqueur"] == $uneConfrontation["Pronostiqueurs_PronostiqueurB"] && $uneConfrontation["Pronostiqueurs_Vainqueur"] != null)
							echo '<label class="confrontation--vainqueur confrontation--nom">' . $uneConfrontation["PronostiqueursB_NomUtilisateur"] . '</label>';
						else
							echo '<label class="confrontation--nom">' . $uneConfrontation["PronostiqueursB_NomUtilisateur"] . '</label>';
					}
					else
						echo '<label class="confrontation--nom">' . $uneConfrontation["Confrontations_NumeroCaseB"] . '</label>';

					echo '<label class="confrontation--score">' . $uneConfrontation["Confrontations_ScorePronostiqueurB"] . '</label>';
				echo '</div>';
			echo '</div>';
		}
		else {
			echo '<div class="' . $classeEnveloppe . '" style="left: ' . ($x - 1) . 'px; top: ' . ($y - 1) . 'px;" >';
				echo '<div class="confrontation-fictive" style="left: ' . $x . 'px; top: ' . $y . 'px;">';
					echo '<label class="confrontation--titre">' . $uneConfrontation["Confrontations_NomCourt"] . '</label>';
				echo '</div>';
				
				echo '<div class="confrontation-fictive" style="left: ' . $x . 'px; top: ' . ($y + $hauteurCarreau) . 'px;" onclick="gerer_cdf_selectionnerPronostiqueur(' . $uneConfrontation["Confrontations_NumeroCaseB"] . ');">';
					echo '<img class="confrontation--image" src="images/pronostiqueurs/' . $uneConfrontation["PronostiqueursB_Photo"] . '" alt="" />';
					
					if($uneConfrontation["Pronostiqueurs_PronostiqueurB"] != null)
						echo '<label class="confrontation--nom confrontation--nom-sans-score">' . $uneConfrontation["PronostiqueursB_NomUtilisateur"] . '</label>';
					else
						echo '<label class="confrontation--nom confrontation--nom-sans-score">' . $uneConfrontation["Confrontations_NumeroCaseB"] . '</label>';
						
				echo '</div>';
			echo '</div>';
		}
	}
?>

