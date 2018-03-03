<?php

	ini_set('session.gc_maxlifetime', '86400');
	session_start();

	if(!isset($_SESSION["pronostiqueur"]))
		header('Location: index.php');

	$pronostiqueur = $_SESSION["pronostiqueur"];
	if($pronostiqueur == 0)
		header('Location: index.php');

	$nomPronostiqueur = isset($_SESSION["nomPronostiqueur"]) ? $_SESSION["nomPronostiqueur"] : 'Nom inconnu';
	$administrateur = isset($_SESSION["administrateur"]) ? $_SESSION["administrateur"] : 0;

	// Connexion à la base de données
	try {
		if($_SERVER['HTTP_HOST'] == 'localhost') {
			$_SESSION["local"] = 0;
			$bdd = new PDO('mysql:host=localhost;dbname=lepoulpeg', 'root', '', array(\PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));
		} else {
			$_SESSION["local"] = 0;
			$bdd = new PDO('mysql:host=mysql51-119.perso;dbname=lepoulpeg', 'lepoulpeg', 'Allezlom2014', array(\PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));
		}
	}
	catch(Exception $e) {
		die('Erreur de base de données : ' . $e->getMessage());
	}

	function enregistrerConsultationPage($bdd, $nomPage) {
		// Ajout de la page pour le pronostiqueur dans la table des pages consultées
		$ordreSQL =		'	INSERT INTO		pages_consultees(Pages_Page, Pronostiqueurs_Pronostiqueur, PagesConsultees_Date)' .
									'	SELECT			Page, ' . $_SESSION["pronostiqueur"] . ', NOW()' .
									'	FROM			pages' .
									'	WHERE			Pages_Nom = \'' . $nomPage . '\'';

		$bdd->exec($ordreSQL);
	}
?>
