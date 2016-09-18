<?php
	// Cette page affiche la liste des meilleurs buteurs sélectionnés par les pronostiqueurs

	// Lecture des vrais résultats
	$ordreSQL =		'	SELECT		IFNULL(GROUP_CONCAT(Joueurs_Nom SEPARATOR \' - \'), \'\') AS Joueurs_Nom' .
					'	FROM		cdm_meilleur_buteur' .
					'	JOIN		cdm_joueurs' .
					'				ON		Joueurs_Joueur = Joueur';

	$req = $bdd->query($ordreSQL);
	$resultats = $req->fetchAll();

	// Tous les pronostics de meilleur buteur
	$ordreSQL =		'	SELECT		CASE' .
					'					WHEN	Pronostiqueur = ' . $_SESSION["pronostiqueur"] .
					'					THEN	1' .
					'					ELSE	2' .
					'				END AS Ordre' .
					'				,Pronostiqueur' .
					'				,CASE' .
					'					WHEN	Pronostiqueur = ' . $_SESSION["pronostiqueur"] .
					'					THEN	\'Moi\'' .
					'					ELSE	Pronostiqueurs_Nom' .
					'				END AS Pronostiqueurs_Nom' .
					'				,IFNULL(Joueurs_Nom, \'-\') AS Joueurs_Nom' .
					'	FROM		cdm_pronostiqueurs' .
					'	JOIN		cdm_pronostics_buteur' .
					'				ON		Pronostiqueur = Pronostiqueurs_Pronostiqueur' .
					'	LEFT JOIN	cdm_joueurs' .
					'				ON		Joueurs_Joueur = Joueur' .
					'	WHERE		Pronostiqueurs_Pronostiqueur <> 1' .
					'	ORDER BY	Ordre, Pronostiqueurs_Nom';

	$req = $bdd->query($ordreSQL);
	$pronostics = $req->fetchAll();

	$nombrePronostiqueurs = sizeof($pronostics);
	
	// Points pour le meilleur buteur
	$ordreSQL	=	'	SELECT		CASE' .
					'					WHEN	Pronostiqueur = ' . $_SESSION["pronostiqueur"] .
					'					THEN	1' .
					'					ELSE	2' .
					'				END AS Ordre' .
					'				,IFNULL(Bonus_Buteur, 0) AS Bonus_Buteur' .
					'	FROM		cdm_pronostiqueurs' .
					'	LEFT JOIN	cdm_bonus' .
					'				ON		Pronostiqueur = Pronostiqueurs_Pronostiqueur' .
					'	WHERE		Pronostiqueurs_Pronostiqueur <> 1' .
					'	ORDER BY	Ordre, Pronostiqueurs_Nom';

	$req = $bdd->query($ordreSQL);
	$points = $req->fetchAll();

	echo '<table id="tblMeilleurButeur">';
		echo '<thead>';
			echo '<tr>';
				echo '<th>&nbsp;</th>';
				echo '<th class="bordure-droite">';
					echo $resultats[0]["Joueurs_Nom"];
				echo '</th>';
				
				echo '<th class="bordure-droite">Points buteur</th>';
			echo '</tr>';
		echo '</thead>';
		echo '<tbody>';
			for($i = 0; $i < $nombrePronostiqueurs; $i++) {
				echo '<tr>';
					echo '<td class="nomPronostiqueur bordure-droite">' . $pronostics[$i]["Pronostiqueurs_Nom"] . '</td>';
					echo '<td class="bordure-droite">';
						echo $pronostics[$i]["Joueurs_Nom"];
					echo '</td>';
					
					echo '<td class="bordure-droite">' . $points[$i]["Bonus_Buteur"] . '</td>';
				echo '</tr>';
			}
		echo '</tbody>';
	echo '</table>';
?>
