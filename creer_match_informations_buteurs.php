<?php
	include_once('commun_administrateur.php');

	// Demande d'informations complémentaires concernant un buteur (cote dans le cas où ce buteur n'a pas encore été ajouté)


	$demanderCote = isset($_POST["demander_cote"]) ? $_POST["demander_cote"] : 1;
	$joueur = isset($_POST["joueur"]) ? $_POST["joueur"] : 0;
	$match = isset($_POST["match"]) ? $_POST["match"] : 0;
	$equipe = isset($_POST["equipe"]) ? $_POST["equipe"] : 0;
	
	
	
	if($demanderCote) {

		// Lecture de la cote du buteur si elle existe dans la table
		$ordreSQL =		'	SELECT		JoueursCotes_Cote' .
						'	FROM		joueurs_cotes' .
						'	WHERE		Joueurs_Joueur = ' . $joueur .
						'				AND		Equipes_Equipe = ' . $equipe .
						'				AND		Matches_Match = ' . $match;

		$req = $bdd->query($ordreSQL);
		$cotes = $req->fetchAll();
		if(sizeof($cotes) == 1)
			$cote = $cotes[0]["JoueursCotes_Cote"];
		else
			$cote = '';
		
		echo '<label>Cote : </label>';
		echo '<input type="text" id="inputCote" value="' . $cote . '" />';
		echo '<br />';
	}
	echo '<input type="checkbox" id="inputCSC" name="inputCSC" value="1" /><label for="inputCSC">But contre son camp</label>';


?>