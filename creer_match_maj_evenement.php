<?php
	include('commun_administrateur.php');

	// Sauvegarde d'un événement sur un match

	// Lecture des paramètres passés à la page
	$match = isset($_POST["match"]) ? $_POST["match"] : 0;
	$evenement = isset($_POST["evenement"]) ? $_POST["evenement"] : 0;
	
	date_default_timezone_set('Europe/Paris');
	$date = date('Y-m-d H:i:s');
	
	$ordreSQL =		'	UPDATE		journees' .
					'	JOIN		matches' .
					'				ON		journees.Journee = matches.Journees_Journee' .
					'	SET			journees.Journees_DateEvenement = \'' . $date . '\'' .
					'				,journees.Journees_CodeEvenement = ' . $evenement .
					'	WHERE		matches.Match = ' . $match;
	$req = $bdd->exec($ordreSQL);
	
	// Il est à présent nécessaire de mettre à jour le critère de rafraîchissement de la table des modules
	// Ce critère est lu par le module lors de son tout premier affichage (et sauvegardé dans un champ caché)
	// Ensuite, à la première demande de rafraîchissement, le module sera capable de comparer cette valeur et le critère de comparaison
	// pour savoir s'il faut se rafraîchir ou non
	// La table modules_resultats contient la liste des modules concernés par une mise à jour relative aux matches et aux journées
	// On sait par avance qu'il s'agit des modules 12 à 15 (actuellement, il n'y a pas de moyen plus intelligent de faire le lien entre les tables)

	$ordreSQL =		'	UPDATE		modules' .
					'	JOIN		championnats' .
					'				ON		Modules_Parametre = championnats.Championnat' .
					'	JOIN		journees' .
					'				ON		championnats.Championnat = journees.Championnats_Championnat' .
					'	JOIN		matches' .
					'				ON		journees.Journee = matches.Journees_Journee' .
					'	JOIN		modules_resultats_evenements' .
					'				ON		modules.Module = modules_resultats_evenements.Modules_Module' .
					'	SET			Modules_CritereRafraichissement = \'' . $date . '\'' .
					'	WHERE		matches.Match = ' . $match;

	$req = $bdd->exec($ordreSQL);
	
?>
	