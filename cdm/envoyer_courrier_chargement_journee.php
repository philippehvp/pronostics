<?php
	include('commun.php');

	// Page de chargement d'une journée pour l'envoi de courrier
	
	// Journée concernée
	$journee = isset($_POST["journee"]) ? $_POST["journee"] : 0;
	
	$ordreSQL =		'	SELECT		IFNULL(Courriers_Message, \'\') AS Courriers_Message' .
					'	FROM		cdm_courriers' .
					'	WHERE		Courriers_JourneeEnCours = ' . $journee;
	$req = $bdd->query($ordreSQL);
	$message = $req->fetch();
	$req->closeCursor();
	
	echo $message["Courriers_Message"];
?>
	