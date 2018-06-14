<?php
	include('commun.php');
	
	$NOMBRE_COLONNES = 3;
	
	// Cette page affiche la liste des pronostiqueurs, hormis le pronostiqueur en cours
	// Un traitement spécial est fait pour le pronostiqueur 1, qui représente les résultats finaux
	$ordreSQL =		'	SELECT		Pronostiqueur, Pronostiqueurs_Nom' .
					'	FROM		cdm_pronostiqueurs' .
					'	WHERE		Pronostiqueur <> ' . $_SESSION["cdm_pronostiqueur"] .
					'	ORDER BY	Pronostiqueurs_Nom';
	$req = $bdd->query($ordreSQL);
	$pronostiqueursConsultables = $req->fetchAll();
	$nombrePronostiqueursConsultables = sizeof($pronostiqueursConsultables);
	
	if($nombrePronostiqueursConsultables) {
		// Les pronostiqueurs sont affichés sur un certain nombre de colonnes
		$nombrePronostiqueursParColonne = $nombrePronostiqueursConsultables / $NOMBRE_COLONNES;
		
		echo '<div id="divPronostiqueursConsultables" class="listePronostiqueurs">';
			for($i = 0; $i < $NOMBRE_COLONNES; $i++) {
				echo '<div class="gauche">';
					for($j = 0; $j < $nombrePronostiqueursParColonne && $i * $NOMBRE_COLONNES + $j < $nombrePronostiqueursConsultables; $j++) {
						if($pronostiqueursConsultables[$i * $nombrePronostiqueursParColonne + $j]["Pronostiqueur"] == 1)
							$nomPronostiqueur = 'RESULTATS REELS';
						else
							$nomPronostiqueur = $pronostiqueursConsultables[$i * $nombrePronostiqueursParColonne + $j]["Pronostiqueurs_Nom"];
						
						echo '<label class="lien" onclick="window.open(\'recapituler_prono.php?pronostiqueurConsulte=' . $pronostiqueursConsultables[$i * $nombrePronostiqueursParColonne + $j]["Pronostiqueur"] . '\', \'_self\');">' . $nomPronostiqueur . '</label>';
						echo '<br />';
					}
				echo '</div>';
			}
		echo '</div>';
	}
	
?>