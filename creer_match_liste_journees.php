<?php
	include_once('commun_administrateur.php');

	// Lecture des journées du championnat dont le numéro a été passé en paramètre

	$championnat = isset($_POST["championnat"]) ? $_POST["championnat"] : 0;

	// Lecture des journées
	$ordreSQL =		'SELECT		Journee, Journees_Nom ';
	$ordreSQL .=	'FROM		journees ';
	$ordreSQL .=	'WHERE		Championnats_Championnat = ' . $championnat . ' ';
	$ordreSQL .=	'ORDER BY	Journee';

	$req = $bdd->query($ordreSQL);

	echo '<label>Journée :</label>';
	echo '<select id="selectJournee" onchange="creerMatch_changerJournee();">';
		echo '<option value="0">Journées</option>';
		while($donnees = $req->fetch()) {
			echo '<option value="' . $donnees["Journee"] . '">' . $donnees["Journees_Nom"] . '</option>';
		}
	echo '</select>';

	$req->closeCursor();
?>
