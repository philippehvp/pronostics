<?php
	// Module de tchat
	// Vérification de la nécessité de faire le rafraîchissement ou non
	
	// La page est forcément appelée par de l'Ajax
	include('commun.php');

	// Lecture des paramètres passés à la page
	$tchatGroupe = isset($_POST["parametre"]) ? $_POST["parametre"] : 0;
	$critereRafraichissement = isset($_POST["critereRafraichissement"]) ? $_POST["critereRafraichissement"] : '';	

	// Lecture de l'identifiant du dernier message
	$ordreSQL =	'	SELECT		MAX(Message) AS Message' .
				'	FROM		messages' .
				'	WHERE		TchatGroupes_TchatGroupe = ' . $tchatGroupe;
	$req = $bdd->query($ordreSQL);
	$message = $req->fetchAll();
	$dernierMessage = isset($message[0]["Message"]) ? $message[0]["Message"] : '';

	$tableau = array();
	// Le crtitère de rafraîchissement indique la même date que celle de la mise à jour des données
	// Il n'est donc pas nécessaire de continuer le traitement
	if($critereRafraichissement == $dernierMessage)
		$tableau['rafraichir'] = '0';
	else {
		$tableau['rafraichir'] = '1';
		$tableau['critereRafraichissement'] = $dernierMessage;
	}

	echo json_encode($tableau);	
?>