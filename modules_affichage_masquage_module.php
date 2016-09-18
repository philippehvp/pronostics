<?php
	include('commun.php');
	
	// Retourne la liste des modules actifs du groupe de modules pour un pronostiqueur
	
	$parametre = isset($_POST["parametre"]) ? $_POST["parametre"] : -1;
	
	$ordreSQL =		'	SELECT		Module, Modules_Parametre' .
					'	FROM		modules' .
					'	LEFT JOIN	modules_groupes' .
					'				ON		modules.Modules_Parametre = modules_groupes.ModulesGroupes_Parametre' .
					'	LEFT JOIN	modules_pronostiqueurs' .
					'				ON		modules.Module = modules_pronostiqueurs.Modules_Module' .
					'						AND		modules_groupes.Pronostiqueurs_Pronostiqueur = modules_pronostiqueurs.Pronostiqueurs_Pronostiqueur' .
					'	WHERE		modules_pronostiqueurs.Pronostiqueurs_Pronostiqueur = ' . $_SESSION["pronostiqueur"] .
					'				AND		modules.Modules_Parametre = ' . $parametre .
					'				AND		ModulesPronostiqueurs_Actif = 1';
	$req = $bdd->query($ordreSQL);
	$modules = $req->fetchAll(PDO::FETCH_ASSOC);
	
	$tableau = array();
	$tableau['donnees'] = $modules;
	
	echo json_encode($tableau);


?>