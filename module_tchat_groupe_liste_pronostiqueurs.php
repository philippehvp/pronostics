<?php
	// Consultation des pronostiqueurs
	include('commun.php');
	
	// Lecture des pronostiqueurs éventuellement déjà sélectionnés
	$pronostiqueursSelectionnes = isset($_POST["pronostiqueursSelectionnes"]) ? (';' . $_POST["pronostiqueursSelectionnes"]) : '';
	
	// La liste des pronostiqueurs affichée permet :
	// - soit de sélectionner un à plusieurs pronostiqueurs (pour créer un tchat de groupe) (1)
	// - soit à n'en sélectionner qu'un seul (pour une conversation) (0)
	$typeTchat = isset($_POST["typeTchat"]) ? $_POST["typeTchat"] : 1;
	
	$ordreSQL =		'	SELECT		Pronostiqueur, Pronostiqueurs_NomUtilisateur, CASE WHEN TIMESTAMPDIFF(SECOND, PronostiqueursActivite_Date, NOW()) < 30 THEN 1 ELSE 0 END AS Pronostiqueurs_EstConnecte' .
					'	FROM		pronostiqueurs' .
					'	JOIN		pronostiqueurs_activite' .
					'				ON		pronostiqueurs.Pronostiqueur = pronostiqueurs_activite.Pronostiqueurs_Pronostiqueur' .
					'	WHERE		Pronostiqueur <> ' . $_SESSION["pronostiqueur"] .
					'	ORDER BY	Pronostiqueurs_EstConnecte DESC, Pronostiqueurs_NomUtilisateur';

	$req = $bdd->query($ordreSQL);
	$pronostiqueurs =$req->fetchAll();
	$nombrePronostiqueurs = sizeof($pronostiqueurs);
	$NOMBRE_COLONNES = 3;
	$nombrePronostiqueursParColonne = ceil($nombrePronostiqueurs / $NOMBRE_COLONNES);
	
	// Parcours des pronostiqueurs
	if($nombrePronostiqueurs) {
		for($i = 0; $i < $NOMBRE_COLONNES; $i++) {
			echo '<div class="gauche" style="margin-right: 1.5em;">';
				for($j = 0; $j < $nombrePronostiqueursParColonne && $i * $nombrePronostiqueursParColonne + $j < $nombrePronostiqueurs; $j++) {
					$indice = $i * $nombrePronostiqueursParColonne + $j;
					$pronostiqueur = $pronostiqueurs[$indice]["Pronostiqueur"];
					$pronostiqueursNomUtilisateur = $pronostiqueurs[$indice]["Pronostiqueurs_NomUtilisateur"];
					$pronostiqueursEstConnecte = $pronostiqueurs[$indice]["Pronostiqueurs_EstConnecte"];
					
					// Pour savoir si, en amont, des pronostiqueurs ont déjà été sélectionnés, il est nécessaire de regarder dans la chaîne passée en paramètre
					// si le nom du pronostiqueur y apparaît déjà ou non

					if(strpos($pronostiqueursSelectionnes, (';' . $pronostiqueursNomUtilisateur . ';')) !== false)
						$checked = ' checked';
					else
						$checked = '';
						
					// Selon le type de tchat, soit on affiche des cases à cocher, soit on affiche des boutons radio
					if($typeTchat == 1)					$type = 'checkbox';
					else								$type = 'radio';

					if($pronostiqueursEstConnecte)
						echo '<label class="texte-vert"><input class="texte-vert" type="' . $type . '" name="pronostiqueur" id="pronostiqueur_' . $pronostiqueur . '" value="' . $pronostiqueursNomUtilisateur . '"' . $checked . ' /> ' . $pronostiqueursNomUtilisateur . '</label>';
					else
						echo '<label class="texte-orange texte-italique"><input type="' . $type . '" name="pronostiqueur" id="pronostiqueur_' . $pronostiqueur . '" value="' . $pronostiqueursNomUtilisateur . '"' . $checked . ' /> ' . $pronostiqueursNomUtilisateur . '</label>';
					echo '<br />';
				}
			echo '</div>';
		}
	}
	



?>