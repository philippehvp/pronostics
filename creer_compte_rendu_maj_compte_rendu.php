<?php
	include_once('commun_administrateur.php');


	// Page de sauvegarde du compte-rendu
	$compteRendu = isset($_POST["compte_rendu"]) ? $_POST["compte_rendu"] : 'Rien';

	$ordreSQL =		'	REPLACE INTO	compte_rendu_modeles(CompteRenduModele, CompteRenduModeles_Modele)' .
					'	SELECT			1, ' . $bdd->quote($compteRendu);
	$bdd->exec($ordreSQL);

?>
