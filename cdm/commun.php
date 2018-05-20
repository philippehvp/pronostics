<?php
	if(session_status() == PHP_SESSION_NONE) {
		session_start();
	}
	
	if(!isset($_SESSION["cdm_pronostiqueur"]))
		header('Location: index.php');
		
	$pronostiqueur = $_SESSION["cdm_pronostiqueur"];
	if($pronostiqueur == 0)
		header('Location: index.php');
		
	$nomPronostiqueur = isset($_SESSION["cdm_nom_pronostiqueur"]) ? $_SESSION["cdm_nom_pronostiqueur"] : 'Nom inconnu';
	$administrateur = isset($_SESSION["cdm_administrateur"]) ? $_SESSION["cdm_administrateur"] : 0;
	
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
?>