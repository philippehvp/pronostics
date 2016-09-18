<?php
	include('commun.php');
	
	$equipe = isset($_POST["equipe"]) ? $_POST["equipe"] : 0;
	
	$ordreSQL =		'	SELECT		GROUP_CONCAT(Pronostiqueurs_Nom SEPARATOR \', \') AS Pronostiqueurs_Nom' .
					'	FROM		cdm_pronostics_sequencement' .
					'	JOIN		cdm_pronostiqueurs' .
					'				ON		Pronostiqueurs_Pronostiqueur = Pronostiqueur' .
					'	WHERE		cdm_pronostics_sequencement.Pronostiqueurs_Pronostiqueur <> 1' .
					'				AND		Matches_Match = 16' .
					'				AND		(' .
					'							Equipes_EquipeA = ' . $equipe .
					'							OR		Equipes_EquipeB = ' . $equipe .
					'						)';

	$req = $bdd->query($ordreSQL);
	$pronostiqueurs = $req->fetchAll();
	
	foreach($pronostiqueurs as $pronostiqueur) {
		echo '<label>' . $pronostiqueur["Pronostiqueurs_Nom"] . '</label><br />';
	}
?>