<?php
	include_once('commun_administrateur.php');

	// Sauvegarde des informations d'un match

	// Lecture des paramètres passés à la page
	$match = isset($_POST["match"]) ? $_POST["match"] : 0;
	$action = isset($_POST["action"]) ? $_POST["action"] : 0;

	$ordreSQL = '';
	if($action == 1) {
		$scoreEquipeD = isset($_POST["scoreEquipeD"]) && $_POST["scoreEquipeD"] != -1 ? $_POST["scoreEquipeD"] : 'NULL';
		$ordreSQL = '	UPDATE matches SET matches.Matches_ScoreEquipeDomicile = ' . $scoreEquipeD . ' WHERE matches.Match = ' . $match;
	} else if($action == 2) {
		$scoreEquipeV = isset($_POST["scoreEquipeV"]) && $_POST["scoreEquipeV"] != -1 ? $_POST["scoreEquipeV"] : 'NULL';
		$ordreSQL = '	UPDATE matches SET matches.Matches_ScoreEquipeVisiteur = ' . $scoreEquipeV . ' WHERE matches.Match = ' . $match;
	} else if($action == 3) {
		$scoreAPEquipeD = isset($_POST["scoreAPEquipeD"]) && $_POST["scoreAPEquipeD"] != -1 ? $_POST["scoreAPEquipeD"] : 'NULL';
		$ordreSQL = '	UPDATE matches SET matches.Matches_ScoreAPEquipeDomicile = ' . $scoreAPEquipeD . ' WHERE matches.Match = ' . $match;
	} else if($action == 4) {
		$scoreAPEquipeV = isset($_POST["scoreAPEquipeV"]) && $_POST["scoreAPEquipeV"] != -1 ? $_POST["scoreAPEquipeV"] : 'NULL';
		$ordreSQL = '	UPDATE matches SET matches.Matches_ScoreAPEquipeVisiteur = ' . $scoreAPEquipeV . ' WHERE matches.Match = ' . $match;
	} else if($action == 5) {
		$vainqueur = isset($_POST["vainqueur"]) && $_POST["vainqueur"] != -1 ? $_POST["vainqueur"] : 'NULL';
		$ordreSQL = '	UPDATE matches SET matches.Matches_Vainqueur = ' . $vainqueur . ' WHERE matches.Match = ' . $match;
	} else if($action == 6) {
		$matchIgnore = isset($_POST["matchIgnore"]) ? $_POST["matchIgnore"] : 0;
		$ordreSQL = '	UPDATE matches SET matches.Matches_MatchIgnore = ' . $matchIgnore . ' WHERE matches.Match = ' . $match;
	} else if($action == 7) {
		$matchHorsPronostic = isset($_POST["matchHorsPronostic"]) ? $_POST["matchHorsPronostic"] : 0;
		$ordreSQL = '	UPDATE matches SET matches.Matches_MatchHorsPronostic = ' . $matchHorsPronostic . ' WHERE matches.Match = ' . $match;
	} else if($action == 8) {
		$matchDirect = isset($_POST["matchDirect"]) ? $_POST["matchDirect"] : 0;
		$ordreSQL = '	UPDATE matches SET matches.Matches_Direct = ' . $matchDirect . ' WHERE matches.Match = ' . $match;
	}

	if($ordreSQL != '') {
		echo $ordreSQL;
		$bdd->exec($ordreSQL);
	} else {
		echo 'Rien à exécuter';
	}
?>