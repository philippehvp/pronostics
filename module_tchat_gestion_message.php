<?php
	include_once('commun.php');

	// Envoi d'un message

	$tableau = array();

	// Extraction des variables postées
	extract($_POST);

	// Si un message vient d'être ajouté
	if($action == 'ajoutMessage') {

		// Vérification de la longueur du message
		// Cette vérification double celle qui est faite en amont dans le module mais qui a pu être modifiée par l'utilisateur
		if(strlen(trim($message)) == 0)
			return;
		$pronostiqueur = $_SESSION["pronostiqueur"];
		$ordreSQL = "INSERT INTO messages(Pronostiqueurs_Pronostiqueur, Messages_Date, Messages_Message, TchatGroupes_TchatGroupe) VALUES(?, NOW(), ?, ?)";
		$req = $bdd->prepare($ordreSQL);
		$req->execute(array($pronostiqueur, $message, $tchatGroupe));
		$tableau["etat"] = 'OK';

		// A chaque fois qu'une personne envoie un nouveau message, elle incrémente le nombre de messages non lus pour les personnes de ce tchat de groupe
		// On n'exclut toutefois le tchat public
		if($tchatGroupe != 1) {
			$ordreSQL =		'	UPDATE		messages_lus' .
							'	SET			MessagesLus_NombreMessages = MessagesLus_NombreMessages + 1' .
							'	WHERE		Pronostiqueurs_Pronostiqueur <> ' . $_SESSION["pronostiqueur"] .
							'				AND		TchatGroupes_TchatGroupe = ' . $tchatGroupe;
			$req = $bdd->exec($ordreSQL);
		}

	}
	else if($action == 'lectureMessage') {
		$dernierMessage = floor($dernierMessage);
		$ordreSQL =		'	SELECT		tchat_groupes.Pronostiqueurs_Pronostiqueur, Pronostiqueurs_NomUtilisateur, Message, Messages_Message, Pronostiqueurs_CodeCouleur' .
						'	FROM		messages' .
						'	JOIN		pronostiqueurs' .
						'				ON		messages.Pronostiqueurs_Pronostiqueur = pronostiqueurs.Pronostiqueur' .
						'	JOIN		tchat_groupes' .
						'				ON		messages.TchatGroupes_TchatGroupe = tchat_groupes.TchatGroupe' .
						'	WHERE		Message > ' . $dernierMessage .
						'				AND		messages.TchatGroupes_TchatGroupe = ' . $tchatGroupe .
						'	ORDER BY	Messages_Date ASC';

		$req = $bdd->query($ordreSQL);
		$messages = $req->fetchAll();
		$tableau["lecture"] = '';
		$tableau["dernierMessage"] = $dernierMessage;
		foreach($messages as $unMessage) {
			// Lecture de la couleur
			$codeCouleur = isset($unMessage["Pronostiqueurs_CodeCouleur"]) ? $unMessage["Pronostiqueurs_CodeCouleur"] : '#ffffff';
			$messageAAfficher = $unMessage["Messages_Message"];
			$nomAAfficher = $unMessage["Pronostiqueurs_Pronostiqueur"] == $_SESSION["pronostiqueur"] ? 'Moi' : $unMessage["Pronostiqueurs_NomUtilisateur"];
			$tableau["lecture"] = '(' . date('H:i:s', $unMessage["Messages_Date"]) . ') <p style="color: ' . $codeCouleur . ';"><strong>' . $nomAAfficher . '</strong> :&nbsp;' . $messageAAfficher . '</p>';

			$tableau["dernierMessage"] = $unMessage["Message"];
		}

		// On indique que l'on a lu tous les messages de ce tchat de groupe
		// On exclut le tchat public
		if($tchatGroupe != 1) {
			$ordreSQL =		'	UPDATE		messages_lus' .
							'	SET			MessagesLus_NombreMessages = 0' .
							'	WHERE		Pronostiqueurs_Pronostiqueur = ' . $_SESSION["pronostiqueur"] .
							'				AND		TchatGroupes_TchatGroupe = ' . $tchatGroupe;
			$req = $bdd->exec($ordreSQL);
		}


		$tableau["etat"] = 'OK';
	}


	echo json_encode($tableau);

?>