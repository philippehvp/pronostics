<?php
	include_once('commun.php');

	// Sauvegarde des pronostics de bonus

	// Lecture des paramètres passés à la page
	$equipeChampionne = isset($_POST["equipeChampionne"]) ? $_POST["equipeChampionne"] : 0;
	$equipeLDC1 = isset($_POST["equipeLDC1"]) ? $_POST["equipeLDC1"] : 0;
	$equipeLDC2 = isset($_POST["equipeLDC2"]) ? $_POST["equipeLDC2"] : 0;
	$equipeLDC3 = isset($_POST["equipeLDC3"]) ? $_POST["equipeLDC3"] : 0;
	$equipeLDC4 = isset($_POST["equipeLDC4"]) ? $_POST["equipeLDC4"] : 0;
	$equipeReleguee1 = isset($_POST["equipeReleguee1"]) ? $_POST["equipeReleguee1"] : 0;
	$equipeReleguee2 = isset($_POST["equipeReleguee2"]) ? $_POST["equipeReleguee2"] : 0;
	$equipeReleguee3 = isset($_POST["equipeReleguee3"]) ? $_POST["equipeReleguee3"] : 0;
	$meilleurButeur = isset($_POST["meilleurButeur"]) ? $_POST["meilleurButeur"] : 0;
	$meilleurPasseur = isset($_POST["meilleurPasseur"]) ? $_POST["meilleurPasseur"] : 0;

	// Mise à jour des données dans la table
	$ordreSQL =		'	REPLACE INTO	pronostics_bonus	(	Pronostiqueurs_Pronostiqueur' .
					'											,PronosticsBonus_EquipeChampionne' .
					'											,PronosticsBonus_EquipeLDC1, PronosticsBonus_EquipeLDC2, PronosticsBonus_EquipeLDC3, PronosticsBonus_EquipeLDC4' .
					'											,PronosticsBonus_EquipeReleguee1, PronosticsBonus_EquipeReleguee2, PronosticsBonus_EquipeReleguee3' .
					'											,PronosticsBonus_JoueurMeilleurButeur, PronosticsBonus_JoueurMeilleurPasseur' .
					'										)' .
					'	SELECT			pronostics.*' .
					'	FROM			(' .
					'						SELECT		' . $_SESSION["pronostiqueur"] . ' AS Pronostiqueurs_Pronostiqueur' .
					'									,' . $equipeChampionne . ' AS Equipe_Championne' .
					'									,' . $equipeLDC1 . ' AS Equipe_LDC1' .
					'									,' . $equipeLDC2 . ' AS Equipe_LDC2' .
					'									,' . $equipeLDC3 . ' AS Equipe_LDC3' .
					'									,' . $equipeLDC4 . ' AS Equipe_LDC4' .
					'									,' . $equipeReleguee1 . ' AS Equipe_Releguee1' .
					'									,' . $equipeReleguee2 . ' AS Equipe_Releguee2' .
					'									,' . $equipeReleguee3 . ' AS Equipe_Releguee3' .
					'									,' . $meilleurButeur . ' AS Meilleur_Buteur' .
					'									,' . $meilleurPasseur . ' AS Meilleur_Passeur' .
					'					) pronostics' .
					'	CROSS JOIN		bonus_date_max' .
					'	WHERE			NOW() <= Bonus_Date_Max';
	$bdd->exec($ordreSQL);
	echo 'Vos pronostics de bonus ont été sauvegardés avec succès';

?>