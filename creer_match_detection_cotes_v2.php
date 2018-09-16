<?php
	include('commun_administrateur.php');

	// Page de détection des cotes des joueurs v2

	// Equipe concernée
	$match = isset($_POST["match"]) ? $_POST["match"] : 0;
	$equipe = isset($_POST["equipe"]) ? $_POST["equipe"] : 0;

	// Le numéro de la colonne contenant la cote utile est contenu dans une table de configuration
	$ordreSQL =		'	SELECT		Configurations_ColonneCote' .
								'	FROM			configurations' .
								'	WHERE			Configuration = 1';
	$req = $bdd->query($ordreSQL);
	$colonneCote = $req->fetchAll();

	echo '<label>Colonne utile : </label>';
	echo '<select onchange="creerMatch_modifierColonneCote(this);" id="configuration_colonneCote">';
		for($i = 1; $i <= 5; $i++) {
			$selected = ($i == $colonneCote[0]["Configurations_ColonneCote"]) ? ' selected' : '';
			echo '<option value="' . $i . '"' . $selected . '>' . ($i == -1 ? 'Colonne' : $i) . '</option>';
		}
	echo '</select>';
	echo '<br />';
	echo '<textarea id="txtCotesJoueurs" rows="20" cols="50" placeholder="Coller ici le code HTML des cotes"></textarea>';
?>
