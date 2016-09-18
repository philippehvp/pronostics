<?php
	session_start();
	
	// Connexion à la base de données
	try {
		if($_SERVER['HTTP_HOST'] == 'localhost')
			$bdd = new PDO('mysql:host=localhost;dbname=lepoulpeg', 'root', 'root', array(\PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));
		else
			$bdd = new PDO('mysql:host=mysql51-119.perso;dbname=lepoulpeg', 'lepoulpeg', 'Allezlom2014', array(\PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));
	}
	catch(Exception $e) {
		die('Erreur : ' . $e->getMessage());
	}
	
	// Vérification dans la table des utilisateurs (données envoyées par le formulaire)
	$login = isset($_POST["login"]) ? $_POST["login"] : '';
	$mdp = isset($_POST["mdp"]) ? $_POST["mdp"] : '';
	
	// Sauvegarde du login et du mot de passe dans des cookies
	setcookie('lepoulpeg_login', $login, time() + (7 * 24 * 3600), null, null, false, true);
	setcookie('lepoulpeg_mdp', $mdp, time() + (7 * 24 * 3600), null, null, false, true);
	
	$ordreSQL =		'	SELECT		Pronostiqueur, Pronostiqueurs_NomUtilisateur, Pronostiqueurs_Prenom, Pronostiqueurs_Administrateur' .
					'				,IFNULL(Pronostiqueurs_Photo, \'_inconnu.png\') AS Pronostiqueurs_Photo' .
					'				,IFNULL(Pronostiqueurs_PremiereConnexion, 1) AS Pronostiqueurs_PremiereConnexion' .
					'				,Pronostiqueurs_AfficherTropheesChampionnat' .
					'				,IFNULL(themes.Themes_NomCourt, \'defaut\') AS Pronostiqueurs_Theme' .
					'	FROM		pronostiqueurs' .
					'	LEFT JOIN	themes' .
					'				ON		pronostiqueurs.Themes_Theme = themes.Theme' .
					'	WHERE		Pronostiqueurs_NomUtilisateur = ?' .
					'				AND		Pronostiqueurs_MotDePasse = ?' .
					'	LIMIT		1';
	
	$req = $bdd->prepare($ordreSQL);
	$req->execute(array($login, $mdp));
	
	$donnees = $req->fetch();
	$_SESSION["pronostiqueur"] = $donnees["Pronostiqueur"];
	$_SESSION["nom_utilisateur"] = $donnees["Pronostiqueurs_NomUtilisateur"];
	$_SESSION["prenom"] = $donnees["Pronostiqueurs_Prenom"];
	$_SESSION["administrateur"] = $donnees["Pronostiqueurs_Administrateur"];
	$_SESSION["photo_pronostiqueur"] = $donnees["Pronostiqueurs_Photo"];
	$_SESSION["theme"] = $donnees["Pronostiqueurs_Theme"];
	$premiereConnexion = $donnees["Pronostiqueurs_PremiereConnexion"];
	$afficherTropheesChampionnat = $donnees["Pronostiqueurs_AfficherTropheesChampionnat"];
	
	$req->closeCursor();
	
	if($_SESSION["pronostiqueur"] != 0) {
		$_SESSION["erreurLogin"] = 0;
		
		// S'il s'agit de la première connexion de l'utilisateur, on le dirige vers la page de modification de mot de passe
		if($premiereConnexion == 1)
			header('Location: premiere_connexion.php');
		else {
			if($afficherTropheesChampionnat != null && $afficherTropheesChampionnat != 0) {
				$pageAAfficher = 'Location: consulter_trophees.php?championnat=' . $afficherTropheesChampionnat . '&affichertrophees=1';
				header($pageAAfficher);
			}
			else
				header('Location: accueil.php');
		}
	}

	else {
		$_SESSION["pronostiqueur"] = 0;
		$_SESSION["nom_utilisateur"] = '';
		$_SESSION["prenom"] = '';
		$_SESSION["administrateur"] = 0;
		$_SESSION["photo_pronostiqueur"] = '';
		$_SESSION["erreur_login"] = 1;
		header('Location: index.php');
	}
?>
