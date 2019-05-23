<?php
	include_once('commun.php');

	// Page de chargement d'une journ�e pour l'envoi de courrier
	
	// Journ�e concern�e
	$journee = isset($_POST["journee"]) ? $_POST["journee"] : 0;
	
	$ordreSQL =		'	SELECT		IFNULL(Courriers_Message, \'\') AS Courriers_Message' .
					'	FROM		cdm_courriers' .
					'	WHERE		Courriers_JourneeEnCours = ' . $journee;
	$req = $bdd->query($ordreSQL);
	$message = $req->fetch();
	$req->closeCursor();
	
	echo $message["Courriers_Message"];
?>
	