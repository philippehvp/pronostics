<?php

	ini_set('session.gc_maxlifetime', '86400');
	session_start();

	if(!isset($_SESSION["pronostiqueur"]))
		header('Location: index.php');

	$pronostiqueur = $_SESSION["pronostiqueur"];
	if($pronostiqueur == 0)
		header('Location: index.php');

	$nomPronostiqueur = isset($_SESSION["nom_pronostiqueur"]) ? $_SESSION["nom_pronostiqueur"] : 'Nom inconnu';
	$administrateur = isset($_SESSION["administrateur"]) ? $_SESSION["administrateur"] : 0;

	// Connexion à la base de données
	try {
		if(substr($_SERVER['HTTP_HOST'], 0, 9) == 'localhost') {
			$_SESSION["local"] = 1;
			$bdd = new PDO('mysql:host=db;port=3306;dbname=lepoulpeg', 'root', 'Allezlom2014', array(\PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));
		} else {
			$_SESSION["local"] = 0;
			$bdd = new PDO('mysql:host=lepoulpeg.mysql.db;dbname=lepoulpeg', 'lepoulpeg', 'Allezlom2014', array(\PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));
		}
		$bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}
	catch(Exception $e) {
		die('Erreur de base de données : ' . $e->getMessage());
	}
?>
