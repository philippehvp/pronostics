<?php
	include_once('commun.php');


	// Suppression d'un groupe de tchat
	// Les opérations suivantes doivent être exécutées :
	// - suppression de tous les messages
	// - suppression des membres du groupe
	// - suppression du groupe
	// On vérifie avant tout que le pronostiqueur soit bien le créateur du groupe

	$tableau = array();

	// Extraction des variables postées
	extract($_POST);

	$ordreSQL =		'	SELECT		Pronostiqueurs_Pronostiqueur' .
					'	FROM		tchat_groupes' .
					'	WHERE		TchatGroupe = ' . $tchatGroupe;
	$req = $bdd->query($ordreSQL);
	$createur = $req->fetchAll();
	if(sizeof($createur) == 0) {
		$tableau["erreur"] = 'Le groupe n\'a pu être supprimé car il n\'existe pas';
		$tableau["etat"] = 'KO';
		return;
	}

	if($_SESSION["pronostiqueur"] != $createur[0]["Pronostiqueurs_Pronostiqueur"]) {
		$tableau["erreur"] = 'Le groupe n\'a pu être supprimé car il ne vous appartient pas';
		$tableau["etat"] = 'KO';
		return;
	}

	// Suppression des messages
	$ordreSQL =		'	DELETE FROM messages WHERE TchatGroupes_TchatGroupe = ' . $tchatGroupe;
	$req = $bdd->exec($ordreSQL);

	// Suppression des membres du groupe
	$ordreSQL =		'	DELETE FROM tchat_groupes_membres WHERE TchatGroupes_TchatGroupe = ' . $tchatGroupe;
	$req = $bdd->exec($ordreSQL);

	// Suppression du groupe
	$ordreSQL =		'	DELETE FROM tchat_groupes WHERE TchatGroupe = ' . $tchatGroupe;
	$req = $bdd->exec($ordreSQL);

	// Suppression des paramètres du tchat de groupe
	$ordreSQL =		'	DELETE FROM modules_pronostiqueurs WHERE Modules_Module = 50 AND TchatGroupes_TchatGroupe = ' . $tchatGroupe;
	$req = $bdd->exec($ordreSQL);

	$tableau["etat"] = 'OK';

	echo json_encode($tableau);
?>