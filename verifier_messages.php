<?php
	// Vérification régulière de l'arrivée de nouveaux messages
	
	// Les données suivantes sont affichées :
	// - conversations de l'utilisateur (qu'il soit à l'origine ou non de la discussion)
	// - tchats de groupe créés par l'utilisateur
	// - tchats de groupe auxquels il participe
	
	// Rafraîchissement automatique du module
	include('commun.php');
	
	// Conversations non lues
	function lireConversationsNonConsultees($bdd) {
		$ordreSQL =		'	SELECT		TchatGroupe, Pronostiqueurs_NomUtilisateur, MessagesLus_NombreMessages' .
						'	FROM		pronostiqueurs' .
						'	JOIN		(' .
						'					SELECT		tchat_groupes.TchatGroupe, tchat_groupes_membres.Pronostiqueurs_Pronostiqueur, MessagesLus_NombreMessages' .
						'					FROM		tchat_groupes' .
						'					JOIN		tchat_groupes_membres' .
						'								ON		tchat_groupes.TchatGroupe = tchat_groupes_membres.TchatGroupes_TchatGroupe' .
						'					JOIN		messages_lus' .
						'								ON		tchat_groupes.TchatGroupe = messages_lus.TchatGroupes_TchatGroupe' .
						'					WHERE		TchatGroupes_TypeTchat = 0' .
						'								AND		messages_lus.Pronostiqueurs_Pronostiqueur = ' . $_SESSION["pronostiqueur"] .
						'								AND		messages_lus.MessagesLus_NombreMessages > 0' .
						'								AND		tchat_groupes_membres.Pronostiqueurs_Pronostiqueur <> ' . $_SESSION["pronostiqueur"] .
						'				) conversations' .
						'				ON		pronostiqueurs.Pronostiqueur = conversations.Pronostiqueurs_Pronostiqueur';
		$req = $bdd->query($ordreSQL);
		$conversations = $req->fetchAll();
		
		$nombreMessages = 0;
		if(sizeof($conversations)) {
			foreach($conversations as $uneConversation) {
				$nombreMessages += $uneConversation["MessagesLus_NombreMessages"];
			}
		}
		
		return $nombreMessages;
	}
	
	// Tchats de groupe créés par l'utilisateur
	function lireTchatGroupeProprietaire($bdd) {
		$ordreSQL =		'	SELECT		TchatGroupe, TchatGroupes_Nom, MessagesLus_NombreMessages' .
						'	FROM		tchat_groupes' .
						'	JOIN		messages_lus' .
						'				ON		tchat_groupes.TchatGroupe = messages_lus.TchatGroupes_TchatGroupe' .
						'						AND		tchat_groupes.Pronostiqueurs_Pronostiqueur = messages_lus.Pronostiqueurs_Pronostiqueur' .
						'	WHERE		tchat_groupes.Pronostiqueurs_Pronostiqueur = ' . $_SESSION["pronostiqueur"] .
						'				AND		tchat_groupes.TchatGroupes_TypeTchat = 1';

		$req = $bdd->query($ordreSQL);
		$tchatGroupes = $req->fetchAll();
		$nombreTchatGroupes = sizeof($tchatGroupes);
		
		$nombreMessages = 0;
		if($nombreTchatGroupes != 0) {
			foreach($tchatGroupes as $unTchatGroupe) {
				// Lecture des membres du groupe
				$ordreSQL =		'	SELECT		GROUP_CONCAT(Pronostiqueurs_NomUtilisateur SEPARATOR \', \') AS Pronostiqueurs_NomUtilisateur' .
								'	FROM		tchat_groupes_membres' .
								'	JOIN		pronostiqueurs' .
								'				ON		Pronostiqueurs_Pronostiqueur = Pronostiqueur' .
								'	WHERE		Pronostiqueurs_Pronostiqueur <> ' . $_SESSION["pronostiqueur"] .
								'				AND		TchatGroupes_TchatGroupe = ' . $unTchatGroupe["TchatGroupe"];
								
				$req = $bdd->query($ordreSQL);
				$membres = $req->fetchAll();
				$nombreMessagesNonLus = $unTchatGroupe["MessagesLus_NombreMessages"];
				$nombreMessages += $nombreMessagesNonLus;
			}
		}
		return $nombreMessages;
	}
	
	
	// Affichage des tchats de groupe auxquels participe le pronostiqueur
	// On exclut le tchat public
	// On en profite pour regarder dans la table messages_lus le nombre de messages qui auraient été postés depuis la dernière fois où il n'a pas ouvert ce tchat de groupe en particulier
	function lireTchatGroupeParticipant($bdd) {
		$ordreSQL =		'	SELECT		Pronostiqueurs_NomUtilisateur, TchatGroupe, TchatGroupes_Nom, MessagesLus_NombreMessages' .
						'	FROM		tchat_groupes_membres' .
						'	JOIN		tchat_groupes' .
						'				ON		tchat_groupes_membres.TchatGroupes_TchatGroupe = tchat_groupes.TchatGroupe' .
						'	JOIN		pronostiqueurs' .
						'				ON		tchat_groupes.Pronostiqueurs_Pronostiqueur = pronostiqueurs.Pronostiqueur' .
						'	JOIN		messages_lus' .
						'				ON		tchat_groupes_membres.Pronostiqueurs_Pronostiqueur = messages_lus.Pronostiqueurs_Pronostiqueur' .
						'						AND		tchat_groupes_membres.TchatGroupes_TchatGroupe = messages_lus.TchatGroupes_TchatGroupe' .
						'	WHERE		tchat_groupes_membres.Pronostiqueurs_Pronostiqueur = ' . $_SESSION["pronostiqueur"] .
						'				AND		tchat_groupes.Pronostiqueurs_Pronostiqueur <> ' . $_SESSION["pronostiqueur"] .
						'				AND		tchat_groupes_membres.TchatGroupes_TchatGroupe <> 1' .
						'				AND		tchat_groupes.TchatGroupes_TypeTchat = 1';

		$req = $bdd->query($ordreSQL);
		$discussions = $req->fetchAll();
		$nombreDiscussions = sizeof($discussions);
		
		$nombreMessages = 0;
		if($nombreDiscussions != 0) {
			foreach($discussions as $uneDiscussion) {
				// Lecture des membres du groupe
				$ordreSQL =		'	SELECT		GROUP_CONCAT(Pronostiqueurs_NomUtilisateur SEPARATOR \', \') AS Pronostiqueurs_NomUtilisateur' .
								'	FROM		tchat_groupes_membres' .
								'	JOIN		pronostiqueurs' .
								'				ON		Pronostiqueurs_Pronostiqueur = Pronostiqueur' .
								'	WHERE		Pronostiqueurs_Pronostiqueur <> ' . $_SESSION["pronostiqueur"] .
								'				AND		TchatGroupes_TchatGroupe = ' . $uneDiscussion["TchatGroupe"];
								
				$req = $bdd->query($ordreSQL);
				$membres = $req->fetchAll();
				$nombreMessagesNonLus = $uneDiscussion["MessagesLus_NombreMessages"];
				$nombreMessages += $nombreMessagesNonLus;
			}
		}
		
		return $nombreMessages;
	}
	
	$tableau = array();
	
	$tableau['nombreMessagesConversationsNonLues'] = lireConversationsNonConsultees($bdd);
	
	$nombreMessagesTchatGroupeNonLus = 0;
	$nombreMessagesTchatGroupeNonLus += lireTchatGroupeProprietaire($bdd);
	$nombreMessagesTchatGroupeNonLus += lireTchatGroupeParticipant($bdd);
	$tableau['nombreMessagesTchatGroupeNonLus'] = $nombreMessagesTchatGroupeNonLus;
	
	echo json_encode($tableau);
?>

