<?php
	include('commun.php');
	
	// Création d'un nouveau groupe de tchat ou d'une conversation avec un interlocuteur unique
	// La liste des pronostiqueurs contient en fait le champ nom utilisateur des pronostiqueurs séparés par le caractère ;
	$tableau = array();
	
	// Lecture des paramètres passés à la page
	$typeTchat = isset($_POST["typeTchat"]) ? $_POST["typeTchat"] : 1;
	$nomTchatGroupe = isset($_POST["nomTchatGroupe"]) ? $_POST["nomTchatGroupe"] : '';
	$listePronostiqueurs = isset($_POST["listePronostiqueurs"]) ? $_POST["listePronostiqueurs"] : '';
	

	// Création d'un groupe de tchat
	function creerTchatGroupe($bdd, $typeTchat, $nomTchatGroupe, $listePronostiqueurs) {
		$ordreSQL =		'	INSERT INTO		tchat_groupes(TchatGroupes_TypeTchat, TchatGroupes_Nom, Pronostiqueurs_Pronostiqueur)' .
						'	VALUES			(' . $typeTchat . ', ?, ' . $_SESSION["pronostiqueur"] . ')';
		$req = $bdd->prepare($ordreSQL);
		$req->execute(array($nomTchatGroupe));
		// Lecture de l'identifiant nouvellement créé
		//$tchatGroupe = $req->fetch(PDO::FETCH_ASSOC);
		$tchatGroupe = $bdd->lastInsertId();
		
		// Ajout des pronostiqueurs (et le pronostiqueur en cours également) dans la liste des membres de ce tchat de groupe ou de cette conversation
		// La liste des pronostiqueurs doit être scannée
		$unPronostiqueur = strtok($listePronostiqueurs, ';');
		while($unPronostiqueur !== false) {
			// Insertion en base dans la tables membres de ce tchat de groupe
			$ordreSQL =		'	INSERT INTO		tchat_groupes_membres(TchatGroupes_TchatGroupe, Pronostiqueurs_Pronostiqueur)' .
							'	SELECT			' . $tchatGroupe .
							'					,Pronostiqueur' .
							'	FROM			pronostiqueurs' .
							'	WHERE			Pronostiqueurs_NomUtilisateur = ?';

			$req = $bdd->prepare($ordreSQL);
			$req->execute(array($unPronostiqueur));
			
			// La table messages_lus permet de savoir pour chaque tchat de groupe le nombre de messages lus et non encore lus pour chaque membre
			$ordreSQL =		'	INSERT INTO		messages_lus(TchatGroupes_TchatGroupe, Pronostiqueurs_Pronostiqueur, MessagesLus_NombreMessages)' .
							'	SELECT			' . $tchatGroupe . ', Pronostiqueur, 0' .
							'	FROM			pronostiqueurs' .
							'	WHERE			Pronostiqueurs_NomUtilisateur = ?';

			$req = $bdd->prepare($ordreSQL);
			$req->execute(array($unPronostiqueur));
			
			$unPronostiqueur = strtok(';');
		}
		
		// Ajout du pronostiqueur ayant créé le tchat de groupe
		$ordreSQL =		'	INSERT INTO		tchat_groupes_membres(TchatGroupes_TchatGroupe, Pronostiqueurs_Pronostiqueur)' .
						'	SELECT			' . $tchatGroupe . ', ' . $_SESSION["pronostiqueur"];
		$req = $bdd->exec($ordreSQL);
		
		$ordreSQL =		'	INSERT INTO		messages_lus(TchatGroupes_TchatGroupe, Pronostiqueurs_Pronostiqueur, MessagesLus_NombreMessages)' .
						'	SELECT			' . $tchatGroupe . ', ' . $_SESSION["pronostiqueur"] . ', 0';

		$req = $bdd->exec($ordreSQL);
		
		// En retour, l'ajout d'un groupe de tchat retourne le numéro de tchat de groupe
		$tableau["tchatGroupe"] = $tchatGroupe;
		
		echo json_encode($tableau);
	}
	
	// Création d'une conversation
	// Si celle-ci existe déjà entre le pronostiqueur et l'interlocuteur, on n'en crée pas de nouvelle mais on la réouvre
	function creerConversation($bdd, $typeTchat, $nomTchatGroupe, $listePronostiqueurs, $tableau) {
		// Recherche d'une conversation entre ces deux personnes
		$ordreSQL =		'	SELECT		TchatGroupe' .
						'	FROM		tchat_groupes' .
						'	JOIN		tchat_groupes_membres' .
						'				ON		TchatGroupe = TchatGroupes_TchatGroupe' .
						'	JOIN		pronostiqueurs' .
						'				ON		(' .
						'							tchat_groupes.Pronostiqueurs_Pronostiqueur = ' . $_SESSION["pronostiqueur"] .
						'							AND		tchat_groupes_membres.Pronostiqueurs_Pronostiqueur = pronostiqueurs.Pronostiqueur' .
						'						)' .
						'						OR' .
						'						(' .
						'							tchat_groupes.Pronostiqueurs_Pronostiqueur = pronostiqueurs.Pronostiqueur' .
						'							AND		tchat_groupes_membres.Pronostiqueurs_Pronostiqueur = ' . $_SESSION["pronostiqueur"] .
						'						)' .
						'	WHERE		TchatGroupes_TypeTchat = 0' .
						'				AND		pronostiqueurs.Pronostiqueurs_NomUtilisateur = ?';
		$req = $bdd->prepare($ordreSQL);

		$req->execute(array($listePronostiqueurs));
		$conversation = $req->fetchAll();
		
		if($conversation == null) {
			// Création d'un nouveau tchat de groupe de type conversation
			creerTchatGroupe($bdd, $typeTchat, $nomTchatGroupe, $listePronostiqueurs, $tableau);
		}
		else {
			// Réouverture d'une ancienne conversation
			$tableau["tchatGroupe"] = $conversation[0]["TchatGroupe"];
			echo json_encode($tableau);
		}
		
		
	}
	
	// Création du groupe
	if($typeTchat == 1) {
		creerTchatGroupe($bdd, $typeTchat, $nomTchatGroupe, $listePronostiqueurs, $tableau);
	}
	else {
		creerConversation($bdd, $typeTchat, $nomTchatGroupe, $listePronostiqueurs, $tableau);
	}

	
?>