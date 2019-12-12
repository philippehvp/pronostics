<?php
	$ordreSQL =		'	SELECT		IFNULL(Reglements_Texte, \'Vide\') AS Reglements_Texte' .
					'	FROM		reglements' .
					'	WHERE		Championnats_Championnat = 0';
	$req = $bdd->query($ordreSQL);
	$reglements = $req->fetchAll();
	$reglement = $reglements[0]["Reglements_Texte"];

	echo $reglement;
?>