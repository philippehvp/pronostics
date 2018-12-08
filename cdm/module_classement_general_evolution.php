<?php
	include_once('commun.php');
	// Page d'affichage de l'évolution du classement d'un pronostiqueur

	$pronostiqueur = isset($_POST["pronostiqueur"]) ? $_POST["pronostiqueur"] : 0;
	
	$ordreSQL =		'	SELECT		Classements_JourneeEnCours, Classements_Classement, Classements_Points' .
					'	FROM		cdm_classements' .
					'	WHERE		Pronostiqueurs_Pronostiqueur = ' . $pronostiqueur .
					'	ORDER BY	Classements_JourneeEnCours';

	$req = $bdd->query($ordreSQL);
	if($req == null)
		return;
	$statsEvolution = $req->fetchAll();
	
	if(sizeof($statsEvolution)) {
		echo '<table id="tblStatistiquesEvolution">';
			echo '<thead>';
				echo '<tr>';
					echo '<th class="journee">Journées</th>';
					echo '<th class="classement">Classement</th>';
					echo '<th class="point">Points</th>';
				echo '</tr>';
			echo '</thead>';
			
			echo '<tbody>';
				foreach($statsEvolution as $uneStat) {
					echo '<tr>';
						echo '<td class="bordure-droite">' . $uneStat["Classements_JourneeEnCours"] . '</td>';
						echo '<td class="bordure-droite">' . $uneStat["Classements_Classement"] . '</td>';
						echo '<td>' . $uneStat["Classements_Points"] . '</td>';
					echo '</tr>';
				}
			echo '</tbody>';
		echo '</table>';
	}
					
?>