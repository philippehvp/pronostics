<?php
	session_start();
	
	// Connexion à la base de données
	try {
		if($_SERVER['HTTP_HOST'] == 'localhost') {
			$_SESSION["local"] = 1;
			$bdd = new PDO('mysql:host=localhost;dbname=lepoulpeg', 'root', '', array(\PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));
		} else {
			$_SESSION["local"] = 0;
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
	
	$req = $bdd->prepare('SELECT Pronostiqueur, Pronostiqueurs_Nom, Pronostiqueurs_Administrateur, IFNULL(Pronostiqueurs_Photo, \'_inconnu.png\') AS Pronostiqueurs_Photo FROM cdm_pronostiqueurs WHERE Pronostiqueurs_Nom = ? AND Pronostiqueurs_MotDePasse = ? LIMIT 1');
	$req->execute(array($login, $mdp));
	
	$_SESSION["cdm_pronostiqueur"] = 0;
	$_SESSION["administrateur"] = 0;
	
	$donnees = $req->fetch();
	$_SESSION["cdm_pronostiqueur"] = $donnees["Pronostiqueur"];
	$_SESSION["nomPronostiqueur"] = $donnees["Pronostiqueurs_Nom"];
	$_SESSION["administrateur"] = $donnees["Pronostiqueurs_Administrateur"];
	$_SESSION["photo_pronostiqueur"] = $donnees["Pronostiqueurs_Photo"];
	
	$req->closeCursor();
	
	if($_SESSION["cdm_pronostiqueur"] <> 0) {
		$_SESSION["erreurLogin"] = 0;
		
		header('Location: accueil.php');
	}

	else {
		$_SESSION["cdm_pronostiqueur"] = 0;
		$_SESSION["administrateur"] = 0;
		$_SESSION["erreurLogin"] = 1;
		header('Location: index.php');
	}
?>
