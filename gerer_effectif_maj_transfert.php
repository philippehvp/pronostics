<?php
	include('commun_administrateur.php');

	// Déplacement d'un joueur
	
	// Lecture des paramètres passés à la page
	$action = isset($_POST["action"]) ? $_POST["action"] : -1;
	$equipe = isset($_POST["equipe"]) ? $_POST["equipe"] : 0;
	$dateTransfert = isset($_POST["dateTransfert"]) ? $_POST["dateTransfert"] : 0;

	
	// Dans le cas d'un transfert
	if($action == 0) {
		// Dans un premier temps, on clôture la présence du joueur dans son ancienne équipe
		// Pour cela, il est nécessaire de calculer quelle est la veille de la date du transfert
		$joueur = isset($_POST["joueur"]) ? $_POST["joueur"] : 0;

		$ordreSQL =	'	UPDATE		joueurs_equipes' .
					'	SET			JoueursEquipes_Fin = DATE_ADD(STR_TO_DATE(\'' . $dateTransfert . '\', \'%d/%m/%Y\'), INTERVAL -1 SECOND)' .
					'	WHERE		joueurs_equipes.Joueurs_Joueur = ' . $joueur .
					'				AND		joueurs_equipes.JoueursEquipes_Fin IS NULL';

		$bdd->exec($ordreSQL);
					
		
	}
	else if($action == 1) {
		$nomJoueur = isset($_POST["nomJoueur"]) ? str_replace('\'', '\\\'', $_POST["nomJoueur"]) : '';
		$prenomJoueur = isset($_POST["prenomJoueur"]) ? str_replace('\'', '\\\'', $_POST["prenomJoueur"]) : '';
		// Dans le cas de la création d'un joueur
		// On crée un nouveau joueur dans la table joueurs
		$ordreSQL =	'	INSERT INTO		joueurs(Joueurs_NomFamille, Joueurs_Prenom) VALUES (\'' . $nomJoueur . '\', \'' . $prenomJoueur . '\')';
		$bdd->exec($ordreSQL);
		
		// On lit le numéro du joueur nouvellement créé
		$ordreSQL =	'	SELECT		MAX(Joueur) AS Joueur FROM joueurs';
		$req = $bdd->query($ordreSQL);
		$donnees = $req->fetch();
		$joueur = $donnees["Joueur"];
	}

	// On crée une nouvelle ligne dans la table d'association des joueurs et des équipes
	$ordreSQL =	'	INSERT INTO	joueurs_equipes(Joueurs_Joueur, Equipes_Equipe, JoueursEquipes_Debut)' .
				'	VALUES(' . $joueur . ', ' . $equipe . ', STR_TO_DATE(\'' . $dateTransfert . '\', \'%d/%m/%Y\'))';

	$bdd->exec($ordreSQL);

?>