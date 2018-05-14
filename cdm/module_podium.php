<?php
	// Cette page affiche la liste des podiums

	// Lecture des vrais résultats
	$ordreSQL =		'	SELECT		(SELECT	Equipes_Nom FROM cdm_equipes WHERE Equipe = cdm_fn_vainqueur(1, 15)) AS Equipes_NomPremier' .
					'				,(SELECT	Equipes_Nom FROM cdm_equipes WHERE Equipe = cdm_fn_perdant(1, 15)) AS Equipes_NomDeuxieme' .
					'				,(SELECT	Equipes_Nom FROM cdm_equipes WHERE Equipe = cdm_fn_vainqueur(1, 16)) AS Equipes_NomTroisieme';

	$req = $bdd->query($ordreSQL);
	$resultats = $req->fetchAll();
		
	// Tous les pronostics de podium
	$ordreSQL =		'	SELECT		CASE' .
					'					WHEN	Pronostiqueur = ' . $_SESSION["cdm_pronostiqueur"] .
					'					THEN	1' .
					'					ELSE	2' .
					'				END AS Ordre' .
					'				,Pronostiqueur' .
					'				,CASE' .
					'					WHEN	Pronostiqueur = ' . $_SESSION["cdm_pronostiqueur"] .
					'					THEN	\'Moi\'' .
					'					ELSE	Pronostiqueurs_Nom' .
					'				END AS Pronostiqueurs_Nom' .
					'				,(SELECT	Equipes_Nom FROM cdm_equipes WHERE Equipe = cdm_fn_vainqueur(cdm_pronostiqueurs.Pronostiqueur, 15)) AS Equipes_NomPremier' .
					'				,(SELECT	Equipes_Nom FROM cdm_equipes WHERE Equipe = cdm_fn_perdant(cdm_pronostiqueurs.Pronostiqueur, 15)) AS Equipes_NomDeuxieme' .
					'				,(SELECT	Equipes_Nom FROM cdm_equipes WHERE Equipe = cdm_fn_vainqueur(cdm_pronostiqueurs.Pronostiqueur, 16)) AS Equipes_NomTroisieme' .
					'	FROM		cdm_pronostiqueurs' .
					'	WHERE		Pronostiqueur <> 1' .
					'	ORDER BY	Ordre, Pronostiqueurs_Nom';
	$req = $bdd->query($ordreSQL);
	$pronostics = $req->fetchAll();

	$nombrePronostiqueurs = sizeof($pronostics);
	
	// Points pour le podium
	$ordreSQL	=	'	SELECT		CASE' .
					'					WHEN	Pronostiqueur = ' . $_SESSION["cdm_pronostiqueur"] .
					'					THEN	1' .
					'					ELSE	2' .
					'				END AS Ordre' .
					'				,IFNULL(Bonus_Vainqueur, 0) + IFNULL(Bonus_Deuxieme, 0) + IFNULL(Bonus_Troisieme, 0) AS Bonus_Podium' .
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
					echo $resultats[0]["Equipes_NomPremier"];
				echo '</th>';
				echo '<th class="bordure-droite">';
					echo $resultats[0]["Equipes_NomDeuxieme"];
				echo '</th>';
				echo '<th class="bordure-droite">';
					echo $resultats[0]["Equipes_NomTroisieme"];
				echo '</th>';
				
				echo '<th>Points classements</th>';
			echo '</tr>';
		echo '</thead>';
		echo '<tbody>';
			for($i = 0; $i < $nombrePronostiqueurs; $i++) {
				echo '<tr>';
					echo '<td class="nomPronostiqueur bordure-droite">' . $pronostics[$i]["Pronostiqueurs_Nom"] . '</td>';
					echo '<td class="bordure-droite">';
						echo $pronostics[$i]["Equipes_NomPremier"];
					echo '</td>';
					echo '<td class="bordure-droite">';
						echo $pronostics[$i]["Equipes_NomDeuxieme"];
					echo '</td>';
					echo '<td class="bordure-droite">';
						echo $pronostics[$i]["Equipes_NomTroisieme"];
					echo '</td>';
					
					echo '<td class="bordure-droite">' . $points[$i]["Bonus_Podium"] . '</td>';
				echo '</tr>';
			}
		echo '</tbody>';
	echo '</table>';

?>
