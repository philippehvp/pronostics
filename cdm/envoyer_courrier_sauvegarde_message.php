<?php
	include_once('commun.php');

	// Page de sauvegarde du courrier d'une journée
	
	// Journée concernée
	$journee = isset($_POST["journee"]) ? $_POST["journee"] : 0;
	$message = isset($_POST["message"]) ? $_POST["message"] : 'NULL';
	
	$ordreSQL =		'	UPDATE		cdm_courriers' .
					'	SET			Courriers_Message = ?' .
					'	WHERE		Courriers_JourneeEnCours = ' . $journee;
	$req = $bdd->prepare($ordreSQL);
	$req->execute(array($message));
	
?>
	