<?php
	session_start();
	
	// Connexion à la base de données
	try {
		if($_SERVER['HTTP_HOST'] == 'localhost') {
			$bdd = new PDO('mysql:host=localhost;dbname=lepoulpeg', 'root', '', array(\PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));
		} else {
			$bdd = new PDO('mysql:host=mysql51-119.perso;dbname=lepoulpeg', 'lepoulpeg', 'Allezlom2014', array(\PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));
		}
	}
	catch(Exception $e) {
		die('Erreur de base de données : ' . $e->getMessage());
	}
	
	// Vérification dans la table des utilisateurs (données envoyées par le formulaire)
	$login = isset($_POST["login"]) ? $_POST["login"] : '';
	$mdp = isset($_POST["mdp"]) ? $_POST["mdp"] : '';
	
	// Sauvegarde du login et du mot de passe dans des cookies
	setcookie('cdm_login', $login, time() + (60*60*24*30), null, null, false, true);
	setcookie('cdm_mdp', $mdp, time() + (60*60*24*30), null, null, false, true);

	$ordreSQL =		'	SELECT		Pronostiqueur, Pronostiqueurs_Nom, Pronostiqueurs_Administrateur, IFNULL(Pronostiqueurs_PremiereConnexion, 1) AS Pronostiqueurs_PremiereConnexion' .
					' 	FROM		cdm_pronostiqueurs' .
					' 	WHERE 		Pronostiqueurs_Nom = ? AND Pronostiqueurs_MotDePasse = ?' .
					' 	LIMIT		1';
	$req = $bdd->prepare($ordreSQL);
	$req->execute(array($login, $mdp));
	
	$_SESSION["cdm_pronostiqueur"] = 0;
	$_SESSION["cdm_administrateur"] = 0;
	
	$donnees = $req->fetch();
	$_SESSION["cdm_pronostiqueur"] = $donnees["Pronostiqueur"];
	$_SESSION["cdm_nom_pronostiqueur"] = $donnees["Pronostiqueurs_Nom"];
	$_SESSION["cdm_administrateur"] = $donnees["Pronostiqueurs_Administrateur"];
	$premiereConnexion = $donnees["Pronostiqueurs_PremiereConnexion"];
	
	$req->closeCursor();
	
	if($_SESSION["cdm_pronostiqueur"] != 0) {
		$_SESSION["cdm_erreur_login"] = 0;

		// S'il s'agit de la première connexion de l'utilisateur, on le dirige vers la page de modification de mot de passe
		if($premiereConnexion == 1)
			header('Location: premiere_connexion.php');
		else
			header('Location: accueil.php');
	}

	else {
		$_SESSION["cdm_pronostiqueur"] = 0;
		$_SESSION["cdm_nom_pronostiqueur"] = '';
		$_SESSION["cdm_administrateur"] = 0;
		$_SESSION["cdm_erreur_login"] = 1;
		header('Location: index.php');
	}
?>
